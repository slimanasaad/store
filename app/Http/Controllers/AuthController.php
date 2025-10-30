<?php

// app/Http/Controllers/AuthController.php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'center_id' => 'required|exists:centers,id',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'center_id' => $request->center_id,
        ]);

        $role = Role::where('name', $request->role)
                    ->where('center_id', $request->center_id)
                    ->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found for this center.'], 404);
        }

        $user->assignRole($role);

        return response()->json(['message' => 'User registered successfully.']);
    }
}
