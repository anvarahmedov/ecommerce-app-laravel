<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\Product;
use Inertia\Inertia;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\OrderStatusEnum;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

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
            'quantity' => 1,
            //'price' => $product->price
        ]);

        //dd($product->price);

        $data = $request->validate([
            'options_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1'],
           // 'price' => ['required', 'integer', 'min:1']
        ]);

       // dd($request->price);

        $cartService->addItemToCart($product, $data['quantity'],
        $data['options_ids'] ?: [], $product->price);

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
        //\Log::info('Cart update method triggered');
        $request->validate([
            'quantity' => ['integer', 'min:1'],
        ]);

       // dd($request);

        $optionsIDs = $request->input('options_ids') ?: [];
        $quantity = $request->input('quantity');
        $item = $request->input('item');
        //dd($item);

        $cartService->updateItemQuantity($product->id, $quantity, $optionsIDs, $item);

        return back()->with('success', 'Product was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        $options_ids = $request->input('options_ids');
        //dd($options_ids);

        $item = $request->input('item');

       // dd($item);

        $cartService->removeItemFromCart($product->id, $options_ids, $item);

       // dd($cartService->getCartItems());

        return back()->with('success', 'Product was removed from cart');
    }

    public function checkout(Request $request, CartService $cartService) {
        \Stripe\Stripe::setApiKey(config('app.stripe_secret_key'));

        $vendorId = $request->input('vendor_id');

        $allCartItems = $cartService->getCartItemsGrouped();

        DB::beginTransaction();

        try {
            $checkOutCartItems = $allCartItems;
            if ($vendorId) {
                $checkOutCartItems = [$allCartItems[$vendorId]];
            }
            $orders = [];
            $lineItems = [];
            foreach ($checkOutCartItems as $item) {
                $user = $item['user'];
                $cartItems = $item['items'];

                $order = Order::create([
                    'stripe_session_id' => null,
                    'user_id' => $request->user()->id,
                    'vendor_user_id' => $user['id'],
                    'total_price' => $item['total_price'],
                    'status' => OrderStatusEnum::Draft->value
                ]);
                $orders[] = $order;

                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem['product_id'],
                        'quantity' => $cartItem['quantity'],
                        'price' => $cartItem['price'],
                        'variation_type_options_ids' => $cartItem['option_ids']
                    ]);
                    $description = collect($cartItem['options'])->map(function ($item) {
                        return "{$item['type']['name']}: {$item['name']}";
                    })->implode(', ');

                    $lineItem = [
                        'price_data' => [
                            'currency' => config('app.currency'),
                            'product_data' => [
                                'name' => $cartItem['title'],
                                'images' => [$cartItem['image']]
                            ],
                            'unit_amount' => $cartItem['price'] * 100
                        ],
                        'quantity' => $cartItem['quantity']
                    ];
                    if ($description) {
                        $lineItem['price_data']['product_data']['description'] = $description;
                    }
                    $lineItems[] = $lineItem;
                }
            }
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $request->user()->email,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', []) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('stripe.failure', [])
            ]);

            foreach ($orders as $order) {
                $order->stripe_session_id = $session->id;
                $order->save();
            }

            DB::commit();
            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Something went wrong');
        }
    }
}
