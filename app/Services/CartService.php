<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;
use App\Models\VariationType;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Decimal;
use Illuminate\Support\Str;
use App\Models\Vendor;
use App\Models\User;

class CartService
{
    private ?array $cachedCardItems = null;

    protected const COOKIE_NAME = 'cartItems';

    protected const COOKIE_LIFETIME = 60 * 24 * 365;


   public function addItemToCart(Product $product, int $quantity = 1, array $optionsIDs = null) {
        if ($optionsIDs === null) {
            $optionsIDs = $product->variation_types->mapWithKeys(fn(VariationType $type)
            =>
            [$type->id => $type->options[0]?->id]
        )->toArray();
        }

        $price = $product->getPriceForOptions($optionsIDs);

        if (Auth::check()) {
            $this->saveItemToDatabase($product->id, $quantity, $price, $optionsIDs);
        } else {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionsIDs);
        }
   }

   public function updateItemQuantity(int $productID, int $quantity, $optionsIDs = null) {
        if (Auth::check()) {
            $this->updateItemQuantityInDatabase($productID, $quantity, $optionsIDs);
        } else {
            $this->updateItemQuantityInCookies($productID, $quantity, $optionsIDs);
        }
   }

   public function removeItemFromCart(int $productID, $optionsIDs = null) {
    //dd($optionsIDs);
    if (Auth::check()) {
        $this->removeItemFromDatabase($productID, $optionsIDs);
    } else {
        $this->removeItemFromCookies($productID, $optionsIDs);
    }
   }

   public function getCartItems(): array {

    try{
        if ($this->cachedCardItems === null) {
            if (auth()->check()) {
                $cartItems = $this->getCartItemsFromDatabase();

              //  dd($cartItems);
            } else {
                $cartItems = $this->getCartItemsFromCookies();
            }
            $productIDs = collect($cartItems)
            ->map(fn($item) => $item['product_id']);
            $products = Product::whereIn('id', $productIDs)
            ->with('user.vendor')->forWebsite()->get()->keyBy('id');

            $cartItemData = [];


            foreach ($cartItems as $key => $cartItem) {
                //dd(json_decode($cartItem['option_ids'], true));
                $product = data_get($products, $cartItem['product_id']);
                if (!$product) continue;

                $optionInfo = [];
                $options = VariationTypeOption::with('variationType')
    ->whereIn('id', json_decode($cartItem['option_ids'], true))  // Decode JSON and get array values
    ->get()
    ->keyBy('id');

                 $imageUrl = null;
                foreach($cartItems as $cartItem) {
                 foreach(json_decode($cartItem['option_ids'], true) as $option_id) {
                    //dd($option_id);
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
                }
              //  dd($user = User::with('vendor')->find($product->user->id)->vendor);
               // dd(Vendor::where('user_id', $product->user->id));

                 $cartItemData[] = [
                    'id' => $cartItem['id'],
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'option_ids' => $cartItem['option_ids'],
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
        throw $e;
        Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
    }
    return [];
   }

   public function getTotalQuantity(): int {
    $totalQuantity = 0;
   // dd($this->getCartItems());
    foreach ($this->getCartItems() as $item) {
       // foreach($items as $item) {
       // dd($item);
      // dd($item);
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

   protected function updateItemQuantityInDatabase(int $productID, int $quantity, $optionsIDs = null) {
    $userID = Auth::id();

    $cartItem = CartItem::where('user_id', $userID)
    ->where('product_id', $productID)->where('variation_type_options_ids',
json_decode($optionsIDs))->first();

if ($cartItem) {
    $cartItem->update([
        'quantity' => $quantity
    ]);
}
   }

   protected function updateItemQuantityInCookies(int $productID, int $quantity, $optionsIDs = null) {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionsIDs);

        $itemKey = $productID . '_' . json_encode($optionsIDs);

        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] = $quantity;
        }

        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
   }

   protected function saveItemToDatabase(int $productID, int $quantity, $price, array $optionsIDs) {

    $userID = Auth::id();
    ksort($optionsIDs);

    $cartItem = CartItem::where('user_id', $userID)
    ->where('product_id', $productID)
    ->where('variation_type_options_ids', $optionsIDs)->first();
   // dd($cartItem,$productID, $quantity, $price, $optionsIDs);
    if ($cartItem) {
        $cartItem->update([
            'quantity' => DB::raw('quantity + ' . $quantity)
        ]);
    } else {
        CartItem::create([
            'user_id' => $userID,
            'product_id' => $productID,
            'quantity' => $quantity,
            'price' => $price,
            'variation_type_options_ids' => json_encode($optionsIDs)
        ]);
    }
   }

   protected function saveItemToCookies(int $productID, int $quantity, $price, array $optionsIDs) {
    $cartItems = $this->getCartItemsFromCookies();
    ksort($optionsIDs);

    $itemKey = $productID . '_' . json_encode($optionsIDs);

    if (isset($cartItems[$itemKey])) {
        $cartItems[$itemKey]['quantity'] += $quantity;
        $cartItems[$itemKey]['quantity'] = $price;
    } else {
        $cartItems['itemKey'] = [
            'id' => Str::uuid(),
            'product_id' => $productID,
            'quantity' => $quantity,
            'price' => $price,
            'option_ids' => $optionsIDs
        ];
    }

    Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
   }

   protected function removeItemFromDatabase(int $productID, array $optionsIDs) {
        $userId = Auth::id();

        ksort($optionsIDs);

        CartItem::where('user_id', $userId)->where('product_id', $productID)
        ->where('variation_type_options_ids', $optionsIDs)
        ->delete();


   }

   protected function removeItemFromCookies(int $productID, $optionsIDs = null) {
        $cartItems = $this->getCartItemsFromCookies();

        ksort($optionsIDs);

        $cartKey = $productID . '_' . json_encode($optionsIDs);

        unset($cartItems[$cartKey]);

        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
   }

   protected function getCartItemsFromDatabase() {
    $userId = Auth::id();
    $cartItems = CartItem::where('user_id', $userId)->get()
    ->map(function($cartItem) {
        return [
            'id' => $cartItem->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'option_ids' => $cartItem->variation_type_options_ids
        ];
    })->toArray();
    //dd($cartItems);
    return $cartItems;
   }

   protected function getCartItemsFromCookies() {
        $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);

        return $cartItems;
   }

}
