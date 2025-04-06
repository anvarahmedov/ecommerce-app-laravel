<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function profile(Vendor $vendor)
    {
        return inertia('Vendor/Profile');
    }

    public function store(Request $request) {
        
    }
}
