<?php

class Site {   
    
    public static function getLatestIssueImageUri()
    {
        $query = db_select('node', 'n');
        $query->join('field_data_field_image', 'field_image', 'n.nid = field_image.entity_id');
        $query->fields('n', array('nid'))
              ->fields('field_image', array('field_image_fid'))
              ->condition('status', 1)
              ->condition('type', array('issue'))
              ->orderBy('created', 'DESC')
              ->range(0,1);
        $result = reset($query->execute()->fetchAll(PDO::FETCH_ASSOC));
        if (isset($result['field_image_fid']))
        {
            $image = file_load($result['field_image_fid']);
            if (isset($image->uri)) {
                return $image->uri;
            }
        }
        return false;
    }

    /**
    * returns true if this is the first page of a paginated index
    */
    public static function isFirstPage() {
        return !(isset($_GET['page']) && intval($_GET['page']) > 0);
    }

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