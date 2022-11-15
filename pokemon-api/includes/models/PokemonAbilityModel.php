<?php

class PokemonAbilityModel extends BaseModel {

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
    public function delSinglePokeAbi($pokebi){
        $sql = "DELETE FROM pokemon_ability WHERE pokemon_ability_id = :pokebi";
        $data = $this->run($sql, [":pokebi" => $pokebi]);
        //return $data;
    }

    public function getPokeAbiById($pokebi){
        $sql = "SELECT * FROM pokemon_ability WHERE pokemon_ability_id = ?";
        $data = $this->run($sql, [$pokebi])->fetch();
        return $data;
    }

}