<?php

class Site {

    /**
    * returns true if this is the first page of a paginated index
    */
    public static function isFirstPage() {
        return !(isset($_GET['page']) && intval($_GET['page']) > 0);
    }

    /**
    * returns a fallback image source for articles with no thumbnail
    */
    public static function getFallbackImgSrc() {
        return 'http://high-times-assets.s3.amazonaws.com/static/placeholder.jpg';
    }

    /**
    * returns a path/to/file for a shared view
    */
    public static function getSharedPath($view_name) {
        return path_to_theme() . '/templates/shared/_' . $view_name . '.php';
    }

    public static function getLibPath($lib_name) {
        return path_to_theme() . '/lib/' . $lib_name . '.php';
    }

    /**
    * returns the request uri
    */
    public static function getRequestURI() {
        global $base_url;
        return $base_url.request_uri();
    }
}