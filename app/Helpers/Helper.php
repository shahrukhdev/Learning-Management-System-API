<?php

use Illuminate\Support\Carbon;

function formatLastLogin($date): string
{
    if (!$date) {
        return '-';
    }

    $date = Carbon::parse($date);

    if ($date->isToday()) {
        return 'Today, ' . $date->format('h:i A');
    }

    if ($date->isYesterday()) {
        return 'Yesterday, ' . $date->format('h:i A');
    }

    return $date->format('M d, Y h:i A');
}