<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
         return response()->json($responseData)->setStatusCode(201, 'User registered successfully');die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->account_type ?? 'user', // Default to 'user' if not provided
        ]);

        // Initialize lawyer variable
        $lawyer = null;
        
        // Check if user type is business, then create lawyer entry
        if ($request->account_type === 'business') {
            // Create a lawyer record with basic information
            $lawyer = new Lawyer();
            $lawyer->full_name = $request->name;
            $lawyer->email = $request->email;
            $lawyer->password_hash = Hash::make($request->password);
            $lawyer->active = true;
            $lawyer->is_verified = false;
            
            // Generate a unique license number if not provided
            $lawyer->license_number = $request->license_number ?? 'TMP-' . Str::random(8);
            
            // Set other fields with default values or from request if available
            $lawyer->specialization = $request->specialization ?? null;
            $lawyer->years_of_experience = $request->years_of_experience ?? 0;
            $lawyer->bio = $request->bio ?? null;
            $lawyer->consultation_fee = $request->consultation_fee ?? 0.00;
            
            // Save the lawyer record (UUID will be auto-generated in the model's boot method)
            $lawyer->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Prepare response data
        $responseData = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'User registered successfully',
            'user' => $user
        ];
        
        // Add lawyer data if business account
        if ($lawyer) {
            $responseData['lawyer'] = [
                'uuid' => $lawyer->id,
                'full_name' => $lawyer->full_name,
                'email' => $lawyer->email,
                'license_number' => $lawyer->license_number
            ];
        }

        return response()->json($responseData)->setStatusCode(201, 'User registered successfully');
    }

    // Login
    public function login(Request $request)
    { 
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hi '.$user->name.', welcome back',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
