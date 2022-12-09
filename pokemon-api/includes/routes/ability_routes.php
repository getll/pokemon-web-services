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
        //check for ability id
        if (isset($abili)) {
            //check if ability exists
            $ability_info = $ability_model->getAbilityById($abili);
            $ability_name = $ability_model->getAbilityById($abili);
            if (!$ability_info) {
                $response_data = (makeCustomJSONError("resourceNotFound",
                                "No matching record was found for ability " . $abili . "."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $ability_info = $ability_model->delSingleAbility($abili);
        }
        $response_data = json_encode(array("Message" => "Ability " . $abili . " deleted.",
            "Ability information" => $ability_name), JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
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

    $valid_rows = array();
    $rows_not_added = 0;

    $ability_model = new AbilityModel();
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

        foreach ($parsed_body as $single_ability) {
            if (validateAbility($single_ability)) {
                // going through each field in a row
                $ability_name = $single_ability["name"];
                $ability_desc = $single_ability["description"];

                $ability_record = array(
                    "name" => $ability_name,
                    "description" => $ability_desc
                );
                $ability_model->createAbility($ability_record);

                // preparing response message
                array_push($valid_rows, $ability_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "abilities" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
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

function handleUpdateAbility(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    
    $valid_rows = array();
    $rows_not_added = 0;

    $ability_model = new AbilityModel();
    $parsed_body = $request->getParsedBody();

    if (!$parsed_body || !(is_array($parsed_body) && array_is_list($parsed_body)) || empty($parsed_body)) {
        $response_code = HTTP_BAD_REQUEST;
        $response_data = json_encode(getErrorBadRequest("Missing or badly formatted request body."));
        $response->getBody()->write($response_data);
        return $response->withStatus($response_code);
    }

    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {

        foreach ($parsed_body as $single_ability) {
            if (validateAbility($single_ability)) {

                $ability_id = $single_ability["ability_id"];
                $ability_name = $single_ability["name"];
                $ability_desc = $single_ability["description"];

                $ability_record = array(
                    "name" => $ability_name,
                    "description" => $ability_desc
                );

                $ability_model->updateAbility($ability_record, array("ability_id " => $ability_id));

                // preparing response message
                array_push($valid_rows, $ability_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "abilitiy" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
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

function handleGetAllAbilities(Request $request, Response $response, array $args) {
    $abilities = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $ability_model = new AbilityModel();

    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    $page_number = ($input_page_number > 0) ? $input_page_number : 1;
    $per_page = ($input_per_page > 0) ? $input_per_page : 10;

    $ability_model->setPaginationOptions($page_number, $per_page);

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

function validateAbility($single_ability) {
    return isset($single_ability["name"]) &&
            isset($single_ability["description"]);
}
