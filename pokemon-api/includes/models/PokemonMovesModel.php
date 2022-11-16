<?php

class PokemonMovesModel extends BaseModel {

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
    public function delSinglePokemonMoves($pokemonMoves){
        $sql = "DELETE FROM pokemon_move WHERE pokemon_move_id = :pokemonMoves";
        $data = $this->run($sql, [":pokemonMoves" => $pokemonMoves]);
        //return $data;
    }

    public function getPokemonMovesById($pokemonMoves){
        $sql = "SELECT * FROM pokemon_move WHERE pokemon_move_id = ?";
        $data = $this->run($sql, [$pokemonMoves])->fetch();
        return $data;
    }

}