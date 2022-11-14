<?php

class GenerationModel extends BaseModel {

    public function __construct($options = []) {
        parent::__construct($options);
    }
    
    public function updateGeneration($data, $where){
        $info = $this->updateGeneration($data, $where);
        return $info;
    }

}
