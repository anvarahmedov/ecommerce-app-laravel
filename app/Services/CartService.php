<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;

class CartService
{
    private ?array $cachedCardItems = null;

    protected const COOKIE_NAME = 'cartItems';

    protected const COOKIE_LIFETIME = 60 * 24 * 365;


   public function addItemToCart(Product $product, int $quantity = 1, array $optionsIDs = null) {

   }

   public function updateItemQuantity(int $productID, int $quantity, $optionsIDs = null) {

   }

   public function removeItemFromCart(int $productID, $optionsIDs = null) {

   }

   public function getCartItems(): array {
    try{
        if ($this->cachedCardItems === null) {
            if (Auth::check()) {
                $cartItems = $this->getCartItemsFromDatabase();
            } else {
                $cartItems = $this->getCartItemsFromCookies();
            }
            $productIDs = collect($cartItems)
            ->map(fn($item) => $item['product_id']);
            $products = Product::whereIn('id', $productIDs)
            ->with('user.vendor')->forWebsite()->get()->keyBy('id');

            $cartItemData = [];

            foreach ($cartItems as $key => $cartItem) {
                $product = data_get($products, $cartItem['product_id']);
                if (!$product) continue;

                $optionInfo = [];
                $options = VariationTypeOption::with('variationType')->whereIn('id',
                 $cartItem['options_ids'])->get()->keyBy('id');

                 $imageUrl = null;

                 foreach($cartItems['options_ids'] as $option_id) {
                    $option = data_get($options, $option_id);
                    if (!$imageUrl) {
                        $imageUrl = $option->getFirstMediaUrl('images', 'small');
                    }
                    $optionInfo[] = [
                        'id' => $option_id,
                        'name' => $option->name,
                        'type' => [
                            'id' => $option->variationType->id,
                            'name' => $option->variationType->name
                        ]
                    ];
                 }

                 $cartItemData[] = [
                    'id' => $cartItem['id'],
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'options_ids' => $cartItem['options_ids'],
                    'options' => $optionInfo,
                    'image' => $imageUrl ?: $product->getFirstMediaUrl('images', 'small'),
                    'user' => [
                        'id' => $product->created_by,
                        'name' => $product->user->vendor->store_name
                    ]
                 ];
            }

            $this->cachedCardItems = $cartItemData;
        }
        return $this->cachedCardItems;
    } catch (\Exception $e) {
        Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
    }
    return [];
   }

   public function getTotalQuantity(): int {
    $totalQuantity = 0;
    foreach ($this->getCartItems() as $item) {
        $totalQuantity += $item['quantity'];
    }
    return $totalQuantity;
   }

   public function getTotalPrice(): float {
    $total = 0;
    foreach ($this->getCartItems() as $item) {
        $total += $item['quantity'] * $item['price'];
    }
    return $total;
   }

   protected function updateItemQuantityInDatabase(int $productID, int $quantity, array $optionsIDs) {
    $userID = Auth::id();

    $cartItem = CartItem::where('user_id', $userID)
    ->where('product_id', $productID);
   }

   protected function updateItemQuantityInCookies(int $productID, int $quantity, array $optionsIDs) {

   }

   protected function saveItemToDatabase(int $productID, int $quantity, ) {

   }

   protected function saveItemToCookies() {

   }

   protected function removeItemFromDatabase() {

   }

   protected function removeItemFromCookies() {

   }

   protected function getCartItemsFromDatabase() {

   }

   protected function getCartItemsFromCookies() {

   }

}
