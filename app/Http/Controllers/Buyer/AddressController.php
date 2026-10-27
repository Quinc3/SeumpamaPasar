<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();
        return view('buyer.addresses.index', compact('addresses'));
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (!empty($data['is_default']) || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        return redirect()->route('addresses.index')->with('status', 'address-added');
    }

    public function setDefault(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('status', 'address-default');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->orderByDesc('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('status', 'address-deleted');
    }
}