<?php

class GameModel extends BaseModel {

    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function updateGame($data, $where){
        $info = $this->update("games", $data, $where);
        return $info;
    }
}
