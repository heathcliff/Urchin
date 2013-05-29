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

    public function category($category) {
        return $this->field('field_category', $category);
    }

    public function series($series) {
        return $this->field('field_series', $series);
    }

    /**
    * returns a node data array from a curated collection
    */
    public static function getCollection($nid) {
        if (isset($nid)) {
            $node = node_load($nid);
            if ($node && isset($node->field_article[$node->language][0]['nid'])) {
                $node_data = array();
                foreach ($node->field_article[$node->language] as $f) {
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