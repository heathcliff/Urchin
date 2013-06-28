<?php

class Utility {

    public static function slugify($string) {
        $string = preg_replace("/[^0-9a-zA-Z ]/m", "", $string);
        $string = preg_replace("/ /", "-", $string);
        return strtolower($string);
    }

    public static function trimText($text, $length = 80, $append = '…') {
        if (strlen($text) > $length) {
            $last_space = strrpos(substr($text, 0, $length), ' ');
            $text = substr($text, 0, $last_space);
            $text .= $append;
        }
        return $text;
    }


}