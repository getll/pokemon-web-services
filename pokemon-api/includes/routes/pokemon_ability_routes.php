<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PokemonAbilityModel.php';


function deleteOnePokeAbility(Request $request, Response $response, array $args) {
    $pokemonAbility_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemonAbility_model = new PokemonAbilityModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokebi = $args["pokebi"];
        //check for artist id
        if (isset($pokebi)) {
            //check if artist exists
            $pokemonAbility_info = $pokemonAbility_model->getPokeAbiById($pokebi);
            $pokebi_name = $pokemonAbility_model->getPokeAbiById($pokebi);
            if (!$pokemonAbility_info) {
                $response_data = json_encode(array("resourceNotFound", 
                        "No matching record was found for pokemon ability ". $pokebi ."."), JSON_INVALID_UTF8_SUBSTITUTE);
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemonAbility_info = $pokemonAbility_model->delSinglePokeAbi($pokebi);
        } 
        $response_data = json_encode(array("Message" => "Pokemon Ability ". $pokebi ." deleted.", 
                "Pokemon Ability information" => $pokebi_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetPokeAbilityById(Request $request, Response $response, array $args) {
    $pokemonAbility_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemonAbility_model = new PokemonAbilityModel();

    // Retreive the artist id from the request's URI.
    $pokebi = $args["pokebi"];
    if (isset($pokebi)) {
        // Fetch the info about the specified artist.
        $pokemonAbility_info = $pokemonAbility_model->getPokeAbiById($pokebi);
        if (!$pokemonAbility_info) {
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
        $response_data = json_encode($pokemonAbility_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
