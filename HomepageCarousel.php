<?php

class HomepageCarousel {
    
    public static function get() {
        $query = new EntityFieldQuery;
        $query->entityCondition('entity_type', 'node')
              ->entityCondition('bundle', array('carousel_homepage'))
              ->propertyCondition('status', '1')
              ->propertyOrderBy('created', 'DESC')
              ->range(0,1);
        $result = $query->execute();
        if (isset($result['node'])) {
            return reset(Node::getNodes(array_keys($result['node'])));
        } else {
            return false;
        }
    }   
}