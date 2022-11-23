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
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for pokemon ability ". $pokebi ."."));
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
        //unset($pokemonAbility_info['is_hidden']);
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


function handleGetAllAbilitiesRelatedToPokemon(Request $request, Response $response, array $args) {
    $ability_pokemon = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_abilities_model = new PokemonAbilityModel();

    // Retreive the artist id from the request's URI.
    $pokemonId = $args["pokemonId"];

    $ability_pokemon = $pokemon_abilities_model->getAbilityRelatedToPokemon($pokemonId);
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($ability_pokemon, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


function deleteAbilityByPokemon(Request $request, Response $response, array $args) {
    $pokemonAbility_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemonAbility_model = new PokemonAbilityModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonId = $args["pokemonId"];
        //check for artist id
        if (isset($pokemonId)) {
            //check if artist exists
            $pokemonAbility_info = $pokemonAbility_model->getAbilityRelatedToPokemon($pokemonId);
            $pokemonId_name = $pokemonAbility_model->getAbilityRelatedToPokemon($pokemonId);
            if (!$pokemonAbility_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for abilities related to pokemon ". $pokemonId ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemonAbility_info = $pokemonAbility_model->deleteAbilityRelatedToPokemon($pokemonId);
        } 
        $response_data = json_encode(array("Message" => "Abilities related to Pokemon ". $pokemonId ." deleted.", 
                "Ability related to pokemon information" => $pokemonId_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


// Here I need to work on the case if there is none (code works, need to work on output)!!!!!!!!!
function handleGetSpecificAbilitiesRelatedToPokemon(Request $request, Response $response, array $args) {
    $pokemon_ability_spec = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_ability_model = new PokemonAbilityModel();
    
    $pokemonId = $args["pokemonId"];
    $pokemonAbility = $args['pokemonAbility'];
    if (isset($pokemonId, $pokemonAbility)) {
        $pokemon_ability_spec = $pokemon_ability_model->getSpecificAbilityRelatedToPokemon($pokemonId, $pokemonAbility);
        
        //$pokemon_ability_spec ['New: '] = "Hello";
        //unset($pokemon_ability_spec['ability_id']);
        if (!$pokemon_ability_spec) {
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified pokemon ability.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemon_ability_spec, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteSpecificAbilitiesRelatedToPokemon(Request $request, Response $response, array $args) {
    $pokemon_ability_spec = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_ability_model = new PokemonAbilityModel();

    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonId = $args["pokemonId"];
        $abilityId = $args['abilityId'];
        $requested_format = $request->getHeader('Accept');
        if (isset($pokemonId)) {
            $pokemon_ability_spec = $pokemon_ability_model->getSpecificAbilityRelatedToPokemon($pokemonId, $abilityId);
            $show = $pokemon_ability_model->getSpecificAbilityRelatedToPokemon($pokemonId, $abilityId);
            if (!$pokemon_ability_spec) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for ability ". $abilityId ." and pokemon ". $pokemonId ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemon_ability_spec = $pokemon_ability_model->deleteSpecificAbilityRelatedToPokemon($pokemonId, $abilityId);
        }
        $response_data = json_encode(array("Message" => "Abilities ". $abilityId ." related to Pokemon ". $pokemonId ." is now deleted.", 
        "Ability related to pokemon information" => $show), JSON_INVALID_UTF8_SUBSTITUTE);
    
    //-- We verify the requested resource representation.    
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

