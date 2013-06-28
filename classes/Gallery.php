<?php

class Gallery
{
    public static function get($node) {
        if ($node && $node->type == "article_gallery") {
            $gallery_data = array();
            foreach ($node->field_gallery_image[$node->language] as $key => $field_gallery_image) {
                $gallery_data[] = array(
                    'image_uri' => $field_gallery_image['uri'],
                    'body'      => $node->field_gallery_body[$node->language][$key]['value']
                );
            }

            if ($gallery_data) {
                return $gallery_data;
            }
        }
        return false;
    }
}