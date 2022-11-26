<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/AbilityModel.php';


function deleteOneAbility(Request $request, Response $response, array $args) {
    $ability_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $ability_model = new AbilityModel();
    //check if json is requested
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $abili = $args["abili"];
        //check for artist id
        if (isset($abili)) {
            //check if artist exists
            $ability_info = $ability_model->getAbilityById($abili);
            $ability_name = $ability_model->getAbilityById($abili);
            if (!$ability_info) {
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for ability ". $abili ."."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $ability_info = $ability_model->delSingleAbility($abili);
        } 
        $response_data = json_encode(array("Message" => "Ability ". $abili ." deleted.", 
                "Ability information" => $ability_name), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}


function handleGetAbilityById(Request $request, Response $response, array $args) {
    $ability_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $ability_model = new AbilityModel();

    // Retreive the ability id from the request's URI.
    $abili = $args["abili"];
    if (isset($abili)) {
        // Fetch the info about the specified ability.
        $ability_info = $ability_model->getAbilityById($abili);
        if (!$ability_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified ability.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($ability_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateAbility(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $abilities = "";
    
    $ability_model = new AbilityModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_ability) {
            // going through each field in a row
            $ability_id = $single_ability["ability_id"];
            $ability_name = $single_ability["name"];
            $ability_desc = $single_ability["description"];

            $ability_record = array(
                "ability_id" => $ability_id, 
                "name" => $ability_name, 
                "description" => $ability_desc
            );
            $ability_model->createAbility($ability_record);

            // preparing response message
            $abilities .= ((empty($abilities)) ? "Created rows for " . $ability_name : ", " . $ability_name);
        }
        
        $response_data = json_encode(array("message" => $abilities, 
                "abilities" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateAbility(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $abilities = "";
    
    $ability_model = new AbilityModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_ability) {
            
            $ability_id = $single_ability["ability_id"];
            $ability_name = $single_ability["name"];
            $ability_desc = $single_ability["description"];

            $ability_record = array(
                "name" => $ability_name, 
                "description" => $ability_desc
            );

            $ability_model->updateAbility($ability_record,array("ability_id "=> $ability_id ));

            // preparing response message
           $abilities .= ((empty($abilities)) ? "Updated rows for " . $ability_name : ", " . $ability_name);
        }
        
        $response_data = json_encode(array("message" => $abilities, 
                "abilities" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAllAbilities(Request $request, Response $response, array $args) {
    $abilities = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $ability_model = new AbilityModel();
    
    $filter_params = $request->getQueryParams();
    
    // Filtering
    $abilities = $ability_model->getAllAbilitiesFiltered($filter_params);

    if (!$abilities) {
        $response_data = makeCustomJSONError("resourceNotFound", "There are no records of abilities");
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($abilities, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAbilitiesByPokemon(Request $request, Response $response, array $args) {
    $abilities = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $ability_model = new AbilityModel();

    
    $pokemonId = $args["pokemonId"];
    if (isset($pokemonId)) {
          
        $abilities = $ability_model->getAllAbilitiesByPokemon($pokemonId);

        if (!$abilities) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No abilities were found for the specified pokemon.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($abilities, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}