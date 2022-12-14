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

        if (isset($pokemonMoves)) {

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


    $pokemonMoves = $args["pokemonMoves"];
    if (isset($pokemonMoves)) {

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

function handleGetAllMovesRelatedToPokemon(Request $request, Response $response, array $args) {
    $moves_pokemon = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_moves_model = new PokemonMovesModel();


    $pokemonId = $args["pokemonId"];

    $moves_pokemon = $pokemon_moves_model->getMoveRelatedToPokemon($pokemonId);

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($moves_pokemon, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


function deleteMovesByPokemon(Request $request, Response $response, array $args) {
    $pokemonMove_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemonMove_model = new PokemonMovesModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonId = $args["pokemonId"];

        if (isset($pokemonId)) {

            $pokemonMove_info = $pokemonMove_model->getMoveRelatedToPokemon($pokemonId);
            $pokemonId_name = $pokemonMove_model->getMoveRelatedToPokemon($pokemonId);
            if (!$pokemonMove_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for moves related to pokemon ". $pokemonId ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemonMove_info = $pokemonMove_model->deleteMovesRelatedToPokemon($pokemonId);
        } 
        $response_data = json_encode(array("Message" => "Moves related to Pokemon ". $pokemonId ." deleted.", 
                "Moves related to pokemon information" => $pokemonId_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


// Here I need to work on the case if there is none (code works, need to work on output)!!!!!!!!!
function handleGetSpecificMovesRelatedToPokemon(Request $request, Response $response, array $args) {
    $pokemon_move_spec = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_move_model = new PokemonMovesModel();

    $pokemonId = $args["pokemonId"];
    $pokemonMove = $args['pokemonMove'];

    $pokemon_move_spec = $pokemon_move_model->getSpecificMoveRelatedToPokemon($pokemonId, $pokemonMove);
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokemon_move_spec, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


function handleDeleteSpecificMoveRelatedToPokemon(Request $request, Response $response, array $args) {
    $pokemon_move_spec = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokemon_moves_model = new PokemonMovesModel();

    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokemonId = $args["pokemonId"];
        $moveId = $args['moveId'];
        $requested_format = $request->getHeader('Accept');
        if (isset($pokemonId)) {
            $pokemon_move_spec = $pokemon_moves_model->getSpecificMoveRelatedToPokemon($pokemonId, $moveId);
            $show = $pokemon_moves_model->getSpecificMoveRelatedToPokemon($pokemonId, $moveId);
            if (!$pokemon_move_spec) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for move ". $moveId ." and pokemon ". $pokemonId ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokemon_move_spec = $pokemon_moves_model->deleteSpecificMoveRelatedToPokemon($pokemonId, $moveId);
        }
        $response_data = json_encode(array("Message" => "Move ". $moveId ." related to Pokemon ". $pokemonId ." is now deleted.", 
        "Move related to pokemon information" => $show), JSON_INVALID_UTF8_SUBSTITUTE);
    
    //-- We verify the requested resource representation.    
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}