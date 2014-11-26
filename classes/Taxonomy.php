<?php

class Taxonomy
{

    public static function getChildren($tid = null) {
        if (isset($tid)) {
            $children = array_keys(taxonomy_get_children($tid));
            if ($children) {
                return $children;
            }
        }
        return false;
    }

    public static function getParents($tid = null) {
        if (isset($tid)) {
            $parents = array_keys(taxonomy_get_parents($tid));
            if ($parents) {
                return $parents;
            }
        }
        return false;
    }

    public static function getFieldName($vid = null) {
        if(isset($vid)) {
            if (class_exists('UrchinCustomizations') && method_exists('UrchinCustomizations', 'getFieldName')) {
                return UrchinCustomizations::getFieldName($vid);
            } else {
                switch ($vid) {
                    case $GLOBALS['tag']:
                        return 'field_tag';
                        break;
                    case $GLOBALS['category']:
                        return 'field_category';
                        break;
                    default:
                        return false;
                        break;
                }
            }
        }
        return false;
    }

    public static function getSeriesInfo($node = null) {
        if ($node) {
            $node_language = LANGUAGE_NONE;
            if (isset($node->field_series[$node_language][0]['tid'])) {
                $term = taxonomy_term_load($node->field_series[$node_language][0]['tid']);
                if ($term) {
                    return self::getTermInfo($term);
                }
            }
        }
        return false;
    }

    public static function getName($tid = null) {
        if ($tid) {
            return db_query("SELECT name FROM {taxonomy_term_data} WHERE tid = {$tid} LIMIT 1")->fetchField();
        }
        return false;
    }

    public static function getTerm($tid = null) {
        if (isset($tid)) {
            $term = taxonomy_term_load($tid);
            if ($term) {
                return self::getTermInfo($term);
            }
        }
    }

    public static function getTermInfo($term = null) {
        if ($term) {
            return array(
                'tid'       => $term->tid,
                'name'      => $term->name,
                'image_uri' => isset($term->field_image[LANGUAGE_NONE][0]['uri']) ? $term->field_image[LANGUAGE_NONE][0]['uri'] : false,
                'path'      => url('taxonomy/term/' . $term->tid),

            );
        }
        return false;
    }

    public static function getTids($field, $language) {
        $tids = array();
        if (isset($field[$language])) {
            foreach ($field[$language] as $f) {
                if (isset($f['tid'])) {
                    $tids[] = intval($f['tid']);
                }
            }
            if ($tids) {
                return $tids;
            }
        }
        return false;
    }

}

