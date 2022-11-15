<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/TrainersModel.php';


function deleteOneTrainer(Request $request, Response $response, array $args) {
    $trainer_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $trainer_model = new TrainersModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $trainers = $args["trainers"];
        //check for artist id
        if (isset($trainers)) {
            //check if artist exists
            $trainer_info = $trainer_model->getTrainerById($trainers);
            $trainer_name = $trainer_model->getTrainerById($trainers);
            if (!$trainer_info) {
                $response_data = json_encode(array("resourceNotFound", 
                        "No matching record was found for trainer ". $trainers ."."), JSON_INVALID_UTF8_SUBSTITUTE);
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $trainer_info = $trainer_model->delSingleTrainer($trainers);
        } 
        $response_data = json_encode(array("Message" => "Trainer ". $trainers ." deleted.", 
                "Trainer information" => $trainer_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetTrainerById(Request $request, Response $response, array $args) {
    $trainer_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $trainer_model = new TrainersModel();

    // Retreive the artist id from the request's URI.
    $trainers = $args["trainers"];
    if (isset($trainers)) {
        // Fetch the info about the specified artist.
        $trainer_info = $trainer_model->getTrainerById($trainers);
        if (!$trainer_info) {
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
        $response_data = json_encode($trainer_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
