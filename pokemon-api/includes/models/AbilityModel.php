<?php

class AbilityModel extends BaseModel {

    private $table_name = "abilities";

    /**
     * A model class for the `album` database table.
     * It exposes operations that can be performed on albums records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all albums from the `album` table that belong to a specific artist.
     * @return array A list of albums. 
     */
    public function delSingleAbility($abili){
        $sql = "DELETE FROM abilities WHERE ability_id = :abili";
        $data = $this->run($sql, [":abili" => $abili]);
        return $data;
    }

    public function getAbilityById($abili){
        $sql = "SELECT * FROM abilities WHERE ability_id = ?";
        $data = $this->run($sql, [$abili])->fetch(); 
        return $data;
    }

}  
    