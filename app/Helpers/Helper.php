<?php

use Illuminate\Support\Carbon;

function getInitials(string $name): ?string
{
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

    if (empty($parts)) {
        return null;
    }

    $firstInitial = mb_substr($parts[0], 0, 1);

    if (count($parts) === 1) {
        return mb_strtoupper($firstInitial);
    }

    $lastInitial = mb_substr(end($parts), 0, 1);

    return mb_strtoupper($firstInitial . $lastInitial);
}

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