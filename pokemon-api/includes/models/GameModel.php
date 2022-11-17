<?php

class GameModel extends BaseModel {

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
}
