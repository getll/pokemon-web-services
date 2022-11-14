<?php

class LocationModel extends BaseModel{
    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function updateLocation($data, $where){
        $info = $this->update("locations", $data, $where);
        return $info;
    }
}

