<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileAvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        $request->user()->update(['avatar' => $path]);

        return back();
    }
}
