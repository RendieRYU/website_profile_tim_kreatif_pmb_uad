<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AccountController extends Controller
{
    public function edit(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $memberAccounts = null;

        // Admin can manage all anggota accounts
        if ($user->role === 'admin') {
            $memberAccounts = User::where('role', 'anggota')->orderBy('username')->get();
        }

        return view('admin.account.edit', compact('user', 'memberAccounts'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:3'],
        ]);

        $user->username = $validated['username'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('admin.account.edit')->with('success', 'Akun berhasil diperbarui.');
    }

    public function updateMemberAccount(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $memberUser = User::where('role', 'anggota')->findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$memberUser->id],
            'password' => ['nullable', 'min:3'],
        ]);

        $memberUser->username = $validated['username'];
        if (!empty($validated['password'])) {
            $memberUser->password = Hash::make($validated['password']);
        }
        $memberUser->save();

        return redirect()->route('admin.account.edit')->with('success', 'Akun anggota "' . $memberUser->username . '" berhasil diperbarui.');
    }
}
