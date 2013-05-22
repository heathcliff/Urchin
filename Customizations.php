<?php

class Customizations {
    public static function getNodeData($node) {
        $data = array(
            'nid'               => $node->nid,
            'image_uri'         => 'what',
        );
        return $data;
    }
}