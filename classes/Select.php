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

    public function recent() {
        $this->currentQuery->orderBy('n.created', 'DESC');
        return $this;
    }

    public function popular() {
        $this->currentQuery->join('node_counter', 'counter', 'n.nid = counter.nid');
        $this->currentQuery->orderBy('counter.totalcount', 'DESC');
        $this->currentQuery->groupBy('n.nid');
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

    public function groupBy($vocabulary = null) {
        if ($vocabulary) {
            $field = Taxonomy::getFieldName($vocabulary);
            if ($field) {
                $this->currentQuery->groupBy($field.'.'.$field.'_tid');
            }
        }
        return $this;
    }

    public function execute() {
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