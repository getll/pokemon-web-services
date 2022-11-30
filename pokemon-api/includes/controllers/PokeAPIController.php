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
    function getNatureInfo() {
        $books = Array();
        $resource_uri = "https://pokeapi.co/api/v2/nature/";
        $booksData = $this->invoke($resource_uri);

        if (!empty($booksData)) {
            // Parse the fetched list of books.   
            $booksData = json_decode($booksData, true);
            //var_dump($booksData);exit;

            $index = 0;
            // Parse the list of books and retreive some  
            // of the contained information.
            foreach ($booksData as $key => $book) {
                $books[$index]["name"] = $book["name"];
                $books[$index]["isbn"] = $book["isbn"];
                $books[$index]["authors"] = $book["authors"];
                $books[$index]["mediaType"] = $book["mediaType"];
                $books[$index]["country"] = $book["country"];
                $books[$index]["released"] = $book["released"];
                //
                $index++;
            }
        }
        return $books;
    }

}
