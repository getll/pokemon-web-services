<?php

class PokemonModel extends BaseModel {

    private $table_name = "pokemon";

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
    public function delSinglePokemon($pokemonId){
        $sql = "DELETE FROM pokemon WHERE pokemon_id = :pokemonId";
        $data = $this->run($sql, [":pokemonId" => $pokemonId]);
        return $data;
    }

    public function getPokemonById($pokemonId){
        $sql = "SELECT * FROM pokemon WHERE pokemon_id = ?";
        $data = $this->run($sql, [$pokemonId])->fetch();
        return $data;
    }

    public function createPokemon($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
    public function updatePokemon($record, $where) {
        $data = $this->update($this->table_name, $record,$where);
        return $data;
    }
}