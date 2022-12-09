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
 
        if (isset($gens)) {
  
            $generation_info = $generation_model->getGenerationById($gens);
            $generation_name = $generation_model->getGenerationById($gens);
            if (!$generation_info) {
                $response_data = (makeCustomJSONError("resourceNotFound",
                                "No matching record was found for generation " . $gens . "."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $generation_info = $generation_model->delSingleGeneration($gens);
        }
        $response_data = json_encode(array("Message" => "Generation " . $gens . " deleted.",
            "Generation information" => $generation_name), JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
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

    $gens = $args["gens"];
    if (isset($gens)) {
 
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

    $valid_rows = array();
    $rows_not_added = 0;

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

        foreach ($parsed_body as $single_generation) {
            if (validateGeneration($single_generation)) {

                // going through each field in a row
                $generation_pokemon_num = $single_generation["pokemon_number"];

                $generation_record = array(
                    "pokemon_number" => $generation_pokemon_num
                );
                $generation_model->createGeneration($generation_record);

                // preparing response message
                array_push($valid_rows, $generation_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "generations" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
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

function handleUpdateGeneration(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    
    $valid_rows = array();
    $rows_not_added = 0;

    $generation_model = new GenerationModel();
    $parsed_body = $request->getParsedBody();

    if (!$parsed_body || !(is_array($parsed_body) && array_is_list($parsed_body)) || empty($parsed_body)) {
        $response_code = HTTP_BAD_REQUEST;
        $response_data = json_encode(getErrorBadRequest("Missing or badly formatted request body."));
        $response->getBody()->write($response_data);
        return $response->withStatus($response_code);
    }

    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {


        foreach ($parsed_body as $single_generation) {
            if (validateGeneration($single_generation)) {
                // going through each field in a row
                $generation_id = $single_generation["generation_id"];
                $pokemon_num = $single_generation["pokemon_number"];
                
                $generation_record = array(
                  "pokemon_number"=>  $pokemon_num
                );

                $generation_model->updateGeneration($generation_record, array("generation_id" => $generation_id));
                
                array_push($valid_rows, $generation_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "generations" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    if (empty($valid_rows) && $rows_not_added > 0) {
        $response_code = HTTP_BAD_REQUEST;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAllGenerations(Request $request, Response $response, array $args) {
    $generations = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $generation_model = new GenerationModel();

    $generations = $generation_model->getAllGenerations();

    if (!$generations) {
        $response_data = makeCustomJSONError("resourceNotFound", "There are no records of generations");
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($generations, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function validateGeneration($single_generation) {
    return isset($single_generation["pokemon_number"]) &&
            is_numeric($single_generation["pokemon_number"]) &&
            $single_generation["pokemon_number"] >= 0;
}
