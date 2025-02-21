<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Services\CartService;
use App\Models\CartItem;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cartService = app(CartService::class);
        $totalQuantity = $cartService->getTotalQuantity();
        $totalPrice = $cartService->getTotalPrice();

        $cartItems = $cartService->getCartItems();
        //dd($request);
       // dd(auth()->check());
       // dd($cartService->getCartItems());
       // dd($totalQuantity);
       // dd($totalPrice, $totalQuantity, $cartItems);

       // dd($totalPrice, $totalQuantity, $cartItems);
        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'success' => [
                'message' => session('success'),
                'time' => microtime(true)
            ],
            'error' => session('error'),
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity,
            'miniCartItems' => $cartItems,
            'csrf_token' => csrf_token(),
        ];
    }
}
