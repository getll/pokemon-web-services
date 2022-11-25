<?php

class LocationModel extends BaseModel{
    
    private $table_name = "locations";
    
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

    public function getAllLocationsByGame($game_id){
        $sql = "SELECT locations.* FROM locations
                JOIN games ON locations.game_id=games.game_id
                WHERE games.game_id = ?";
        $data = $this->run($sql, [$game_id])->fetchAll();
        return $data;
    }
}