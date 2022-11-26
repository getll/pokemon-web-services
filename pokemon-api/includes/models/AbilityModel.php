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

    public function createAbility($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
    
    public function updateAbility($data, $where){
        $info = $this->update($this->table_name, $data, $where);
        return $info;
    }

    public function getAllAbilitiesFiltered($filteringOptions){
        $query_options = Array();
        $sql = "SELECT * FROM abilities WHERE 1 ";

        // Now we validate and filter:
        // by Name
        if (isset($filteringOptions["name"])) {
            $name = $filteringOptions["name"];
            $sql .= " AND name LIKE :name ";            
            $query_options[":name"] = "%" .  $filteringOptions["name"] . "%";
        } 

        $data = $this->run($sql, $query_options)->fetchAll();
        return $data;
    }

    public function getAllAbilitiesByPokemon($pokemon_id){
        $sql = "SELECT abilities.* FROM abilities
                JOIN pokemon_ability ON abilities.ability_id=pokemon_ability.ability_id
                JOIN pokemon ON pokemon_ability.pokemon_id=pokemon.pokemon_id
                WHERE pokemon.pokemon_id = ?";
        $data = $this->run($sql, [$pokemon_id])->fetchAll();
        return $data;
    }
}  
    