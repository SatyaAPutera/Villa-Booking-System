<?php

namespace App\Http\Constants;

class BookingConstants
{
    const DELETED = 0;
    const CONFIRMED = 1;
    const CANCELED = 2;
    const COMPLETED = 3;

    const STATUS = [
        self::DELETED => 'Deleted',
        self::CONFIRMED => 'Confirmed',
        self::CANCELED => 'Canceled',
        self::COMPLETED => 'Completed'
    ];
}
