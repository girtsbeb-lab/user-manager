<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function edit(User $user)
    {
        return Inertia::render('Contacts/Edit', [
            'user'    => $user,
            'contact' => $user->contact,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:50',
            'cell'  => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $user->contact()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('users.show', $user)->with('success', 'Contact updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->contact?->delete();
        return redirect()->route('users.show', $user)->with('success', 'Contact deleted.');
    }
}
