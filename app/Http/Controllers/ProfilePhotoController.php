<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profilePhotoPath = $validated['foto']->store('profile-photos', 'public');

        if (! $profilePhotoPath) {
            return back()->withErrors([
                'foto' => 'Não foi possível salvar a foto. Tente novamente.',
            ]);
        }

        $user = $request->user();
        $previousPhotoPath = $user->profile_photo_path;

        $user->update([
            'profile_photo_path' => $profilePhotoPath,
        ]);

        if ($previousPhotoPath) {
            Storage::disk('public')->delete($previousPhotoPath);
        }

        return back()->with('status', 'Foto do perfil atualizada.');
    }
}
