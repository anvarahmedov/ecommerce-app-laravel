<?php

namespace App;

enum VendorStatusEnum: string
{
    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';

    public static function labels() {
        return [
            self::Pending->value => __('Pending'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
        ];
    }

    public static function colors() {
        return [
            'gray' => self::Pending->value,
            'success' => self::Approved->value,
            'danger' => self::Rejected->value
        ];
    }

}
