<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GenerationModel.php';

function deleteOneGeneration(Request $request, Response $response, array $args) {
    $generation_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $generation_model = new GenerationModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $gens = $args["gens"];
        //check for artist id
        if (isset($gens)) {
            //check if artist exists
            $generation_info = $generation_model->getGenerationById($gens);
            $generation_name = $generation_model->getGenerationById($gens);
            if (!$generation_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for generation ". $gens ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $generation_info = $generation_model->delSingleGeneration($gens);
        } 
        $response_data = json_encode(array("Message" => "Generation ". $gens ." deleted.", 
                "Generation information" => $generation_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}



function handleGetGenerationById(Request $request, Response $response, array $args) {
    $generation_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $generation_model = new GenerationModel();

    // Retreive the artist id from the request's URI.
    $gens = $args["gens"];
    if (isset($gens)) {
        // Fetch the info about the specified artist.
        $generation_info = $generation_model->getGenerationById($gens);
        if (!$generation_info) {
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
        $response_data = json_encode($generation_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateGeneration(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $generations = "";
    
    $generation_model = new GenerationModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_generation) {
            // going through each field in a row
            $generation_id = $single_generation["generation_id"];
            $generation_pokemon_num = $single_generation["pokemon_number"];

            $generation_record = array(
                "generation_id" => $generation_id, 
                "pokemon_number" => $generation_pokemon_num
            );
            $generation_model->createGeneration($generation_record);

            // preparing response message
            $generations .= ((empty($generations)) ? "Created rows for generation " . $generation_id : ", " . $generation_id);
        }
        
        $response_data = json_encode(array("message" => $generations, 
                "generations" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateGeneration(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $generations = "";
    
    $generation_model = new GenerationModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_generation) {
            // going through each field in a row
            $generation_id = $single_generation["generation_id"];
            $generation_pokemon_num = $single_generation["pokemon_number"];

            $generation_model->updateGeneration(array("pokemon_number"=>$generation_pokemon_num),array("generation_id"=>$generation_id));

            // preparing response message
            $generations .= ((empty($generations)) ? "Updated rows for generation " . $generation_id : ", " . $generation_id);
        }
        
        $response_data = json_encode(array("message" => $generations, 
                "generations" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}