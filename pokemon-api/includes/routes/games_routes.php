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
    $games = "";
    
    $game_model = new GameModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        $generation_id = $args["generationId"];
        //check for generation id
        
        if (isset($generation_id)) {
            foreach ($parsed_body as $single_game) {
                // going through each field in a row
                $game_id = $single_game["gameId"];
                $game_name = $single_game["name"];

                $game_record = array(
                    "game_id" => $game_id, 
                    "name" => $game_name, 
                    
                    // generation id from uri
                    "generation_id" => $generation_id
                );
                
                $game_model->createGame($game_record);

                // preparing response message
                $games .= ((empty($games)) ? "Created rows for " . $game_name : ", " . $game_name);
            }
        
            $response_data = json_encode(array("message" => $games, 
            "games" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
        }
        
        
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}