<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => [
                'user' => fn () => $request->user()?->only([
                    'id',
                    'name',
                    'email',
                    'role',
                    'email_verified_at',
                ]),
            ],

            'status' => fn () => $request->session()->get('status'),

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],

            'wishlistCount' => fn (): int => $request->user()
                ? $request->user()->favoriteProducts()->count()
                : 0,

            'cartCount' => fn (): int => (int) (
                $request->user()?->cart()
                    ->withSum('items', 'quantity')
                    ->first()?->items_sum_quantity
                ?? 0
            ),
        ];
    }
}
