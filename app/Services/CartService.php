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
use Illuminate\Support\Facades\Crypt;

class CartService
{
    private ?array $cachedCardItems = null;

    protected const COOKIE_NAME = 'cartItems';

    protected const COOKIE_LIFETIME = 60 * 24 * 365;


   public function addItemToCart(Product $product, int $quantity = 1, array $optionsIDs = null, int $priceForItem) {
    //dd($optionsIDs === null);
    if ($optionsIDs === null) {
            $optionsIDs = $product->variation_types->mapWithKeys(fn(VariationType $type)
            =>
            [$type->id => $type->options[0]?->id]
        )->toArray();

        }
       // dd($priceForItem);

        //dd(empty($optionsIDs));

        if (empty($optionsIDs)) {
            $price = $priceForItem;
        } else {
            $price = $product->getPriceForOptions($optionsIDs);
        }

        //dd($price);



        if (Auth::check()) {
            $this->saveItemToDatabase($product->id, $quantity, $price, $optionsIDs);
        } else {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionsIDs);
        }
   }

   public function updateItemQuantity(int $productID, int $quantity, $optionsIDs = null, $item) {
        if (Auth::check()) {
            $this->updateItemQuantityInDatabase($productID, $quantity, $optionsIDs, $item);
        } else {
            $this->updateItemQuantityInCookies($productID, $quantity, $optionsIDs);
        }
   }

   public function removeItemFromCart(int $productID, $optionsIDs = null, $item) {
    //dd($optionsIDs);
    if (Auth::check()) {
        $this->removeItemFromDatabase($productID, $optionsIDs, $item);
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

          //          $options = VariationTypeOption::with('variationType')
   // ->whereIn('id', json_decode($cartItem['option_ids'], true))  // Decode JSON and get array values
   // ->get()
  //  ->keyBy('id');
                 foreach(json_decode($cartItem['option_ids'], true) as $option_id) {
                    //dd(json_decode($cartItem['option_ids'], true));
                  //  dd($options);
                    //dd($option_id);
                 //   dd(json_decode($cartItem['option_ids'], true));
                   // dd(data_get($options, 2));
                    $option = data_get($options, $option_id);
                  //  if (!$option) {
                 //       continue; // Skip this option if it doesn't exist
                 //   }
                 //   dd($option->name);
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

              //  dd($user = User::with('vendor')->find($product->user->id)->vendor);
               // dd(Vendor::where('user_id', $product->user->id));
                 //dd(json_decode($cartItem['option_ids']));
                 $cartItemData[] = [
                 //   'id' => Crypt::encryptString($cartItem['id']),
                 'id' => $cartItem['id'],
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'option_ids' => json_decode($cartItem['option_ids']),
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

   protected function updateItemQuantityInDatabase(int $productID, int $quantity, $optionsIDs = null, $item) {
    $userID = Auth::id();

    //dd($optionsIDs-);

   // dd($quantity);

    $cartItem = CartItem::where('user_id', $userID)
    ->where('product_id', $productID)->where('variation_type_options_ids',
$optionsIDs)->first();

//dd($item);

$cartItem = CartItem::where('id', $item['id'])->first();

//dd($cartItem);

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

    //dd($price);

    $cartItem = CartItem::where('user_id', $userID)
    ->where('product_id', $productID)
    ->where('variation_type_options_ids', $optionsIDs)->first();

    //dd($cartItem);
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
          // 'variation_type_options_ids' => $optionsIDs
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

   protected function removeItemFromDatabase(int $productID, array $optionsIDs, $item) {
        $userId = Auth::id();

       // dd(User::find($userId));

        ksort($optionsIDs);

      //  dd($optionsIDs);
     // dd($item->id);
      $cartItem = CartItem::where('user_id', $userId)->where('product_id',
      $productID)
     ->where('variation_type_options_ids', json_encode($optionsIDs));
    // dd($cartItem->first());
   // dd($cartItem->first()->id);

    CartItem::where('id', $item['id'])->delete();

      //  $cartItem->delete();

        //dd($this->getCartItems());


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
       // dd($cartItem->variation_type_options_ids);
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

   public function getCartItemsGrouped(): array {
    $cartItems = $this->getCartItems();

    //dd(empty($cartItems));

    if (empty($cartItems)) {
        return $cartItems;
    } else {

    return collect($cartItems)->groupBy(fn ($item) => $item['user']['id'])->
    map(fn ($items, $userID) => [
        'user' => $items->first()['user'],
        'items' => $items->toArray(),
        'totalQuantity' => $items->sum('quantity'),
        'totalPrice' => $items->sum(fn ($item) => $item['price'] * $item['quantity'])
    ])->toArray();
   }
}

    public function moveCartItemsToDatabase($userID) {
        $cartItems = $this->getCartItemsFromCookies();

        foreach ($cartItems as $itemKey => $cartItem) {
            $existingItem = CartItem::where('user_id', $userID)
            ->where('product_id', $cartItem['product_id'])->where('variation_type_options_ids',
        json_encode($cartItem['option_ids']))->first();


        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity = $cartItem['quantity'],
                'price' => $cartItem['price']
            ]);
        } else {
            CartItem::create([
                'user_id' => $userID,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
                'variation_type_options_ids' => $cartItem['option_ids']
            ]);
        }
    }
        Cookie::queue(self::COOKIE_NAME, '', -1);
    }

}
