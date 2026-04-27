<?php

namespace App\Http\Controllers;

use App\Models\DocumentRoute;
use App\Models\RouteStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutingController extends Controller
{
    public function index()
    {
        return view('routing');
    }

    public function list(Request $request)
    {
        $query = DocumentRoute::with(['user', 'stages'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereRaw('CONCAT("RT-", LPAD(id, 3, "0")) LIKE ?', ['%' . $search . '%']);
            });
        }

        $routes = $query->paginate(5)->withQueryString();

        $data = $routes->map(function ($route) {
            $lastStage = $route->stages->last();
            return [
                'id'       => $route->formattedId(),
                'doc'      => $route->title,
                'origin'   => $route->origin_department,
                'waypoint' => $route->current_waypoint ?? ($lastStage ? $lastStage->waypoint_department : $route->origin_department),
                'status'   => $route->status,
                'priority' => $route->priority,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $routes->currentPage(),
            'last_page' => $routes->lastPage(),
            'total' => $routes->total(),
            'per_page' => $routes->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'priority'    => 'required|in:low,medium,high',
            'stages'      => 'required|array|min:1',
            'stages.*.origin'   => 'required|string',
            'stages.*.waypoint' => 'required|string',
        ]);

        $user = auth()->user();

        $route = DB::transaction(function () use ($data, $user) {
            $documentRoute = DocumentRoute::create([
                'user_id'            => $user->id,
                'title'              => $data['title'],
                'priority'           => $data['priority'],
                'status'             => 'pending',
                'origin_department'  => $data['stages'][0]['origin'],
                'current_waypoint'   => $data['stages'][count($data['stages']) - 1]['waypoint'] ?? null,
            ]);

            foreach ($data['stages'] as $index => $stage) {
                RouteStage::create([
                    'document_route_id'  => $documentRoute->id,
                    'stage_order'        => $index + 1,
                    'origin_department'  => $stage['origin'],
                    'waypoint_department'=> $stage['waypoint'],
                    'handler_id'         => null,
                    'status'             => $index === 0 ? 'active' : 'pending',
                    'duration'           => null,
                ]);
            }

            return $documentRoute;
        });

        return response()->json([
            'message' => 'Route created successfully.',
            'route'   => [
                'id'       => $route->formattedId(),
                'doc'      => $route->title,
                'origin'   => $route->origin_department,
                'waypoint' => $route->current_waypoint,
                'status'   => $route->status,
                'priority' => $route->priority,
            ],
        ]);
    }

    public function detail($routeId)
    {
        $numericId = (int) str_replace('RT-', '', $routeId);
        $documentRoute = DocumentRoute::with(['user', 'stages.handler'])->findOrFail($numericId);

        $owner = $documentRoute->user;
        $ownerName = $owner ? ($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
        $submitted = $documentRoute->created_at->format('F j, Y');
        $originAbbr = $this->abbr($documentRoute->origin_department);

        $paths = $documentRoute->stages->map(function ($stage) {
            $handler = $stage->handler;
            $handlerName = $handler ? ($handler->first_name . ' ' . $handler->last_name) : 'Unassigned';
            return [
                'from'     => $stage->origin_department,
                'to'       => $stage->waypoint_department,
                'handler'  => $handlerName,
                'initials' => $this->initials($handlerName),
                'status'   => $stage->status,
                'duration' => $stage->duration ?? '-',
            ];
        });

        $currentStage = $documentRoute->stages->firstWhere('status', 'active')
            ?? $documentRoute->stages->firstWhere('status', 'pending')
            ?? $documentRoute->stages->last();

        $currentHandler = $currentStage && $currentStage->handler
            ? ($currentStage->handler->first_name . ' ' . $currentStage->handler->last_name)
            : 'Unassigned';

        return response()->json([
            'owner'          => $ownerName,
            'submitted'      => $submitted,
            'originAbbr'     => $originAbbr,
            'paths'          => $paths,
            'currentHandler' => $currentHandler,
            'currentInitials'=> $this->initials($currentHandler),
            'status'         => $documentRoute->status,
            'priority'       => $documentRoute->priority,
            'title'          => $documentRoute->title,
            'id'             => $documentRoute->formattedId(),
        ]);
    }

    public function updateStatus(Request $request, $routeId)
    {
        $numericId = (int) str_replace('RT-', '', $routeId);
        $documentRoute = DocumentRoute::with('stages')->findOrFail($numericId);

        $data = $request->validate([
            'action' => 'required|in:received,returned,flag',
        ]);

        $action = $data['action'];

        DB::transaction(function () use ($documentRoute, $action) {
            if ($action === 'received') {
                $activeStage = $documentRoute->stages()->where('status', 'active')->first();
                if ($activeStage) {
                    $activeStage->update(['status' => 'completed']);
                    $nextStage = $documentRoute->stages()
                        ->where('stage_order', '>', $activeStage->stage_order)
                        ->where('status', 'pending')
                        ->first();
                    if ($nextStage) {
                        $nextStage->update(['status' => 'active']);
                        $documentRoute->update([
                            'current_waypoint' => $nextStage->waypoint_department,
                            'status' => 'on-time',
                        ]);
                    } else {
                        $documentRoute->update([
                            'status' => 'completed',
                            'current_waypoint' => $activeStage->waypoint_department,
                        ]);
                    }
                }
            } elseif ($action === 'returned') {
                $documentRoute->update(['status' => 'delayed']);
            } elseif ($action === 'flag') {
                $documentRoute->update(['status' => 'delayed']);
            }
        });

        return response()->json(['message' => 'Status updated.', 'status' => $documentRoute->fresh()->status]);
    }

    private function abbr(string $text): string
    {
        $words = explode(' ', $text);
        $abbr = '';
        foreach ($words as $word) {
            $abbr .= strtoupper($word[0] ?? '');
        }
        return substr($abbr, 0, 3);
    }

    private function initials(string $name): string
    {
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            $initials .= strtoupper($part[0] ?? '');
        }
        return substr($initials, 0, 2);
    }
}

