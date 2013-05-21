<?php

class Ad {
    public static function render($adId = null, $width = null, $height = null) {
        if ($adId && $width && $height) {
            $html  = "";
            $html .= "<div id=\"ad-" . $adId . "\" class=\"ad-wrap ad-wrap-" . $width . "x" . $height . "\">";
            $html .=     "<script>";
            $html .=         "GA_googleFillSlot('" . $adId . "');";
            $html .=     "</script>";
            $html .= "</div>";
            return $html;
        }
    }
}