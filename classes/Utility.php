<?php

class Utility {   
    /**
    * returns a trimmed string given a string and a specified length
    */
    public static function trimText($text, $length = 80, $append = '...')
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length);
            $text .= $append;
        }
        return $text;
    }
}