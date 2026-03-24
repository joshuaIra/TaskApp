<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $users = User::query()
                ->select(['id', 'name', 'email', 'department', 'role', 'is_active', 'created_at'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['users' => $users]);
        }

        $users = User::orderBy('id')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['email'] = strtolower($data['email']);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'User created successfully.',
                'user' => $user->only(['id', 'name', 'email', 'department', 'role', 'is_active', 'created_at']),
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('status', 'user-created');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['email'] = strtolower($data['email']);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'User updated successfully.',
                'user' => $user->only(['id', 'name', 'email', 'department', 'role', 'is_active', 'created_at']),
            ]);
        }

        return redirect()->route('admin.users.index')->with('status', 'user-updated');
    }

    /**
     * Toggle activation state for the user.
     */
    public function toggleActive(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'User status updated successfully.',
                'user' => $user->only(['id', 'name', 'email', 'department', 'role', 'is_active', 'created_at']),
            ]);
        }

        return redirect()->route('admin.users.index')->with('status', 'user-'.($user->is_active ? 'activated' : 'deactivated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $user->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'User deleted successfully.']);
        }

        return redirect()->route('admin.users.index')->with('status', 'user-deleted');
    }
}
