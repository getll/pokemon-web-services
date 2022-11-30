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
        $natures = Array();
        $resource_uri = "https://pokeapi.co/api/v2/nature/" . strtolower($nature);
        
        $natureData = $this->invoke($resource_uri);

        if (!empty($natureData)) {

            $natureData = json_decode($natureData, true);

            $natures["decreased_stat"] = isset($natureData["decreased_stat"]["name"]) ? $natureData["decreased_stat"]["name"] : "None";
            $natures["increased_stat"] = isset($natureData["increased_stat"]["name"]) ? $natureData["increased_stat"]["name"] : "None";
            $natures["hates_flavor"] = isset($natureData["hates_flavor"]["name"]) ? $natureData["hates_flavor"]["name"] : "None";
            $natures["likes_flavor"] = isset($natureData["likes_flavor"]["name"]) ? $natureData["likes_flavor"]["name"] : "None";
        }
        return $natures;
    }
}
