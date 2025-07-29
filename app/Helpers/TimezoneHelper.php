<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    public static function IstToUtc($datetime)
    {
        $localTime = Carbon::parse($datetime, 'Asia/Kolkata'); 
        $utcTime = $localTime->copy()->setTimezone('UTC');
        $IST = $utcTime->format('Y-m-d H:i:s');
        return $IST; 
    }

    public static function UtcToIst($datetime)
    {
        $utcTime = Carbon::parse($datetime, 'UTC'); 
        $istTime = $utcTime->copy()->setTimezone('Asia/Kolkata');
        $formattedIST = $istTime->format('Y-m-d H:i');
        return $formattedIST; 
    }

    public static function get_formatted_time($time)
    {
        $now = Carbon::now('Asia/Kolkata');
        $created = Carbon::parse($time, 'UTC')->setTimezone('Asia/Kolkata');
        $diffInSeconds = abs($now->diffInRealSeconds($created));
        if ($diffInSeconds < 60) {
            return "just now";
        } elseif ($diffInSeconds < 3600) {
            $mins = floor($diffInSeconds / 60);
            return $mins . " min" . ($mins > 1 ? "s" : "") . " ago";
        } elseif ($now->isSameDay($created)) {
            return $created->format('g:i A');
        } elseif ($created->isSameDay($now->copy()->subDay())) {
            return "yesterday " . $created->format('g:i A');
        } else {
            $datePart = $created->format('jS M');
            if ($created->year !== $now->year) {
                $datePart .= ' ' . $created->year;
            }
            return $datePart . ' ' . $created->format('g:i A');
        }
    }
}
