<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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

        $user = Auth::user();

        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
//        $user = $request->user();


        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'success' => fn () => $request->session()->get('success'),
            'error' => fn () => $request->session()->get('error'),
            'auth' => [

                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'), // Get roles
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    'cart' => $user->cart()->get() ?? [],
                    'favorites' => $user->favorites()->with('room.media')->get() ?? [],

                    // Get permissions
                ] : null,

            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
