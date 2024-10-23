<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:500',
                'password' => 'required|string|max:500',
            ]);

            if($validator->fails()) {
                Log::error('Ocurrió un error al intentar loguear a un usuario', [
                    'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
                ]);
                throw new InvalidRequestException('Credenciales inválidas: ' . $validator->errors()->first());
            }

            $username = $request->input('username');
            $password = $request->input('password');

            $users = json_decode(File::get(storage_path('users.json')), true);

            $user = collect($users)->firstWhere('username', $username);

            if($user && $user['password'] === $password) {
                $payload = [
                    'iss' => 'crud-productos',
                    'sub' => $user['username'],
                    'role' => $user['role'],
                    'iat' => time(),
                    'exp' => time() + (60 * 60)
                ];

                $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

                return response()->json(['token' => $jwt]);
            } else {
                Log::error('Credenciales inválidas para el usuario: ' . $username, [
                    'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
                ]);
                throw new InvalidRequestException('Credenciales inválidas');
            }
        });
    }
}
