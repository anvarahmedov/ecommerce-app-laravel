<?php

namespace App;

enum OrderStatusEnum: string
{
    case Draft = 'Draft';

    case Paid = 'Paid';

    case Shipped = 'Shipped';

    case Delivered = 'Delivered';

    case Cancelled = 'Cancelled';

    public static function labels() {
        return [
            self::Draft->value => __('Draft'),
            self::Paid->value => __('Paid'),
            self::Shipped->value => __('Shipped'),
            self::Delivered->value => __('Delivered'),
            self::Cancelled->value => __('Cancelled')
        ];
    }
}
