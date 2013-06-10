<?php

class Base {

    public $currentQuery;

    public function alpha($order = 'ASC') {
        $this->currentQuery->propertyOrderBy('title', $order);
        return $this;
    }

    public function count() {
        $result = $this->currentQuery->count()->execute();
        return $result;
    }

    public function date($order = 'ASC') {
        $this->currentQuery->fieldOrderBy('field_date', 'value', $order);
        return $this;
    }

    public function dateAfter($date) {
        $this->currentQuery->fieldCondition('field_date', 'value', $date, '>');
        return $this;
    }

    public function dateBetween($start, $stop) {
        $this->currentQuery->fieldCondition('field_date', 'value', $start, '>');
        $this->currentQuery->fieldCondition('field_date', 'value', $stop, '<');
        return $this;
    }

    public function dateBefore($date) {
        $this->currentQuery->fieldCondition('field_date', 'value', $date, '<');
        return $this;
    }

    public function exclude($exclude) {
        if (isset($exclude)) {
            if(!is_array($exclude)) {
                $exclude = array($exclude);
            }
            $this->currentQuery->propertyCondition('nid', $exclude, 'NOT IN');
        }
        return $this;
    }

    public function execute($single = false) {
        $result = $this->currentQuery->execute();
        if (isset($result['node'])) {
            if ($single) {
                return reset(Node::getNodes(array_keys($result['node'])));
            } else {
                return Node::getNodes(array_keys($result['node']));
            }
        } else {
            return array();
        }
    }

    public function field($field_name, $field_value, $key = 'value') {
        if (isset($field_value)) {
            if(!is_array($field_value)) {
                $field_value = array($field_value);
            }
            $this->currentQuery->fieldCondition($field_name, $key, $field_value, 'IN');
        }
        return $this;
    }

    public function limit($limit) {
        if (isset($limit)) {
            $this->currentQuery->range(0, $limit);
        } else {
            $this->currentQuery->range(0, 14);
        }
        return $this;
    }

    public function pager($limit) {
        if (isset($limit)) {
            $this->currentQuery->pager($limit);
        } else {
            $this->currentQuery->pager(14);
        }
        return $this;
    }

    public function recent() {
        $this->currentQuery->propertyOrderBy('created', 'DESC');
        return $this;
    }

    public function sticky() {
        $this->currentQuery->propertyOrderBy('sticky', 'DESC');
        return $this;
    }

    public function vocabularyTerm($vocabulary, $term) {
        if (isset($vocabulary) && isset($term)) {
            if(!is_array($term)) {
                $term = array($term);
            }
            if (Taxonomy::getFieldName($vocabulary)) {
                $this->currentQuery->fieldCondition(Taxonomy::getFieldName($vocabulary), 'tid', $term, 'IN');
            }
        }
        return $this;
    }

}