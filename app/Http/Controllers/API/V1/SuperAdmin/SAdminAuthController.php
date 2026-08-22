<?php
namespace App\Http\Controllers\API\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SAdminAuthController extends Controller
{

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'role']),
        ]);
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if ($user->role !== 'root') {
            return response()->json([
                'message' => 'You are not authorized to access the super admin panel.',
            ], 403);
        }

        // Optional: wipe old tokens so only one active session exists
        // $user->tokens()->delete();

        $token = $user->createToken('super-admin-token', ['super-admin'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}