<?php

class Select extends Base {

    public static function get($type = null) {
        $_instance = new self;
        $query = db_select('node', 'n');
        $query->condition('status', '1');
        $query->fields('n', array('nid'));

        // set the type
        if (isset($type)) {
            if(!is_array($type)) {
                $type = array($type);
            }
            $query->condition('type', $type);
        } else {
            $query->condition('type', $GLOBALS['article_node_types']);
        }

        $_instance->currentQuery = $query;
        return $_instance;
    }

    public function exclude($exclude) {
        if (isset($exclude)) {
            if(!is_array($exclude)) {
                $exclude = array($exclude);
            }
            $this->currentQuery->condition('n.nid', $exclude, 'NOT IN');
        }
        return $this;
    }

    public function inStock() {



        $this->currentQuery->join('uc_products', 'uc_products', 'uc_products.nid = n.nid');
        $this->currentQuery->join('uc_product_stock', 'uc_product_stock', 'uc_product_stock.sku = uc_products.model');
        $this->currentQuery->condition('uc_product_stock.stock', 0, '>');
        $this->currentQuery->condition('uc_product_stock.active', 1, '=');
        return $this;
    }

    public function popular() {
        $this->currentQuery->join('node_counter', 'counter', 'n.nid = counter.nid');
        $this->currentQuery->orderBy('counter.totalcount', 'DESC');
        $this->currentQuery->groupBy('n.nid');
        return $this;
    }

    public function sort($type = 'recent', $order = 'DESC') {
        if($type == 'recent') {
            $order = isset($order) ? $order : 'DESC';
            $this->currentQuery->orderBy('n.created', $order);
        }
        return $this;
    }

    public function vocabularyTerm($vocabulary, $tid) {
        if (isset($vocabulary) && isset($tid)) {
            if(!is_array($tid)) {
                $tid = array($tid);
            }
            $field = Taxonomy::getFieldName($vocabulary);
            if ($field) {
                $this->currentQuery->join('field_data_'.$field, $field, 'n.nid = '.$field.'.entity_id');
                $this->currentQuery->condition($field . '.' . $field . '_tid', $tid, 'IN');
            }
        }
        return $this;
    }

    public function notField($field_name, $key = "value") {
        $this->currentQuery->leftJoin("field_data_{$field_name}", "field_data_{$field_name}", "field_data_{$field_name}.entity_id = n.nid");
        $or = db_or();
        $or->condition("field_data_{$field_name}.{$field_name}_{$key}", '0', '=');
        $or->condition("field_data_{$field_name}.{$field_name}_{$key}", NULL, 'IS NULL');
        $this->currentQuery->condition($or);
        return $this;
    }

    public function field($field_name, $field_value, $key = "value") {
        if (isset($field_value)) {
            if (!is_array($field_value)) {
                $field_value = array($field_value);
            }
            $this->currentQuery->leftJoin("field_data_{$field_name}", "field_data_{$field_name}", "field_data_{$field_name}.entity_id = n.nid");
            $this->currentQuery->condition("field_data_{$field_name}.{$field_name}_{$key}", $field_value, 'IN');
        }
        return $this;
    }

    public function groupBy($vocabulary = null) {
        if (isset($vocabulary)) {
            $field = Taxonomy::getFieldName($vocabulary);
            if ($field) {
                $this->currentQuery->groupBy($field.'.'.$field.'_tid');
            }
        }
        return $this;
    }

    public function execute($single = false) {
        $results = $this->currentQuery->execute()->fetchAll(PDO::FETCH_ASSOC);
        if (isset($results[0]['nid'])) {
            $nids = array();
            foreach ($results as $result) {
                $nids[] = $result['nid'];
            }
            return Node::getNodes($nids);
        } else {
            return array();
        }
    }

}