<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\Product;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        return Inertia::render(
            'Cart/Index', [
                'cartItems' => $cartService->getCartItemsGrouped(),
            ]
            );
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product, CartService $cartService)
    {
        $request->mergeIfMissing([
            'quantity' => 1
        ]);

        $data = $request->validate([
            'options_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

      //  dd($product->price);

        $cartService->addItemToCart($product, $data['quantity'],
        $data['options_ids']);

        return back()->with('success', 'Product added to cart successfully!');


    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CartService $cartService)
    {
        $request->validate([
            'quantity' => ['integer', 'min:1'],
        ]);

        $optionsIDs = $request->input('options_ids');
        $quantity = $request->input('quantity');

        $cartService->updateItemQuantity($product->id, $quantity, $optionsIDs);

        return back()->with('success', 'Product was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        $options_ids = $request->input('options_ids');

        $cartService->removeItemFromCart($product->id, $options_ids);

        return back()->with('success', 'Product was removed from cart');
    }

    public function checkout() {

    }
}
