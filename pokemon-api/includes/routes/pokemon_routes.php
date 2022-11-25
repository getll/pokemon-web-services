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

function handleCreatePokemon(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $pokemons = "";
    
    $pokemon_model = new PokemonModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_pokemon) {
            // going through each field in a row
            $pokemon_id = $single_pokemon["pokemon_id"];
            $pokemon_name = $single_pokemon["name"];
            $pokemon_uri = $single_pokemon["uri"];
            $pokemon_height = $single_pokemon["height"];
            $pokemon_weight = $single_pokemon["weight"];
            $pokemon_type_1 = $single_pokemon["primary_type"];
            $pokemon_type_2 = $single_pokemon["secondary_type"];
            $pokemon_intro_gen = $single_pokemon["intro_gen"];

            $pokemon_record = array(
                "pokemon_id" => $pokemon_id, 
                "name" => $pokemon_name, 
                "uri" => $pokemon_uri, 
                "height" => $pokemon_height, 
                "weight" => $pokemon_weight, 
                "primary_type" => $pokemon_type_1, 
                "secondary_type" => $pokemon_type_2, 
                "intro_gen" => $pokemon_intro_gen
            );
            $pokemon_model->createPokemon($pokemon_record);

            // preparing response message
            $pokemons .= ((empty($pokemons)) ? "Created rows for " . $pokemon_name : ", " . $pokemon_name);
        }
        
        $response_data = json_encode(array("message" => $pokemons, 
                "pokemon" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdatePokemon(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $pokemons = "";
    
    $pokemon_model = new PokemonModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_pokemon) {
            // going through each field in a row
            $pokemon_id = $single_pokemon["pokemon_id"];
            $pokemon_name = $single_pokemon["name"];
            $pokemon_uri = $single_pokemon["uri"];
            $pokemon_height = $single_pokemon["height"];
            $pokemon_weight = $single_pokemon["weight"];
            $pokemon_type_1 = $single_pokemon["primary_type"];
            $pokemon_type_2 = $single_pokemon["secondary_type"];
            $pokemon_intro_gen = $single_pokemon["intro_gen"];

            $pokemon_record = array( 
                "name" => $pokemon_name, 
                "uri" => $pokemon_uri, 
                "height" => $pokemon_height, 
                "weight" => $pokemon_weight, 
                "primary_type" => $pokemon_type_1, 
                "secondary_type" => $pokemon_type_2, 
                "intro_gen" => $pokemon_intro_gen
            );
            $pokemon_model->updatePokemon($pokemon_record, array("pokemon_id" => $pokemon_id));
            
            // preparing response message
            $pokemons .= ((empty($pokemons)) ? "Updated rows for " . $pokemon_name : ", " . $pokemon_name);
        }
        
        $response_data = json_encode(array("message" => $pokemons, 
                "pokemon" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAllPokemons(Request $request, Response $response, array $args) {
    $pokemons = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_model = new PokemonModel();

    $pokemons = $pokemon_model->getAllPokemons();
    
    // TODO: Implement filtering by Name and by PrimaryType & SecondaryType
    if (!$pokemons) {
        $response_data = makeCustomJSONError("resourceNotFound", "There are no records of pokemons");
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemons, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetPokemonsByGeneration(Request $request, Response $response, array $args) {
    $pokemons = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_model = new PokemonModel();

    
    $generationId = $args["generationId"];
    if (isset($generationId)) {
         
        $pokemons = $pokemon_model->getAllPokemonsByGeneration($generationId);

        if (!$pokemons) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No pokemons were found for the specified generation.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemons, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}