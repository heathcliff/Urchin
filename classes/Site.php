<?php

class Site {

    public static function getFallbackImgSrc() {
        return '/' . path_to_theme() . '/img/placeholder.jpg';
    }

    public static function getLibPath($lib_name) {
        return path_to_theme() . '/lib/' . $lib_name . '.php';
    }

    public static function getRequestURI() {
        global $base_url;
        return $base_url.request_uri();
    }

    public static function getSharedPath($view_name, $directory = false) {
        if ($directory) {
            return path_to_theme() . '/templates/' . $directory . '/_' . $view_name . '.php';
        } else {
            return path_to_theme() . '/templates/shared/_' . $view_name . '.php';
        }
    }

    public static function isFirstPage() {
        return !(isset($_GET['page']) && intval($_GET['page']) > 0);
    }

    public static function breadcrumb() {
        return theme('breadcrumb', array('breadcrumb' => drupal_get_breadcrumb()));
    }

}