<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/LocationModel.php';


function deleteOneLocation(Request $request, Response $response, array $args) {
    $location_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $location_model = new LocationModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $location = $args["location"];
      
        if (isset($location)) {
           
            $location_info = $location_model->getLocationById($location);
            $location_name = $location_model->getLocationById($location);
            if (!$location_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for location ". $location ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $location_info = $location_model->delSingleLocation($location);
        } 
        $response_data = json_encode(array("Message" => "Location ". $location ." deleted.", 
                "Location information" => $location_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetLocationById(Request $request, Response $response, array $args) {
    $location_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $location_model = new LocationModel();

    // Retreive the location id from the request's URI.
    $location = $args["location"];
    if (isset($location)) {
   
        $location_info = $location_model->getLocationById($location);
        if (!$location_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified location.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($location_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetLocationsByGame(Request $request, Response $response, array $args) {
    $locations = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $location_model = new LocationModel();

    
    $gameId = $args["gameId"];
    if (isset($gameId)) {
        // TODO: Implement filtering by Name and by Location???
        $locations = $location_model->getAllLocationsByGame($gameId);

        if (!$locations) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No locations were found for the specified game.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($locations, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}