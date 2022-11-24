<?php

class GenerationModel extends BaseModel {

    private $table_name = "generations";
    
    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function updateGeneration($data, $where){
        $info = $this->update($this->table_name, $data, $where);
        return $info;
    }

    public function delSingleGeneration($gens){
        $sql = "DELETE FROM generations WHERE generation_id = :gens";
        $data = $this->run($sql, [":gens" => $gens]);
        return $data;
    }

    public function getGenerationById($gens){
        $sql = "SELECT * FROM generations WHERE generation_id = ?";
        $data = $this->run($sql, [$gens])->fetch();
        return $data;
    }

    public function createGeneration($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
}
