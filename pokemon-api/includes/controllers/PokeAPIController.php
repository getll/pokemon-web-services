<?php

/**
 * A class for consuming the PokeAPI.
 *
 * @author 
 */
class PokeAPIController extends WebServiceInvoker {

    private $request_options = Array(
        'headers' => Array('Accept' => 'application/json')
    );

    public function __construct() {
        parent::__construct($this->request_options);
    }

    /**
     * Fetches and parses a list of natures from the PokeAPI.
     * 
     * @return array containing some information about natures. 
     */
    function getNatureInfo($nature) {
        $natureData = Array();
        $resource_uri = "https://pokeapi.co/api/v2/nature/" . strtolower($nature);
        
        
        $natureData = $this->invoke($resource_uri);

        if (!empty($natureData)) {
            foreach ($natureData as $key => $book) {
                $books["decreased_stat"] = $book["name"];
                //
                $index++;
            }
        }
        return $books;
    }

}
