<?php

class GymModel extends BaseModel{
    
    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function getAll(){
        $sql = "SELECT * FROM gyms";
        $data = $this->rows($sql);
        return $data;
    }
    
    public function getWhereLike($gymName){
        $sql = "SELECT * FROM gyms WHERE name LIKE :name ";
        $data = $this->run($sql, [":name" => $gymName . "%"])->fetchAll();
        return $data;
    }
    
    public function updateGym($data, $where){
        $info = $this->update("gyms", $data, $where);
        return $info;
    }
}
