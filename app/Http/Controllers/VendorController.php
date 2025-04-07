<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RolesEnum;
use App\Models\Vendor;
use App\VendorStatusEnum;

class VendorController extends Controller
{
    public function profile(Vendor $vendor)
    {
        return inertia('Vendor/Profile');
    }

    public function store(Request $request) {
        $request->validate([
            'store_name' => ['required','regex:/^[a-z0-]+$/', 'unique:vendors, store_name'],
            'store_address' => ['nullable'],

        ],
    [
        'store_name.regex' => 'Store name must be lowercase and contain only letters, numbers, and dashes.',
    ]);
    $user = $request->user();
    $vendor = $user->vendor ?: new Vendor();
    $vendor->user_id = $user->id;
    $vendor->status = VendorStatusEnum::Approved->value;
    $vendor->store_name = $request->store_name;
    $vendor->store_address = $request->store_address;
    $vendor->save();
    $user->assignRole(RolesEnum::Vendor);
    }
}
