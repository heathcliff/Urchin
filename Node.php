<?php

class Node {
    /**
    * returns node data arrays given an array of node ids
    */
    public static function getNodes(array $nids) {
        $node_data = array();
        foreach ($nids as $nid) {
            $node = node_load($nid);
            if ($node) {
                $node_data[] = self::getNodeData($node);
            }
        }
        return $node_data;
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
        $data = array(
            'nid'               => $node->nid,
            'type'              => str_replace('article_', '', strtolower($node->type)),
            'created'           => $node->created,
            'author_nid'        => Node::getField($node, 'field_author', 'nid'), // TODO: AUTHOR NAME
            'title'             => $node->title,
            'image_uri'         => Node::getThumbnail($node),
            'youtube_id'        => Node::getField($node, 'field_youtube_id'),
            'excerpt'           => Node::getExcerpt($node),
            'path'              => '/' . drupal_lookup_path('alias', 'node/' . $node->nid),
        );
        if ($node->type == 'event') {
            $data['date']   = Node::getField($node, 'field_date');
            $data['city']   = Taxonomy::getTerm(Node::getField($node, 'field_city', 'tid'));
            $data['venue']  = Node::getField($node, 'field_venue');
            $data['artist'] = Node::getField($node, 'field_artist');
        }
        return $data;
    }

    public static function getAuthor($node = null) {
        if ($node && isset($node->field_author[$node->language][0]['nid'])) {
            $author = node_load($node->field_author[$node->language][0]['nid']);
            if ($author) {
                return array(
                    'name'      => $author->title,
                    'excerpt'   => $author->field_excerpt[$author->language][0]['value'],
                    'image_uri' => $author->field_image[$author->language][0]['uri'],
                );
            }
        }
        return false;
    }

    public static function getCategory($node = null) {
        if ($node) {
            if (isset($node->field_category[$node->language][0]['tid'])) {
                $tid  = $node->field_category[$node->language][0]['tid'];
                $term = taxonomy_term_load($tid);
                $name = $term->name;
                return array('tid' => $tid, 'name' => $name);
            }
        }
        return false;
    }

    public static function getExcerpt($node = null) {
        if ($node) {
            if (isset($node->field_excerpt[$node->language][0]['value'])) {
                return Utility::trimText(strip_tags($node->field_excerpt[$node->language][0]['value']), 275);
            } else if (isset($node->body[$node->language][0]['value'])) {
                $body_array = explode("\n", $node->body[$node->language][0]['value']);
                if (isset($body_array[0]) && strlen($body_array[0]) > 0) {
                    return Utility::trimText(strip_tags($body_array[0]), 275);
                }
            }
        }
        return false;
    }

    public static function getField($node, $field, $key = 'value', $id = 0, $strip_tags = false, $multiple = false) {
        if (isset($node) && isset($field) && !empty($node->$field)) {
            $node_field = $node->$field;
            if ($multiple && $key == 'nid') {
                foreach ($node_field[$node->language] as $f) {
                    $nids[] = $f['nid'];
                }
                return Node::getNodes($nids);
            } else if ($strip_tags) {
                $result = strip_tags($node_field[$node->language][$id][$key]);
            } else {
                $result = $node_field[$node->language][$id][$key];
            }
            return $result;
        }
        return false;
    }

    public static function getThumbnail($node = null) {
        if ($node) {
            if (isset($node->field_image[$node->language][0]['uri'])) {
                return $node->field_image[$node->language][0]['uri'];
            } else if (isset($node->field_gallery_image[$node->language][0]['uri'])) {
                //fall back to the gallery image if there's no field image.
                return $node->field_gallery_image[$node->language][0]['uri'];
            }
        }
        return false;
    }

    /**
     * returns an array of nodes related to a provided node by tags, series, or category
     */
    public static function getRelated($node = null)
    {
        if ($node) {
            $tags     = Taxonomy::getTids($node->field_tag, $node->language);
            $series   = Taxonomy::getTids($node->field_series, $node->language);
            $category = Taxonomy::getTids($node->field_category, $node->language);

            $query = db_select('node', 'n');
            $query->leftJoin('field_data_field_tag', 'field_tag', 'n.nid = field_tag.entity_id');
            $query->leftJoin('field_data_field_series', 'field_series', 'n.nid = field_series.entity_id');
            $query->leftJoin('field_data_field_category', 'field_category', 'n.nid = field_category.entity_id');
            $or = db_or();
            $query->fields('n', array('nid'))
                  ->condition('status', 1)
                  ->condition('type', $GLOBALS['article_node_types'])
                  ->condition('n.nid', $node->nid, '!=')
                  ->orderBy('created', 'DESC')
                  ->range(0,3)
                  ->groupBy('n.nid');

            if ($tags || $series || $category) {
                if ($tags) {
                    $or->condition('field_tag.field_tag_tid', $tags, 'IN');
                }
                if ($series) {
                    $or->condition('field_series.field_series_tid', $series, 'IN');
                }
                if ($category) {
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