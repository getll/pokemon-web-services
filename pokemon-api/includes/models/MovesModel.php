<?php

class MovesModel extends BaseModel {

    private $table_name = "moves";
    
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
    public function delSingleMove($moves){
        $sql = "DELETE FROM moves WHERE move_id = :moves";
        $data = $this->run($sql, [":moves" => $moves]);
        return $data;
    }

    public function getMoveById($moves){
        $sql = "SELECT * FROM moves WHERE move_id = ?";
        $data = $this->run($sql, [$moves])->fetch();
        return $data;
    }

    public function createMove($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
    public function updateMove($record, $where) {
        $data = $this->update($this->table_name, $record, $where);
        return $data;
    }
}