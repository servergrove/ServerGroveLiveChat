<?php

namespace ServerGrove\SGLiveChatBundle\Util;

class DateTool
{

    public static function getFriendlyDate($dt, $timezone = 0)
    {
        $now = time() + ( $timezone * 3600 );

        $dt += ( $timezone * 3600 );

//        $now = mktime( 23, 0, 0, 5, 29, 2008 );

        if (date('Ymd', $now) == date('Ymd', $dt)) {
            return date('g:i a', $dt);
        }


        if (date('Ymd', $now) - date('Ymd', $dt) == 1) {
            return 'Yesterday ' . date('g:i a', $dt);
        }

        if (date('Ym', $now) == date('Ym', $dt)) {
            return date('M j', $dt);
        }

        if (date('Y', $now) == date('Y', $dt)) {
            return date('M j', $dt);
        }

        return date('n/j/Y', $dt);
    }

    public static function ezDate($d)
    {

        if (is_numeric($d)) {
            $ts = $d;
        }
        else
            $ts = time() - strtotime(str_replace("-", "/", $d));

        if ($ts > 31536000)
            $val = round($ts / 31536000, 0) . ' year';
        else if ($ts > 2419200)
            $val = round($ts / 2419200, 0) . ' month';
        else if ($ts > 604800)
            $val = round($ts / 604800, 0) . ' week';
        else if ($ts > 86400)
            $val = round($ts / 86400, 0) . ' day';
        else if ($ts > 3600)
            $val = round($ts / 3600, 0) . ' hour';
        else if ($ts > 60)
            $val = round($ts / 60, 0) . ' minute';
        else
            $val = $ts . ' second';

        if ($val > 1)
            $val .= 's';
        return $val;
    }

}
