<?php

namespace App\Http\Constants;

class BookingConstants
{
    const DELETED = 0;
    const PENDING = 1;
    const CONFIRMED = 2;
    const CANCELED = 3;
    const COMPLETED = 4;

    const STATUS = [
        self::DELETED => 'Deleted',
        self::PENDING => 'Pending',
        self::CONFIRMED => 'Confirmed',
        self::CANCELED => 'Canceled',
        self::COMPLETED => 'Completed'
    ];
}
