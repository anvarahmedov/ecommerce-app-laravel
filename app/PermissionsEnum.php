<?php

namespace App;

enum PermissionsEnum: string
{
    case ApproveVendor = 'ApproveVendors';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}
