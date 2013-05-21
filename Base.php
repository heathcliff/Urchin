<?php

class Base {  
      
    public $currentQuery;
    
    public function recent() {
        $this->currentQuery->propertyOrderBy('created', 'DESC');
        return $this;
    }
    
    public function field($field_name, $field_value) {
        if (isset($field_value)) {
            if(!is_array($field_value)) {
                $field_value = array($field_value);
            }
            $this->currentQuery->fieldCondition($field_name, 'tid', $field_value, 'IN');
        }
        return $this;
    }
    
    public function exclude($exclude) {
        if (isset($exclude)) {
            if(!is_array($exclude)) {
                $nid = array($exclude);
            }
            $this->currentQuery->propertyCondition('nid', $exclude, 'NOT IN');
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

    public function execute() {
        $result = $this->currentQuery->execute();
        if (isset($result['node']))  {
            return Node::getNodes(array_keys($result['node']));
        } else {
            return array();
        }
    }
}