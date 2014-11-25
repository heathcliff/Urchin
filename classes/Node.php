<?php

class Node {

    public static function getTitle($nid = null) {
        if ($nid) {
            return db_query("SELECT title FROM {node} WHERE nid = {$nid} LIMIT 1")->fetchField();
        }
        return false;
    }

    public static function getAuthor($node = null) {
        if (isset($node)) {
            if (class_exists('UrchinCustomizations') && method_exists('UrchinCustomizations', 'getAuthor')) {
                return UrchinCustomizations::getAuthor($node);
            } else {
                $nid = Node::getField($node, 'field_author', 'nid');
                if ($nid) {
                    $author = node_load($nid);
                    if ($author) {
                        return array(
                            'nid'       => $author->nid,
                            'name'      => $author->title,
                            'excerpt'   => Node::getField($author, 'field_excerpt'),
                            'image_uri' => Node::getThumbnail($author),
                            'path'      => url('node/' . $author->nid),
                        );
                    }
                }
                return false;
            }
        }
    }

    public static function getCategory($node = null) {
        if (isset($node)) {
            if (class_exists('UrchinCustomizations') && method_exists('UrchinCustomizations', 'getCategory')) {
                return UrchinCustomizations::getCategory($node);
            } else {
                $tid = Node::getField($node, 'field_category', 'tid');
                if ($tid) {
                    $term = taxonomy_term_load($tid);
                    if ($term) {
                        return array(
                            'tid'       => $tid,
                            'name'      => $term->name,
                            'excerpt'   => Node::getField($term, 'field_excerpt'),
                            'image_uri' => Node::getThumbnail($term),
                            'path'      => url('taxonomy/term/' . $tid),
                        );
                    }
                }
                return false;
            }
        }
    }

    public static function getExcerpt($node = null, $strip_tags = true) {
        if ($node) {
            if (self::getField($node, 'field_excerpt')) {
                if ($strip_tags) {
                    return strip_tags(self::getField($node, 'field_excerpt'));
                } else {
                    return self::getField($node, 'field_excerpt');
                }
            } else if (self::getField($node, 'field_body')) {
                $body_array = explode("\n", self::getField($node, 'field_body'));
                if (isset($body_array[0]) && strlen($body_array[0]) > 0) {
                    if ($strip_tags) {
                        return Utility::trimText(strip_tags($body_array[0]), 275);
                    } else {
                        return Utility::trimText($body_array[0], 275);
                    }
                }
            }
        }
        return false;
    }

    public static function getField($node = null, $field = null, $key = 'value', $id = 0, $strip_tags = false, $multiple = false) {
        if (isset($node) && isset($field) && !empty($node->$field)) {
            $node_field = $node->$field;
            $node_language = LANGUAGE_NONE;
            if ($multiple) {
                if ($key == 'nid') {
                    $nids = array();
                    foreach ($node_field[$node_language] as $f) {
                        $nids[] = $f['nid'];
                    }
                    return Node::getNodes($nids);
                } else if ($key == 'tid') {
                    $terms = array();
                    foreach ($node_field[$node_language] as $f) {
                        $term = Taxonomy::getTerm($f['tid']);
                        if ($term) {
                            $terms[] = $term;
                        }
                    }
                    return $terms;
                } else {
                    $results = array();
                    foreach ($node_field[$node_language] as $f) {
                        $results[] = $f[$key];
                    }
                    return $results;
                }
            } else if ($strip_tags) {
                $result = strip_tags($node_field[$node_language][$id][$key]);
            } else {
                if ($key == 'bool') {
                    $result = ($node_field[$node_language][$id]['value'] == 1);
                } else if (!empty($node_field[$node_language][$id][$key])) {
                    $result = $node_field[$node_language][$id][$key];
                } else {
                    $result = false;
                }
            }
            return $result;
        }
        return false;
    }

    public static function getFieldTids($node, $field) {
        if (isset($node) && isset($field) && !empty($node->$field)) {
            $node_field = $node->$field;
            $node_language = (isset($node->language) && $node->language != '') ? $node->language : LANGUAGE_NONE;
            $tids = array();
            foreach ($node_field[$node_language] as $field) {
                if ($field['tid']) {
                    $tids[] = $field['tid'];
                }
            }
            return $tids;
        }
        return false;
    }

    /**
     *  Similar to getField(), this function is reserved for grouped fields
     *  as it returns field values keyed off their delta values
     *
     *  This function currently only supports field keys of 'value' and 'uri'
     */
    public static function getDeltaFields($node = null, $field = null, $key = 'value') {
        if (isset($node->nid) && $field) {
            if ($key == 'uri') {
                $sql = "SELECT
                            delta,
                            file_managed.uri
                        FROM
                            {field_data_{$field}} field
                        INNER JOIN
                            {file_managed} file_managed ON file_managed.fid = field.{$field}_fid
                        WHERE
                            entity_id   = {$node->nid} AND
                            entity_type = 'node'
                        ";
                $result = db_query($sql);
                $result = $result->fetchAllAssoc('delta');
                $data   = array();
                if ($result) {
                    foreach ($result as $delta => $object) {
                        $data[$delta] = $object->uri;
                    }
                    return $data;
                }
            } else if ($key == 'value') {
                $sql = "SELECT
                            delta,
                            {$field}_value
                        FROM
                            {field_data_{$field}} field
                        WHERE
                            entity_id   = {$node->nid} AND
                            entity_type = 'node'
                        ";
                $result = db_query($sql);
                $result = $result->fetchAllKeyed(0);
                if ($result) {
                    return $result;
                }
            } else if ($key == 'nid') {
                $sql = "SELECT
                            delta,
                            {$field}_nid
                        FROM
                            {field_data_{$field}} field
                        WHERE
                            entity_id   = {$node->nid} AND
                            entity_type = 'node'
                        ";
                $result = db_query($sql);
                $result = $result->fetchAllKeyed(0);
                if ($result) {
                    return $result;
                }
            }
        }
        return false;
    }

    public static function getNids(array $nodes) {
        $nids = array();
        foreach ($nodes as $n) {
            if (isset($n['nid'])) {
                $nids[] = $n['nid'];
            }
        }
        return $nids;
    }

    public static function getNodeData($node) {
        if (class_exists('UrchinCustomizations') && method_exists('UrchinCustomizations', 'getNodeData')) {
            return UrchinCustomizations::getNodeData($node);
        } else {
            $data = array(
                'nid'               => $node->nid,
                'type'              => str_replace('article_', '', strtolower($node->type)),
                'created'           => $node->created,
                'author'            => Node::getAuthor($node),
                'author_nid'        => Node::getField($node, 'field_author', 'nid'),
                'byline'            => Node::getField($node, 'field_byline'),
                'title'             => $node->title,
                'image_uri'         => Node::getThumbnail($node),
                'youtube_id'        => Node::getField($node, 'field_youtube_id'),
                'excerpt'           => Node::getExcerpt($node),
                'path'              => url('node/' . $node->nid),
            );
            return $data;
        }
    }

    public static function getNodes($nids) {
        if(!is_array($nids)) {
            $nids = array($nids);
        }
        $nodes = array();
        foreach ($nids as $nid) {
            $node = node_load($nid);
            if ($node) {
                $data = self::getNodeData($node);
                $nodes[] = $data;
            }
        }
        return $nodes;
    }

    public static function getThumbnail($node = null) {
        if (isset($node)) {
            $node_language = LANGUAGE_NONE;
            if (isset($node->field_image[$node_language][0]['uri'])) {
                return $node->field_image[$node_language][0]['uri'];
            } else if (isset($node->field_gallery_image[$node_language][0]['uri'])) {
                //fall back to the gallery image if there's no field image.
                return $node->field_gallery_image[$node_language][0]['uri'];
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * returns an array of nodes related to a provided node by tags, category, or both
     */
    public static function getRelated($node = null, $limit = 3, $category_only = true, $tags_only = false) {
        if (class_exists('UrchinCustomizations') && method_exists('UrchinCustomizations', 'getRelated')) {
            return UrchinCustomizations::getRelated($node);
        } else {
            if (isset($node)) {
                $node_language = LANGUAGE_NONE;
                $tags          = Taxonomy::getTids($node->field_tag, $node_language);
                $category      = Taxonomy::getTids($node->field_category, $node_language);

                $query = db_select('node', 'n');

                if ($category_only) {
                    $query->leftJoin('field_data_field_category', 'field_category', 'n.nid = field_category.entity_id');
                } else if ($tags_only) {
                    $query->leftJoin('field_data_field_tag', 'field_tag', 'n.nid = field_tag.entity_id');
                } else {
                    $query->leftJoin('field_data_field_category', 'field_category', 'n.nid = field_category.entity_id');
                    $query->leftJoin('field_data_field_tag', 'field_tag', 'n.nid = field_tag.entity_id');
                }

                $or = db_or();
                $query->fields('n', array('nid'))
                      ->condition('status', 1)
                      ->condition('type', $GLOBALS['article_node_types'])
                      ->condition('n.nid', $node->nid, '!=')
                      ->orderBy('created', 'DESC')
                      ->range(0, $limit)
                      ->groupBy('n.nid');

                if ($tags || $category) {
                    if ($tags && !$category_only) {
                        $or->condition('field_tag.field_tag_tid', $tags, 'IN');
                    }
                    if ($category && !$tags_only) {
                        $or->condition('field_category.field_category_tid', $category, 'IN');
                    }
                    $query->condition($or);
                }

                $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

                if (isset($result[0]['nid'])) {
                    $nids = array();
                    foreach ($result as $r) {
                        if (isset($r['nid'])) {
                            $nids[] = $r['nid'];
                        }
                    }
                    if ($nids) {
                        return self::getNodes($nids);
                    }
                }
            }
            return false;
        }
    }

    public static function language() {
        global $language;
        return $language->prefix;
    }

}