<?php

/**
 * A model for the customer table
 *
 * @author Sleiman Rabah
 */
class CustomerModel extends BaseModel {

    private $table_name = "customer";

    /**
     * A model class for the `customer` database table.
     * It exposes operations that can be performed on customer records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all customer from the `customer` table.
     * @return array A list of customer. 
     */
    public function getAll() {
        $sql = "SELECT * FROM $this->table_name";        
        $data = $this->paginate($sql);
        return $data;
    }

}
