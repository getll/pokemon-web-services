<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GymModel.php';


function deleteOneGym(Request $request, Response $response, array $args) {
    $gym_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $gym_model = new GymModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $gyms = $args["gyms"];

        if (isset($gyms)) {

            $gym_info = $gym_model->getGymById($gyms);
            $gym_name = $gym_model->getGymById($gyms);
            if (!$gym_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for gym ". $gyms ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $gym_info = $gym_model->delSingleGym($gyms);
        } 
        $response_data = json_encode(array("Message" => "Gym ". $gyms ." deleted.", 
                "Gym information" => $gym_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetGymById(Request $request, Response $response, array $args) {
    $gym_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $gym_model = new GymModel();

    // Retreive the gym id from the request's URI.
    $gyms = $args["gyms"];
    if (isset($gyms)) {

        $gym_info = $gym_model->getGymById($gyms);
        if (!$gym_info) {
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
        $response_data = json_encode($gym_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetGymsByLocation(Request $request, Response $response, array $args) {
    $gyms = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $gym_model = new GymModel();

    
    $locationId = $args["locationId"];
    if (isset($locationId)) {
        // TODO: Implement filtering by Name
        $gyms = $gym_model->getAllGymsByLocation($locationId);

        if (!$gyms) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No gyms were found for the specified location.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($gyms, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}