<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function activate(Request $request): RedirectResponse
    {
        $request->user()->activateMember();

        return redirect()
        ->route('profile.show')
        ->with('status', 'member-activated');
    }
}