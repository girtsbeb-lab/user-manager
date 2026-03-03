<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressController extends Controller
{
    public function edit(User $user)
    {
        return Inertia::render('Addresses/Edit', [
            'user'    => $user,
            'address' => $user->address,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'street_number'        => 'nullable|string|max:20',
            'street_name'          => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'country'              => 'nullable|string|max:100',
            'postcode'             => 'nullable|string|max:20',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'timezone_offset'      => 'nullable|string|max:10',
            'timezone_description' => 'nullable|string|max:255',
        ]);

        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('users.show', $user)->with('success', 'Address updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->address?->delete();
        return redirect()->route('users.show', $user)->with('success', 'Address deleted.');
    }
}
