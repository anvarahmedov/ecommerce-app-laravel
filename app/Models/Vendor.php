<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\VendorStatusEnum;

class Vendor extends Model
{
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $primaryKey = 'user_id';

    public function scopeElligibleForPayout(Builder $query) {
        return $query->where('status', VendorStatusEnum::Approved);
    }
}
