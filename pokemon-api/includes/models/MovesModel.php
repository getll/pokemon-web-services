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

    public function getAllMovesFiltered($filteringOptions){
        $query_options = Array();
        $sql = "SELECT * FROM moves WHERE 1 ";

        // Now we validate and filter:
        // by Name
        // by Category (optional/low priority)
        // by Type
        if (isset($filteringOptions["name"])) {
            $name = $filteringOptions["name"];
            $sql .= " AND name LIKE :name ";            
            $query_options[":name"] = "%" .  $filteringOptions["name"] . "%";
        }
        if (isset($filteringOptions["category"])) {
            $sql .= " AND  category LIKE :category ";
            $query_options[":category"] = "%" .  $filteringOptions["category"] . "%";            
        }
        if (isset($filteringOptions["type"])) {
            $sql .= " AND type LIKE :type " ;                
            $query_options[":type"] = "%" .  $filteringOptions["type"] . "%";
        }    

        $data = $this->paginate($sql, $query_options);
        return $data;
    }
    
    public function getAllMovesByPokemon($pokemon_id){
        $sql = "SELECT moves.* FROM moves
                JOIN pokemon_move ON moves.move_id=pokemon_move.move_id
                JOIN pokemon ON pokemon_move.pokemon_id=pokemon.pokemon_id
                WHERE pokemon.pokemon_id = ?";
        $data = $this->run($sql, [$pokemon_id])->fetchAll();
        return $data;
    }
}