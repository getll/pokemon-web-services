<?php

class LocationModel extends BaseModel{
    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function updateLocation($data, $where){
        $info = $this->update("locations", $data, $where);
        return $info;
    }

    public function delSingleLocation($location){
        $sql = "DELETE FROM locations WHERE location_id = :location";
        $data = $this->run($sql, [":location" => $location]);
        return $data;
    }

    public function getLocationById($location){
        $sql = "SELECT * FROM locations WHERE location_id = ?";
        $data = $this->run($sql, [$location])->fetch();
        return $data;
    }
}

