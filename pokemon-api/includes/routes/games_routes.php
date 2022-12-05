<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GameModel.php';

function deleteOneGame(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $gamez = $args["gamez"];
        //check for artist id
        if (isset($gamez)) {
            //check if artist exists
            $game_info = $game_model->getGameById($gamez);
            $game_name = $game_model->getGameById($gamez);
            if (!$game_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for games ". $gamez ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $game_info = $game_model->delSingleGame($gamez);
        } 
        $response_data = json_encode(array("Message" => "Game ". $gamez ." deleted.", 
                "Game information" => $game_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


function handleGetGameById(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the artist id from the request's URI.
    $gamez = $args["gamez"];
    if (isset($gamez)) {
        // Fetch the info about the specified artist.
        $game_info = $game_model->getGameById($gamez);
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified game.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($game_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateGame(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    
    $valid_rows = array();
    $rows_not_added = 0;
    
    $game_model = new GameModel();
    $generation_model = new GenerationModel();
    $parsed_body = $request->getParsedBody();
    
    // checking for request body
    if (!$parsed_body || !(is_array($parsed_body) && array_is_list($parsed_body)) || empty($parsed_body)) {
        $response_code = HTTP_BAD_REQUEST;
        $response_data = json_encode(getErrorBadRequest("Missing or badly formatted request body."));
        $response->getBody()->write($response_data);
        return $response->withStatus($response_code);
    }
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        //check for generation id
        $generation_id = $args["generationId"];
        
        if (isset($generation_id) && $generation_model->getGenerationById($generation_id)) {
            foreach ($parsed_body as $single_game) {
                if (validateGame($single_game)) {
                    // going through each field in a row
                    $game_name = $single_game["name"];

                    $game_record = array(
                        "name" => $game_name, 

                        // generation id from uri
                        "generation_id" => (int) $generation_id
                    );

                    $game_model->createGame($game_record);

                    // preparing response message
                    array_push($valid_rows, $game_record);
                }
                else {
                    $rows_not_added++;
                }
            }
        
            $response_data = json_encode(array("message" => 
                    count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
                    $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
                    "games" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
        }
        else {
            $response_data = json_encode(getErrorBadRequest("Request could not be processed. Please verify the generation ID."));
            $response_code = HTTP_BAD_REQUEST;
        }
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    // if all rows were rejected
    if (empty($valid_rows) && $rows_not_added > 0) {
        $response_code = HTTP_BAD_REQUEST;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetGamesByGeneration(Request $request, Response $response, array $args) {
    $games = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    $generationId = $args["generationId"];
    if (isset($generationId)) {
        // TODO: Implement filtering by Name
        
        $games = $game_model->getAllGamesByGeneration($generationId);
        
        if (!$games) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No games were found for the specified generation.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($games, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleDeleteSpecificGameRelatedToGeneration(Request $request, Response $response, array $args) {
    $game_spec = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $gen = $args["generationId"];
        $gamez = $args['gameId'];
        $requested_format = $request->getHeader('Accept');
        if (isset($gen)) {
            $game_spec = $game_model->getSpecificGameRelatedToGeneration($gen, $gamez);
            $show = $game_model->getSpecificGameRelatedToGeneration($gen, $gamez);
            if (!$game_spec) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for ability ". $gamez ." and pokemon ". $gen ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $game_spec = $game_model->deleteSpecificGameRelatedToGeneration($gen, $gamez);
        }
        $response_data = json_encode(array("Message" => "Abilities ". $gamez ." related to Pokemon ". $gen ." is now deleted.", 
        "Ability related to pokemon information" => $show), JSON_INVALID_UTF8_SUBSTITUTE);
    
    //-- We verify the requested resource representation.    
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
function validateGame($single_game) {
    return isset($single_game["name"]);

}