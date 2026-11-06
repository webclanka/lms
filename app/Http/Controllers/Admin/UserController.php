<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private array $allowedRoles = ['super-admin', 'admin', 'instructor', 'student', 'assistant', 'auditor'];

    public function index(Request $request)
    {
        $allowedSorts = ['name', 'email', 'created_at'];
        $allowedPerPage = [10, 15, 25, 50];

        $search = trim((string) $request->query('search', ''));

        $requestedRole = (string) $request->query('role', '');
        $role = in_array($requestedRole, $this->allowedRoles, true) ? $requestedRole : '';

        $requestedSort = (string) $request->query('sort', 'created_at');
        $sort = in_array($requestedSort, $allowedSorts, true) ? $requestedSort : 'created_at';

        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $requestedPerPage = (int) $request->query('per_page', 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;

        $users = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== '', function ($query) use ($role) {
                $query->whereHas('roles', fn($query) => $query->where('name', $role));
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString()
            ->through(
                fn(User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                    'created_at' => $user->created_at?->format('M j, Y'),
                ],
            );

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => Role::query()->whereIn('name', $this->allowedRoles)->orderBy('name')->pluck('name'),
            'filters' => [
                'search' => $search,
                'role' => $role,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
            'summary' => [
                'total_users' => User::query()->count(),
                'super_admins' => User::whereHas('roles', fn($query) => $query->where('name', 'super-admin'))->count(),
                'admins' => User::whereHas('roles', fn($query) => $query->where('name', 'admin'))->count(),
                'instructors' => User::whereHas('roles', fn($query) => $query->where('name', 'instructor'))->count(),
                'students' => User::whereHas('roles', fn($query) => $query->where('name', 'student'))->count(),
                'assistants' => User::whereHas('roles', fn($query) => $query->where('name', 'assistant'))->count(),
                'auditors' => User::whereHas('roles', fn($query) => $query->where('name', 'auditor'))->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Form', [
            'user' => null,
            'roles' => $this->roles(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in($this->allowedRoles)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if ($request->hasFile('profile_photo')) {
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');

            $user->save();
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Form', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'profile_photo_url' => $user->profile_photo_path ? Storage::disk('public')->url($user->profile_photo_path) : null,
            ],
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in($this->allowedRoles)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ((int) $request->user()->id === (int) $user->id && $data['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot remove your own admin role.',
            ]);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $newPath = $request->file('profile_photo')->store('profile-photos', 'public');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $newPath;
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index');
    }

    public function show(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return redirect()->route('profile.show');
        }

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'created_at' => $user->created_at?->format('M j, Y'),
                'updated_at' => $user->updated_at?->format('M j, Y'),
                'profile_photo_url' => $user->profile_photo_path ? Storage::disk('public')->url($user->profile_photo_path) : null,
            ],
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors([
                'user' => 'You cannot delete your own account here.',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index');
    }

    private function roles(): array
    {
        return Role::query()->whereIn('name', $this->allowedRoles)->orderBy('name')->pluck('name')->all();
    }

    public function suggestions(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'role' => ['nullable', Rule::in($this->allowedRoles)],
        ]);

        $search = trim($validated['q']);
        $role = $validated['role'] ?? '';

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->where('name', 'like', "%{$search}%")
            ->when($role !== '', function ($query) use ($role) {
                $query->whereHas('roles', fn($query) => $query->where('name', $role));
            })
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json($users);
    }
}
