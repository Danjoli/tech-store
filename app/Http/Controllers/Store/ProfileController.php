<?php

namespace App\Http\Controllers\Store;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Store/Profile/Show', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified_at,
                'favorites_count' => $user->favoriteProducts()->count(),
            ],
        ]);
    }

    public function update(
        UpdateProfileRequest $request,
        UpdateUserProfileInformation $updateProfile,
    ): RedirectResponse {
        $user = $request->user();
        $validated = $request->validated();

        $updateProfile->update($user, [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->update(['phone' => $validated['phone'] ?? null]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }
}
