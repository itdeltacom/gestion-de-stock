<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Vérifier si l'utilisateur est actif
            if (!Auth::user()->is_active) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.'
                ], 403);
            }

            // Enregistrer l'activité de connexion
            activity()
                ->causedBy(Auth::user())
                ->log('Connexion réussie');

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie! Bienvenue ' . Auth::user()->name,
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect.'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Enregistrer l'activité de déconnexion avant de se déconnecter
        activity()
            ->causedBy(Auth::user())
            ->log('Déconnexion');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('roles.permissions');

        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        try {
            // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le mot de passe actuel est incorrect.'
                    ], 400);
                }
                $validated['password'] = Hash::make($request->new_password);
            }

            // Supprimer les champs de mot de passe de la validation si non utilisés
            unset($validated['current_password'], $validated['new_password'], $validated['new_password_confirmation']);

            $user->update($validated);

            // Enregistrer l'activité
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->log('Profil mis à jour');

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect.'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Enregistrer l'activité
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->log('Mot de passe modifié');

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
}