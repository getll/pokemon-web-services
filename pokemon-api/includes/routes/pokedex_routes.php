<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PokedexModel.php';


function deleteOnePokedex(Request $request, Response $response, array $args) {
    $pokedex_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokedex_model = new PokedexModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $pokedex = $args["pokedex"];
        //check for artist id
        if (isset($pokedex)) {
            //check if artist exists
            $pokedex_info = $pokedex_model->getPokedexById($pokedex);
            $pokedex_name = $pokedex_model->getPokedexById($pokedex);
            if (!$pokedex_info) {
                $response_data = json_encode(array("resourceNotFound", 
                        "No matching record was found for pokedex ". $pokedex ."."), JSON_INVALID_UTF8_SUBSTITUTE);
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $pokedex_info = $pokedex_model->delSinglePokedex($pokedex);
        } 
        $response_data = json_encode(array("Message" => "Pokedex ". $pokedex ." deleted.", 
                "Pokedex information" => $pokedex_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetPokedexById(Request $request, Response $response, array $args) {
    $pokedex_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokedex_model = new PokedexModel();

    // Retreive the artist id from the request's URI.
    $pokedex = $args["pokedex"];
    if (isset($pokedex)) {
        // Fetch the info about the specified artist.
        $pokedex_info = $pokedex_model->getPokedexById($pokedex);
        if (!$pokedex_info) {
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
        $response_data = json_encode($pokedex_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
