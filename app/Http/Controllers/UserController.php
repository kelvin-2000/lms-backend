<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        // Only admins can list all users
        $user = Auth::user();
        if (!in_array($user->role, ['admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to list users'
            ], 403);
        }
        
        $query = User::query();
        
        // Filter by role if requested
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        
        // Paginate the results
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Users can view their own profile, admins can view any profile
        $currentUser = Auth::user();
        if ($currentUser->id !== $user->id && !in_array($currentUser->role, ['admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this user'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user = null)
    {
        // If no user is provided, update the authenticated user
        if (!$user) {
            $user = Auth::user();
        } else {
            // Only admins can update other users
            $currentUser = Auth::user();
            if ($currentUser->id !== $user->id && !in_array($currentUser->role, ['admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this user'
                ], 403);
            }
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'avatar' => 'sometimes|nullable|string',
            'bio' => 'sometimes|nullable|string',
            'role' => 'sometimes|required|string|in:student,instructor,admin',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Hash password if it's being updated
        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        
        // Update the user
        $user->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Only admins can delete users
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete users'
            ], 403);
        }
        
        // Prevent admins from deleting themselves
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 400);
        }
        
        // Delete the user
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
} 