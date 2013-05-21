<?php

class Search {   
    
    public static function get($search_term = null)
    {
        if ($search_term) {
            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'node')
                  ->addTag('search')
                  ->entityCondition('bundle', $GLOBALS['article_node_types'])
                  ->propertyCondition('status', '1')
                  ->propertyOrderBy('created', 'DESC')
                  ->pager(10);
            $result = $query->execute();
            if (isset($result['node'])) {
                return Node::getNodes(array_keys($result['node']));
            }
        }
        return false;
    }

}