<?php

namespace App\Http\Controllers;

use App\Models\DocumentRoute;
use App\Models\RouteStage;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutingController extends Controller
{
    public function index()
    {
        $priorities = Setting::getGroup('priorities');
        return view('routing', compact('priorities'));
    }

    public function getDepartments()
    {
        $departments = Setting::getGroup('departments');
        return response()->json(['departments' => $departments]);
    }

    public function list(Request $request)
    {
        $query = DocumentRoute::with(['user', 'stages'])->latest();

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
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
            'data'         => $data,
            'current_page' => $routes->currentPage(),
            'last_page'    => $routes->lastPage(),
            'total'        => $routes->total(),
            'per_page'     => $routes->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'priority'            => 'required|in:low,medium,high',
            'deadline'            => 'nullable|date|after:now',
            'stages'              => 'required|array|min:1',
            'stages.*.origin'     => 'required|string',
            'stages.*.waypoint'   => 'required|string',
            'stages.*.handler_id' => 'nullable|exists:users,id',
        ]);

        $user = auth()->user();

        $route = DB::transaction(function () use ($data, $user) {
            $documentRoute = DocumentRoute::create([
                'user_id'           => $user->id,
                'title'             => $data['title'],
                'priority'          => $data['priority'],
                'status'            => 'pending',
                'origin_department' => $data['stages'][0]['origin'],
                'current_waypoint'  => $data['stages'][count($data['stages']) - 1]['waypoint'] ?? null,
                'deadline'          => $data['deadline'] ?? null,
            ]);

            foreach ($data['stages'] as $index => $stage) {
                RouteStage::create([
                    'document_route_id'   => $documentRoute->id,
                    'stage_order'         => $index + 1,
                    'origin_department'   => $stage['origin'],
                    'waypoint_department' => $stage['waypoint'],
                    'handler_id'          => $stage['handler_id'] ?? null,
                    'status'              => $index === 0 ? 'active' : 'pending',
                    'duration'            => null,
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
        $numericId     = (int) str_replace('RT-', '', $routeId);
        $documentRoute = DocumentRoute::with(['user', 'stages.handler'])->findOrFail($numericId);

        // Auto-mark delayed if past deadline and not yet completed/returned
        if ($documentRoute->deadline && now()->gt($documentRoute->deadline)
            && !in_array($documentRoute->status, ['completed', 'delayed', 'returned'])) {
            $documentRoute->update(['status' => 'delayed']);
        }

        $owner     = $documentRoute->user;
        $ownerName = $owner ? ($owner->first_name . ' ' . $owner->last_name) : 'Unknown';
        $submitted  = $documentRoute->created_at->format('F j, Y');
        $originAbbr = $this->abbr($documentRoute->origin_department);

        $paths = $documentRoute->stages->map(function ($stage) {
            $handler     = $stage->handler;
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

        $activeStage = $documentRoute->stages->firstWhere('status', 'active')
            ?? $documentRoute->stages->firstWhere('status', 'pending')
            ?? $documentRoute->stages->last();

        $currentHandler = $activeStage && $activeStage->handler
            ? ($activeStage->handler->first_name . ' ' . $activeStage->handler->last_name)
            : 'Unassigned';

        $fresh = $documentRoute->fresh();

        return response()->json([
            'owner'                  => $ownerName,
            'submitted'              => $submitted,
            'originAbbr'             => $originAbbr,
            'paths'                  => $paths,
            'currentHandler'         => $currentHandler,
            'currentInitials'        => $this->initials($currentHandler),
            'status'                 => $fresh->status,
            'priority'               => $documentRoute->priority,
            'title'                  => $documentRoute->title,
            'id'                     => $documentRoute->formattedId(),
            'activeWaypoint'         => $activeStage ? $activeStage->waypoint_department : null,
            'activeHandlerId'        => $activeStage ? $activeStage->handler_id : null,
            'deadline'               => $documentRoute->deadline?->format('F j, Y g:i A'),
            'remarks'                => $fresh->remarks,
            'returnedByDepartment'   => $fresh->returned_by_department,
            'originDept'             => $documentRoute->origin_department,
        ]);
    }

    public function updateStatus(Request $request, $routeId)
    {
        $numericId     = (int) str_replace('RT-', '', $routeId);
        $documentRoute = DocumentRoute::with('stages')->findOrFail($numericId);

        $data = $request->validate([
            'action'  => 'required|in:received,returned,flag',
            'remarks' => 'nullable|string|max:500',
        ]);

        $activeStage = $documentRoute->stages()->where('status', 'active')->first()
            ?? $documentRoute->stages()->where('status', 'pending')->first();

        if (!$activeStage) {
            return response()->json(['message' => 'No active stage found.'], 422);
        }

        // Auto-mark delayed if past deadline
        if ($documentRoute->deadline && now()->gt($documentRoute->deadline)
            && !in_array($documentRoute->status, ['completed', 'delayed', 'returned'])) {
            $documentRoute->update(['status' => 'delayed']);
            return response()->json(['message' => 'This route has passed its deadline and has been marked as delayed.'], 422);
        }

        // Authorisation
        $user               = auth()->user();
        $hasSpecificHandler = !is_null($activeStage->handler_id);
        $authorised         = $hasSpecificHandler
            ? $user->id === $activeStage->handler_id
            : $user->department === $activeStage->waypoint_department;

        if (!$authorised) {
            return response()->json(['message' => 'You are not authorised to act on this route stage.'], 403);
        }

        $action = $data['action'];

        DB::transaction(function () use ($documentRoute, $action, $data, $activeStage, $user) {
            if ($action === 'received') {
                $activeStage->update(['status' => 'completed']);
                $nextStage = $documentRoute->stages()
                    ->where('stage_order', '>', $activeStage->stage_order)
                    ->where('status', 'pending')
                    ->first();
                if ($nextStage) {
                    $nextStage->update(['status' => 'active']);
                    $documentRoute->update([
                        'current_waypoint'      => $nextStage->waypoint_department,
                        'status'                => 'on-time',
                        'remarks'               => null,
                        'returned_by_department'=> null,
                    ]);
                } else {
                    $documentRoute->update([
                        'status'                => 'completed',
                        'current_waypoint'      => $activeStage->waypoint_department,
                        'remarks'               => null,
                        'returned_by_department'=> null,
                    ]);
                }
            } elseif ($action === 'returned') {
                $documentRoute->update([
                    'status'                => 'returned',
                    'remarks'               => $data['remarks'] ?? null,
                    'returned_by_department'=> $user->department,
                ]);
            } elseif ($action === 'flag') {
                $documentRoute->update(['status' => 'delayed']);
            }
        });

        return response()->json(['message' => 'Status updated.', 'status' => $documentRoute->fresh()->status]);
    }

    public function republish(Request $request, $routeId)
    {
        $numericId     = (int) str_replace('RT-', '', $routeId);
        $documentRoute = DocumentRoute::with('stages')->findOrFail($numericId);

        if ($documentRoute->status !== 'returned') {
            return response()->json(['message' => 'Only returned routes can be republished.'], 422);
        }

        // Only the origin department (the department that owns the route) can republish
        if (auth()->user()->department !== $documentRoute->origin_department) {
            return response()->json(['message' => 'Only the origin department can republish this route.'], 403);
        }

        DB::transaction(function () use ($documentRoute) {
            // Reset all stages: first becomes active, rest become pending
            $documentRoute->stages()->orderBy('stage_order')->each(function ($stage, $index) {
                $stage->update(['status' => $index === 0 ? 'active' : 'pending']);
            });

            $firstStage = $documentRoute->stages()->orderBy('stage_order')->first();

            $documentRoute->update([
                'status'                => 'on-time',
                'remarks'               => null,
                'returned_by_department'=> null,
                'current_waypoint'      => $firstStage ? $firstStage->waypoint_department : $documentRoute->current_waypoint,
            ]);
        });

        return response()->json(['message' => 'Route republished successfully.']);
    }

    private function abbr(string $text): string
    {
        $words = explode(' ', $text);
        $abbr  = '';
        foreach ($words as $word) {
            $abbr .= strtoupper($word[0] ?? '');
        }
        return substr($abbr, 0, 3);
    }

    private function initials(string $name): string
    {
        $parts    = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            $initials .= strtoupper($part[0] ?? '');
        }
        return substr($initials, 0, 2);
    }
}
