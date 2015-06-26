<?php

class Base {

    public $currentQuery;

    //
    // Chainable calls
    // =================
    // Calls that modify the query and return it. These are intended
    // to be used in the middle of the chain.
    //

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

    public function field($field_name, $field_value, $key = 'value') {
        if (isset($field_value)) {
            if(!is_array($field_value)) {
                $field_value = array($field_value);
            }
            $this->currentQuery->fieldCondition($field_name, $key, $field_value, 'IN');
        }
        return $this;
    }

    public function fieldOrderBy($field, $order = 'DESC', $key = 'value') {
        if (isset($field)) {
            $this->currentQuery->fieldOrderBy($field, $key, $order);
        }
        return $this;
    }

    public function limit($limit = 14) {
        $this->currentQuery->range(0, $limit);
        return $this;
    }

    public function range($offset = 0, $limit = 14) {
        $this->currentQuery->range($offset, $limit);
        return $this;
    }

    public function pager($limit = 14) {
        $this->currentQuery->pager($limit);
        return $this;
    }

    public function sort($type = 'recent', $order = false) {
        if ($type == 'recent') {
            $order = $order ? $order : 'DESC';
            $this->currentQuery->propertyOrderBy('created', $order);
        } else if ($type == 'changed') {
            $order = $order ? $order : 'DESC';
            $this->currentQuery->propertyOrderBy('changed', $order);
        } else if ($type == 'alpha') {
            $order = $order ? $order : 'ASC';
            $this->currentQuery->propertyOrderBy('title', $order);
        } else if ($type == 'sticky') {
            $order = $order ? $order : 'DESC';
            $this->currentQuery->propertyOrderBy('sticky', $order);
        }
        return $this;
    }

    public function vocabularyTerm($vocabulary, $term, $condition = 'IN') {
        if (isset($vocabulary) && isset($term)) {
            if(!is_array($term)) {
                $term = array($term);
            }
            if (Taxonomy::getFieldName($vocabulary)) {
                $this->currentQuery->fieldCondition(Taxonomy::getFieldName($vocabulary), 'tid', $term, $condition);
            }
        }
        return $this;
    }

    public function language($language_code = null) {
        if ($language_code) {
            $this->currentQuery->propertyCondition('language', $language_code);
        }
        return $this;
    }

    //
    // Terminating calls
    // =================
    // Calls that execute and return a result. These are intended
    // to be used at the end of the chain.
    //

    public function count() {
        $result = $this->currentQuery->count()->execute();
        return $result;
    }

    public function execute($single = false) {

        // check to see if a language has been specified
        $language_specified = false;
        if (!empty($this->currentQuery->propertyConditions)) {
            foreach ($this->currentQuery->propertyConditions as $condition) {
                if ($condition['column'] == 'language') {
                    $language_specified = true;
                }
            }
        }

        // if a language has not been specified, execute the query in the current language
        if (!$language_specified) {
            if (!empty($GLOBALS['default_language'])) {
                $this->currentQuery->propertyCondition('language', $GLOBALS['default_language']);
            } else {
                global $language ;
                $language_code = $language->language;
                $this->currentQuery->propertyCondition('language', $language_code);
            }
        }

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

}