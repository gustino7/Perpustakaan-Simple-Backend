<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => "required|string|max:30",
            "email" => "required|string|unique:users|email",
            "password" => "required|string|min:8"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Gagal registrasi",
                "error" => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = $user->createToken('register_token')->plainTextToken;
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Berhasil registrasi',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Gagal registrasi',
                'error'=> $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function login(Request $request)
    {
        $credential = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        $validator = Validator::make($request->all(), [
            "email" => "required|string|email",
            "password" => "required|string|min:8"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Gagal login",
                "error" => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!Auth::attempt($credential)) {
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Email atau password salah',
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        $token = $user->createToken('login_token')->plainTextToken;
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil login',
            'data' => $request->user(),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            "status" => Response::HTTP_OK,
            "message" => "Berhasil logout",
        ], Response::HTTP_OK);
    }
}
