<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\SearchUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Search and paginate user accounts with filtering by role and status.
     */
    public function index(SearchUserRequest $request): JsonResponse
    {
        $query = User::with(['roles.permissions']);

        if ($search = $request->validated('search')) {
            $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('full_name', $like, "%{$search}%")
                    ->orWhere('email', $like, "%{$search}%")
                    ->orWhere('phone', $like, "%{$search}%");
            });
        }

        if ($role = $request->validated('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($status = $request->validated('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->integer('per_page', 15);
        $users = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Create a new user account with assigned roles.
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'status' => $validated['status'] ?? 'active',
                'school_id' => $validated['school_id'] ?? null,
            ]);

            $roles = $validated['roles'] ?? ['parent'];
            $user->syncRoles($roles);

            return $user;
        });

        $user->load(['roles.permissions']);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * Retrieve single user details and role permissions.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles.permissions', 'auditLogs' => fn ($q) => $q->latest()->limit(10)]);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Update user profile, status, or role assignments.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $updateData = [];

            if (array_key_exists('full_name', $validated)) {
                $updateData['full_name'] = $validated['full_name'];
            }
            if (array_key_exists('email', $validated)) {
                $updateData['email'] = $validated['email'];
            }
            if (array_key_exists('phone', $validated)) {
                $updateData['phone'] = $validated['phone'];
            }
            if (! empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }
            if (array_key_exists('status', $validated)) {
                $updateData['status'] = $validated['status'];
            }
            if (array_key_exists('school_id', $validated)) {
                $updateData['school_id'] = $validated['school_id'];
            }

            if (! empty($updateData)) {
                $user->update($updateData);
            }

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }
        });

        $user->load(['roles.permissions']);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Deactivate or soft-delete a user account.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], Response::HTTP_OK);
    }
}
