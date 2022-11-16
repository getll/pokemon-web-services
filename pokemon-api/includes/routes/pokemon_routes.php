<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PokemonModel.php';


function deleteOnePokemon(Request $request, Response $response, array $args) {
    $pokemon_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_model = new PokemonModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonId = $args["pokemonId"];
        //check for artist id
        if (isset($pokemonId)) {
            //check if artist exists
            $pokemon_info = $pokemon_model->getPokemonById($pokemonId);
            $pokemon_name = $pokemon_model->getPokemonById($pokemonId);
            if (!$pokemon_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for pokemon ". $pokemonId ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemon_info = $pokemon_model->delSinglePokemon($pokemonId);
        } 
        $response_data = json_encode(array("Message" => "Pokemon ". $pokemonId ." deleted.", 
                "Pokemon information" => $pokemon_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetPokemonById(Request $request, Response $response, array $args) {
    $pokemon_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_model = new PokemonModel();

    // Retreive the artist id from the request's URI.
    $pokemonId = $args["pokemonId"];
    if (isset($pokemonId)) {
        // Fetch the info about the specified artist.
        $pokemon_info = $pokemon_model->getPokemonById($pokemonId);
        if (!$pokemon_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified pokemon.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemon_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
