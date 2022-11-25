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

    public function getAllPokemons(){
        $sql = "SELECT * FROM pokemon";
        $data = $this->rows($sql); 
        return $data;
    }

    public function getAllPokemonsByGeneration($generation_id){
        $sql = "SELECT pokemon.* FROM pokemon
                JOIN generations ON pokemon.intro_gen=generations.generation_id
                WHERE generations.generation_id = ?";
        $data = $this->run($sql, [$generation_id])->fetchAll();
        return $data;
    }

    public function getAllPokemonsFilterByName($name){
        
        $sql = "SELECT * FROM pokemon WHERE name LIKE :name";
        $data = $this->run($sql, [":name" => "%" . $name . "%"])->fetchAll();
        return $data;
    }

    public function getAllPokemonsFilterByPrimaryType($primaryType){
        
        $sql = "SELECT * FROM pokemon WHERE primary_type LIKE :primaryType";
        $data = $this->run($sql, [":primaryType" => "%" . $primaryType . "%"])->fetchAll();
        return $data;
    }

    public function getAllPokemonsFilterBySecondaryType($secondaryType){
        $sql = "SELECT * FROM pokemon WHERE secondary_type LIKE :secondaryType";
        $data = $this->run($sql, [":secondaryType" => "%" . $secondaryType . "%"])->fetchAll();
        return $data;
    }

    public function getAllPokemonsFiltered($filteringOptions){
        $query_options = Array();
        $sql = "SELECT * FROM pokemon WHERE 1 ";

        // Now we validate and filter:
        //TODO: do we filter using AND or OR clause?
        if (isset($filteringOptions["name"])) {
            $name = $filteringOptions["name"];
            $sql .= " AND name LIKE :name ";            
            $query_options[":name"] = "%" .  $filteringOptions["name"] . "%";
        }
        if (isset($filteringOptions["primaryType"])) {
            $sql .= " AND  primary_type LIKE :primaryType ";
            $query_options[":primaryType"] = "%" .  $filteringOptions["primaryType"] . "%";            
        }
        if (isset($filteringOptions["secondaryType"])) {
            $sql .= " AND secondary_type LIKE :secondaryType " ;                
            $query_options[":secondaryType"] = "%" .  $filteringOptions["secondaryType"] . "%";
        }    

        $data = $this->run($sql, $query_options)->fetchAll();
        return $data;
    }
}