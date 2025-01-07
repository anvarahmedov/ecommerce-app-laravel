<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

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
            ->with('user.vendor');
        }
        return $this->cachedCardItems;
    } catch (\Exception $e) {

    }
   }

   public function getTotalQuantity(): int {

   }

   public function getTotalPrice(): float {

   }

   protected function updateItemQuantityInDatabase(int $productID, int $quantity, array $optionsIDs) {

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
