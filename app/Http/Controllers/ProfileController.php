<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->role === \App\Enums\UserRole::Admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('profile.show', [
            'user' => $user,
            'orderCount' => $user->orders()->count(),
            'addressCount' => $user->addresses()->count(),
            'cartCount' => $user->cartItems()->count(),
            'affiliate' => $user->affiliateProfile,
            'sellerProfile' => $user->sellerProfile,
            'businessProfile' => $user->businessProfile,
            'commissionTotal' => $user->affiliateProfile
                ? (float) $user->affiliateProfile->commissions()
                    ->where('status', \App\Enums\CommissionStatus::Approved->value)
                    ->sum('amount')
                : 0,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
