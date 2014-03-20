<?php

class Article extends Base {

    public static function get($type = null) {
        $_instance = new self;
        $query = new EntityFieldQuery();
        $query->entityCondition('entity_type', 'node')
              ->propertyCondition('status', '1');

        //set the type
        if (isset($type)) {
            if(!is_array($type)) {
                $type = array($type);
            }
            $query->entityCondition('bundle', $type);
        } else {
            $query->entityCondition('bundle', $GLOBALS['article_node_types']);
        }

        $_instance->currentQuery = $query;
        return $_instance;
    }

    /**
    * returns a node data array from a curated collection
    */
    public static function getCollection($nid) {
        if (isset($nid)) {
            $node = node_load($nid);
            $node_language = LANGUAGE_NONE;
            if ($node && isset($node->field_article[$node_language][0]['nid'])) {
                $node_data = array();
                foreach ($node->field_article[$node_language] as $f) {
                    $tmp_data = reset(Node::getNodes(array($f['nid'])));
                    if ($tmp_data) {
                        $node_data[] = $tmp_data;
                    }
                }
                if ($node_data) {
                    return $node_data;
                }
            }
        }
        return array();
    }

}