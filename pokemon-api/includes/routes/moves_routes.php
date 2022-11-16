<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/MovesModel.php';


function deleteOneMove(Request $request, Response $response, array $args) {
    $moves_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $moves_model = new MovesModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $moves = $args["moves"];
        //check for artist id
        if (isset($moves)) {
            //check if artist exists
            $moves_info = $moves_model->getMoveById($moves);
            $move_name = $moves_model->getMoveById($moves);
            if (!$moves_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for move ". $moves ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $moves_info = $moves_model->delSingleMove($moves);
        } 
        $response_data = json_encode(array("Message" => "Move ". $moves ." deleted.", 
                "Move information" => $move_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetMoveById(Request $request, Response $response, array $args) {
    $moves_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $moves_model = new MovesModel();

    // Retreive the artist id from the request's URI.
    $moves = $args["moves"];
    if (isset($moves)) {
        // Fetch the info about the specified artist.
        $moves_info = $moves_model->getMoveById($moves);
        if (!$moves_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified move.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($moves_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
