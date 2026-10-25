<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();
        $role = $request->string('role')->trim()->value();
        $isDev = (bool) $request->user()?->isDevAdmin();

        $query = User::query()
            ->when(! $isDev, function ($q) {
                $q->where('role', '!=', 'dev')
                    ->where('is_system_account', false);
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($q, $role) {
                $q->where('role', $role);
            })
            ->orderByDesc('created_at');

        $users = $query->paginate(15)->withQueryString()->through(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? ($user->is_admin ? 'admin' : 'editor'),
            'is_admin' => (bool) $user->is_admin,
            'is_system_account' => (bool) $user->is_system_account,
            'has_two_factor' => (bool) $user->two_factor_confirmed_at,
            'email_verified' => (bool) $user->email_verified_at,
            'created_at' => $user->created_at?->format('d M Y, H:i'),
        ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
            'roles' => [
                ...($isDev ? [['value' => 'dev', 'label' => 'Developer']] : []),
                ['value' => 'admin', 'label' => 'Administrator'],
                ['value' => 'editor', 'label' => 'Content Editor'],
                ['value' => 'coach', 'label' => 'Coach'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Request $request): Response
    {
        $isDev = (bool) $request->user()?->isDevAdmin();

        $roles = [
            ...($isDev ? [['value' => 'dev', 'label' => 'Developer', 'description' => 'Super Administrator with developer, log and system access']] : []),
            ['value' => 'admin', 'label' => 'Administrator', 'description' => 'Full administrative access to club activities, content, and users'],
            ['value' => 'editor', 'label' => 'Content Editor', 'description' => 'Can manage articles, galleries, and pages'],
            ['value' => 'coach', 'label' => 'Coach', 'description' => 'Can view activities, sessions, and bookings'],
        ];

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $isDev = (bool) $request->user()?->isDevAdmin();
        $allowedRoles = $isDev ? ['dev', 'admin', 'editor', 'coach'] : ['admin', 'editor', 'coach'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_admin' => in_array($validated['role'], ['dev', 'admin'], true),
            'is_system_account' => $validated['role'] === 'dev',
            'email_verified_at' => now(),
        ]);

        if (function_exists('activity')) {
            activity()
                ->performedOn($user)
                ->causedBy($request->user())
                ->withProperties(['role' => $user->role])
                ->log("Created user account: {$user->email}");
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, User $user): Response
    {
        $isDev = (bool) $request->user()?->isDevAdmin();

        if (($user->is_system_account || $user->role === 'dev') && ! $isDev) {
            abort(403, 'Unauthorized. Developer accounts can only be managed by developer administrators.');
        }

        $roles = [
            ...($isDev ? [['value' => 'dev', 'label' => 'Developer', 'description' => 'Super Administrator with developer, log and system access']] : []),
            ['value' => 'admin', 'label' => 'Administrator', 'description' => 'Full administrative access to club activities, content, and users'],
            ['value' => 'editor', 'label' => 'Content Editor', 'description' => 'Can manage articles, galleries, and pages'],
            ['value' => 'coach', 'label' => 'Coach', 'description' => 'Can view activities, sessions, and bookings'],
        ];

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? ($user->is_admin ? 'admin' : 'editor'),
                'is_admin' => (bool) $user->is_admin,
                'is_system_account' => (bool) $user->is_system_account,
                'has_two_factor' => (bool) $user->two_factor_confirmed_at,
                'created_at' => $user->created_at?->format('d M Y, H:i'),
            ],
            'roles' => $roles,
            'isSelf' => $user->id === auth()->id(),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $isDev = (bool) $request->user()?->isDevAdmin();

        if (($user->is_system_account || $user->role === 'dev') && ! $isDev) {
            abort(403, 'Unauthorized. Developer accounts can only be managed by developer administrators.');
        }

        $allowedRoles = $isDev ? ['dev', 'admin', 'editor', 'coach'] : ['admin', 'editor', 'coach'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === $request->user()?->id && ! in_array($validated['role'], ['dev', 'admin'], true)) {
            return back()->withErrors(['role' => 'You cannot change your own role from Administrator.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_admin = in_array($validated['role'], ['dev', 'admin'], true);

        if ($validated['role'] === 'dev') {
            $user->is_system_account = true;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (function_exists('activity')) {
            activity()
                ->performedOn($user)
                ->causedBy($request->user())
                ->withProperties(['role' => $user->role])
                ->log("Updated user account: {$user->email}");
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()?->id) {
            return back()
                ->with('error', 'You cannot delete your own account.')
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if (($user->is_system_account || $user->role === 'dev') && ! $request->user()?->isDevAdmin()) {
            return back()
                ->with('error', 'Developer and system accounts are protected and cannot be deleted.')
                ->withErrors(['error' => 'Developer and system accounts are protected and cannot be deleted.']);
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        if (function_exists('activity')) {
            activity()
                ->causedBy($request->user())
                ->log("Deleted user account: {$userEmail} ({$userName})");
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', "User '{$userName}' has been deleted.");
    }
}
