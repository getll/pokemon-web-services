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

    public function getMoveRelatedToPokemon($pokemonId){
        $sql = "SELECT * FROM pokemon_move WHERE pokemon_id = ?";
        $data = $this->run($sql,[$pokemonId])->fetchAll();
        return $data;
    }

    public function deleteMovesRelatedToPokemon($pokemonId){
        $sql = "DELETE FROM pokemon_move WHERE pokemon_id = :pokemonId";
        $data = $this->run($sql, [":pokemonId" => $pokemonId]);
        return $data;
    }

    public function getSpecificMoveRelatedToPokemon($pokemonId, $pokemonMove){
        $sql = "SELECT * FROM pokemon_move WHERE pokemon_id = ? AND move_id = ?";
        $data = $this->run($sql,[$pokemonId, $pokemonMove])->fetchAll();
        return $data;
    }

    public function deleteSpecificMoveRelatedToPokemon($pokemonId, $moveId){
        $sql = "DELETE FROM pokemon_move WHERE pokemon_id = ? AND move_id = ?";
        $data = $this->run($sql, [$pokemonId, $moveId]);
        return $data;
    }
}