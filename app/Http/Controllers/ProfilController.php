<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Tampilkan Profil Pengguna yang Sedang Login
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('profil.index', compact('user'));
    }

    /**
     * Tampilkan Formulir Profil (Kompatibilitas Breeze)
     */
    public function edit(Request $request): View
    {
        return $this->index();
    }

    /**
     * Perbarui Profil Pengguna yang Sedang Login
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;

        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $targetRoute = $request->routeIs('profile.*') ? 'profile.edit' : 'profil.index';

        return redirect()->route($targetRoute)->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Hapus Akun Pengguna Sendiri
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Akun Anda berhasil dihapus.');
    }
}
