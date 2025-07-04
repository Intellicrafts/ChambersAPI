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
        // Log the request for debugging
        \Log::info('Registration attempt', [
            'email' => $request->email,
            'name' => $request->name,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            \Log::warning('Registration failed: Validation errors', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->account_type ?? 'user', // Default to 'user' if not provided
            ]);

            // Initialize lawyer variable
            $lawyer = null;
            
            // Check if user type is business, then create lawyer entry
            if ($request->account_type === '2' || $request->account_type === 'business') {
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
            
            \Log::info('Registration successful', ['user_id' => $user->id, 'email' => $user->email]);

            // Prepare response data
            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'User registered successfully',
                'user' => $user
            ];
            
            // Add lawyer data if business account
            if ($lawyer) {
                $response['lawyer'] = [
                    'uuid' => $lawyer->id,
                    'full_name' => $lawyer->full_name,
                    'email' => $lawyer->email,
                    'license_number' => $lawyer->license_number
                ];
            }

            return response()->json($response)
                ->setStatusCode(201, 'User registered successfully')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Login
    public function login(Request $request)
    { 
        // Log the request for debugging
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all()
        ]);
        
        if (!Auth::attempt($request->only('email', 'password'))) {
            \Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        
        // Revoke any existing tokens
        $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        \Log::info('Login successful', ['user_id' => $user->id, 'email' => $user->email]);

        return response()->json([
            'message' => 'Hi '.$user->name.', welcome back',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    // Logout
    public function logout(Request $request)
    {
        try {
            // Log the request for debugging
            \Log::info('Logout attempt', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'headers' => $request->headers->all()
            ]);
            
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
                \Log::info('Logout successful', ['user_id' => $request->user()->id]);
            } else {
                \Log::warning('Logout attempted without authenticated user');
            }

            return response()->json([
                'message' => 'Successfully logged out'
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\Exception $e) {
            \Log::error('Logout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
