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

    public function popular() {
        // orders the results using the statistics module node counter
        $this->currentQuery->join('node_counter', 'counter', 'n.nid = counter.nid');
        $this->currentQuery->orderBy('counter.totalcount', 'DESC');
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