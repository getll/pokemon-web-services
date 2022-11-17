<?php

class PokedexModel extends BaseModel {

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
    public function delSinglePokedex($pokedex){
        $sql = "DELETE FROM pokedex WHERE pokedex_id = :pokedex";
        $data = $this->run($sql, [":pokedex" => $pokedex]);
        return $data;
    }

    public function getPokedexById($pokedex){
        $sql = "SELECT * FROM pokedex WHERE pokedex_id = ?";
        $data = $this->run($sql, [$pokedex])->fetch();
        return $data;
    }

    public function createPokedex($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
}