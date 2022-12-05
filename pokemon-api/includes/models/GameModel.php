<?php

class GameModel extends BaseModel {

    private $table_name = "games";
    
    public function __construct($options = []) {
        parent::__construct($options);
    }
    

    public function updateGame($data, $where){
        $info = $this->update("games", $data, $where);
        return $info;
    }

    public function delSingleGame($gamez){
        $sql = "DELETE FROM games WHERE game_id = :gamez";
        $data = $this->run($sql, [":gamez" => $gamez]);
        return $data;
    }

    public function getGameById($gamez){
        $sql = "SELECT * FROM games WHERE game_id = ?";
        $data = $this->run($sql, [$gamez])->fetch();
        return $data;
    }
    
    public function createGame($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }

    public function getAllGamesByGeneration($generation_id){
        $sql = "SELECT games.* FROM games
                JOIN generations ON games.generation_id=generations.generation_id
                WHERE generations.generation_id = ?";
        $data = $this->run($sql, [$generation_id])->fetchAll();
        return $data;
    }

    public function getSpecificGameRelatedToGeneration($gen, $gamez){
        $sql = "SELECT * FROM games WHERE generation_id = ? AND game_id = ?";
        $data = $this->run($sql,[$gen, $gamez])->fetch();
        return $data;
    }

    public function deleteSpecificGameRelatedToGeneration($gen, $gamez){
        $sql = "DELETE FROM games WHERE generation_id = ? AND game_id = ?";
        $data = $this->run($sql, [$gen, $gamez]);
        return $data;
    }
}
