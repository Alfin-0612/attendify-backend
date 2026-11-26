<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with('kelas')->where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['pesan' => 'Username atau password salah.'], 401);
        }

        return response()->json([
            'token' => $user->createToken('flutter')->plainTextToken,
            'user' => $this->format($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['pesan' => 'Berhasil keluar.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->format($request->user()->load('kelas')));
    }

    private function format(User $user): array
    {
        return [
            'nama' => $user->name,
            'username' => $user->username,
            'nisn' => $user->nisn,
            'kelas' => $user->kelas?->nama,
            'tahun_ajaran' => $user->tahun_ajaran,
        ];
    }
}
