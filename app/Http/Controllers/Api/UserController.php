<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Get all users.
     */
    public function index(Request $request)
    {
        try {
            // Only admin can list all users
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para listar usuarios.',
                ], Response::HTTP_FORBIDDEN);
            }

            $query = User::query();

            // Filter by role
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $users = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($users->items()),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific user.
     */
    public function show(User $user, Request $request)
    {
        try {
            // Only admin or the user itself can view
            if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para ver este usuario.',
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a user.
     */
    public function update(User $user, Request $request)
    {
        try {
            // Only admin or the user itself can update
            if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para actualizar este usuario.',
                ], Response::HTTP_FORBIDDEN);
            }

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:8|confirmed',
                'role' => 'sometimes|in:admin,analyst,viewer',
                'is_active' => 'sometimes|boolean',
            ]);

            $data = $request->validated();

            // Only admin can change role or is_active
            if (!$request->user()->isAdmin()) {
                unset($data['role'], $data['is_active']);
            }

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente.',
                'data' => new UserResource($user),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user, Request $request)
    {
        try {
            // Only admin can delete
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar usuarios.',
                ], Response::HTTP_FORBIDDEN);
            }

            // Prevent deleting the last admin
            if ($user->isAdmin()) {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puede eliminar el último administrador.',
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
