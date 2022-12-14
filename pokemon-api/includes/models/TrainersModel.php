<?php

class TrainersModel extends BaseModel {

    private $table_name = "trainers";
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
    public function delSingleTrainer($trainers){
        $sql = "DELETE FROM trainers WHERE trainer_id = :trainers";
        $data = $this->run($sql, [":trainers" => $trainers]);
        return $data;
    }

    public function getTrainerById($trainers){
        $sql = "SELECT * FROM trainers WHERE trainer_id = ?";
        $data = $this->run($sql, [$trainers])->fetch();
        return $data;
    }

    public function createTrainer($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
    
    public function createTrainerAsGymLeader($record) {
        $data = $this->insert($this->table_name, $record);
        return $data;
    }
    
    public function updateTrainer($record, $where) {
        $data = $this->update($this->table_name, $record, $where);
        return $data;
    }

    public function getAllTrainers(){
        $sql = "SELECT * FROM trainers";
        $data = $this->paginate($sql); 
        return $data;
    }

    public function getAllTrainersByGym($gym_id){
        $sql = "SELECT trainers.* FROM trainers
                JOIN gyms ON trainers.trainer_id=gyms.gym_leader
                WHERE gyms.gym_id = ?";
        $data = $this->run($sql, [$gym_id])->fetchAll();
        return $data;
    }
}