<?php

class Utility {

    public static function slugify($string) {
        $string = preg_replace("/[^0-9a-zA-Z ]/m", "", $string);
        $string = preg_replace("/ /", "-", $string);
        $string = str_replace("--", "-", $string);
        return strtolower($string);
    }

    public static function trimText($text, $length = 80, $append = '...') {
        if (strlen($text) > $length) {
            $last_space = strrpos(substr($text, 0, $length), ' ');
            $text = substr($text, 0, $last_space);
            $text .= $append;
        }
        return $text;
    }

    public static function timeAgo($date, $granularity = 1) {
        $difference = time() - $date;
        $periods = array(
            'decade' => 315360000,
            'year'   => 31536000,
            'month'  => 2628000,
            'week'   => 604800,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1
        );
        $retval = '';
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference/$value);
                $difference %= $value;
                $retval .= ($retval ? ' ' : '').$time.' ';
                $retval .= (($time > 1) ? $key.'s' : $key);
                $granularity--;
            }
            if ($granularity == '0') {
                break;
            }
        }
        return ' posted ' . $retval . ' ago';
    }

    public static function cleanURL($url) {
        if (strpos($url, "http://") !== 0) {
            $url =  "http://" . $url;
        } 
        return $url;
    }
    
}