<?php

class Pager extends Article {
    //Pager is really similar to the Entity query, with the exception of this variation in the limit call
    public function limit($limit) {
        if (isset($limit)) {
            $this->currentQuery->pager($limit);
        } else {
            $this->currentQuery->pager(5);
        }
        return $this;
    }
}