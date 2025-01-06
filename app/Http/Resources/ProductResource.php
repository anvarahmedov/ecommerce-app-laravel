<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);

      return [
        'id' => $this->id,
        'slug' => $this->slug,
        'title' => $this->title,
        'description' => $this->description,
        'price' => $this->price,
        'quantity' => $this->quantity,
        'image' => $this->getFirstMediaUrl('images'),
        'images' => $this->getMedia(
            'images'
        )->map(
            function($image) {
                return [
                    'id' => $image->id,
                    'thumb' => $image->thumb,
                    'small' => $image->getUrl('small'),
                    'large' => $image->getUrl('large')
                ];
            }
        ),
        'user' => [
            'id' => $this->user->id,
            'name' => $this->user->name
        ],
        'department' => [
            'id' => $this->department->id,
            'name' => $this->department->name
        ],
        'variationTypes' => $this->variation_types->map(
            function(
                $variation_type
            ) {
                return [
                    'id' => $variation_type->id,
                    'name' => $variation_type->name,
                    'type' => $variation_type->type,
                    'options' => $variation_type->options->map(
                        function(
                            $option
                        ) {
                            return [
                                'id' => $option->id,
                                'name' => $option->name,
                                'images' => $option->getMedia(
                                    'images'
                                )->map(function($image) {
                                    return [
                                        'id' => $image->id,
                                        'thumb' => $image->getUrl('thumb'),
                                        'small' => $image->getUrl('small'),
                                        'large' => $image->getUrl('large')
                                    ];
                                })
                            ];
                        }
                    )
                ];

            }
        ),
        'variations' => $this->variations->map(
            function ($variation) {
                return [
                    'id' => $variation->id,
                    'variation_type_options_ids' => $variation->variation_type_options_ids,
                    'quantity' => $variation->quantity,
                    'price' => $variation->price
                ];
            }
        )
      ];
    }
}
