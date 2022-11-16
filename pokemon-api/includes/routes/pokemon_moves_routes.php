<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PokemonMovesModel.php';


function deleteOnePokemonMove(Request $request, Response $response, array $args) {
    $pokemon_moves_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_moves_model = new PokemonMovesModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonMoves = $args["pokemonMoves"];
        //check for artist id
        if (isset($pokemonMoves)) {
            //check if artist exists
            $pokemon_moves_info = $pokemon_moves_model->getPokemonMovesById($pokemonMoves);
            $pokemon_moves_name = $pokemon_moves_model->getPokemonMovesById($pokemonMoves);
            if (!$pokemon_moves_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for pokemon move ". $pokemonMoves ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemon_moves_info = $pokemon_moves_model->delSinglePokemonMoves($pokemonMoves);
        } 
        $response_data = json_encode(array("Message" => "Pokemon move ". $pokemonMoves ." deleted.", 
                "Pokemon move information" => $pokemon_moves_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetPokemonMoveById(Request $request, Response $response, array $args) {
    $pokemon_moves_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_moves_model = new PokemonMovesModel();

    // Retreive the artist id from the request's URI.
    $pokemonMoves = $args["pokemonMoves"];
    if (isset($pokemonMoves)) {
        // Fetch the info about the specified artist.
        $pokemon_moves_info = $pokemon_moves_model->getPokemonMovesById($pokemonMoves);
        if (!$pokemon_moves_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified pokemon move.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemon_moves_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
