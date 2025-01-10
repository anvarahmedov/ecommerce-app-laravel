<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\ProductStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;


    public function category() {
        return $this->belongsTo(Category::class);
    }

    protected $with = ['variations'];
  //  protected $fillable = [
  //      'slug', 'title'
  //  ];

 // public function getSlug(Product $product) {
 //   return $product->slug;
 // }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100);
        $this->addMediaConversion('small')
            ->width(480);
        $this->addMediaConversion('large')
            ->width(1200);
    }

    protected $fillable = [
        // Other fillable attributes
        'variations',
    ];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function variation_types() {
        return $this->hasMany(VariationType::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variations() {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

 //   public function options() {
  //      return $this->hasMany(VariationTypeOption::class);
  //  }

    public function scopeForVendor(Builder $query): Builder {
        return $query->where('created_by', auth()->user()->id);
    }

    public function scopeForWebsite(Builder $query): Builder {
        return $query->published();
    }

    public function scopePublished(Builder $query): Builder {
        return $query->where('status', ProductStatusEnum::Published);
    }

    public function getPriceForOptions($optionsIDs = []) {

        $optionsIDs = array_values($optionsIDs);
        sort($optionsIDs);
       // dd($this->variations);
        foreach($this->variations as $variation) {
            //dd('dsds');
            $a = $variation->variation_type_options_ids;
            sort($a);
          //  dd($optionsIDs == $a);
            if ($optionsIDs == $a) {
                return $variation->price !== null ? $variation->price : $this->price;
            }
        }

    }
}
