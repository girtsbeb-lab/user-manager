<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RandomUserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::with(['contact', 'address'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('username', 'ilike', "%{$search}%");
                });
            })
            ->when($request->gender, fn($q, $g) => $q->where('gender', $g))
            ->when($request->nationality, fn($q, $n) => $q->where('nationality', $n));

        $users = $query->orderBy('last_name')->paginate(15)->withQueryString();

        return Inertia::render('Users/Index', [
            'users'         => $users,
            'filters'       => $request->only(['search', 'gender', 'nationality']),
            'nationalities' => User::distinct()->pluck('nationality')->sort()->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'username'     => 'required|string|max:100|unique:users,username',
            'gender'       => 'required|in:male,female,other',
            'date_of_birth'=> 'nullable|date',
            'age'          => 'nullable|integer|min:0|max:150',
            'nationality'  => 'nullable|string|max:10',
            'avatar'       => 'nullable|url',
            'thumbnail'    => 'nullable|url',
        ]);

        $user = User::create($validated);

        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('Users/Show', [
            'user' => $user->load(['contact', 'address']),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load(['contact', 'address']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => "required|email|unique:users,email,{$user->id}",
            'username'     => "required|string|max:100|unique:users,username,{$user->id}",
            'gender'       => 'required|in:male,female,other',
            'date_of_birth'=> 'nullable|date',
            'age'          => 'nullable|integer|min:0|max:150',
            'nationality'  => 'nullable|string|max:10',
            'avatar'       => 'nullable|url',
            'thumbnail'    => 'nullable|url',
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function import(Request $request, RandomUserService $service)
    {
        $request->validate([
            'results'  => 'nullable|integer|min:1|max:5000',
            'truncate' => 'nullable|boolean',
        ]);

        try {
            $stats = $service->import(
                $request->input('results', 50),
                $request->boolean('truncate', false)
            );
            return back()->with('success', "Import done! Imported: {$stats['imported']}, Updated: {$stats['updated']}, Errors: {$stats['errors']}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
