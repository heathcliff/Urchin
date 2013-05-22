<?php

class Utility {
    /**
    * returns a trimmed string given a string and a specified length
    */
    public static function trimText($text, $length = 80, $append = '…') {
        if (strlen($text) > $length) {
            $last_space = strrpos(substr($text, 0, $length), ' ');
            $text = substr($text, 0, $last_space);
            $text .= $append;
        }
        return $text;
    }

    public static function hyphenate($string) {
        $string = preg_replace("/[^0-9a-zA-Z ]/m", "", $string);
        $string = preg_replace("/ /", "-", $string);
        return strtolower($string);
    }
}