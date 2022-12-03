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

function handleCreateMove(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    
    $valid_rows = array();
    $rows_not_added = 0;
    
    $moves_model = new MovesModel();
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
        
        foreach ($parsed_body as $single_move) {
            // validate move and check if it does not already exist since name is unique
            if (validateMove($single_move) && isset($single_move["name"]) && !$moves_model->getMoveByName($single_move["name"])) {
                // going through each field in a row
                $move_name = $single_move["name"];
                $move_desc = $single_move["description"];
                $move_category = $single_move["category"];
                $move_power = isset($single_move["power"]) ? $single_move["power"] : null;
                $move_accuracy = isset($single_move["accuracy"]) ? $single_move["accuracy"] : null;
                $move_power_points = $single_move["power_points"];
                $move_type = $single_move["type"];
                $move_secondary_effect = ($single_move["has_secondary_effect"]) ? 1 : 0;

                $move_record = array(
                    "name" => $move_name, 
                    "description" => $move_desc, 
                    "category" => $move_category, 
                    "power" => $move_power, 
                    "accuracy" => $move_accuracy, 
                    "power_points" => $move_power_points, 
                    "type" => $move_type, 
                    "has_secondary_effect" => $move_secondary_effect
                );
                $moves_model->createMove($move_record);

                // preparing response message
                array_push($valid_rows, $move_record);
            }
            else {
                $rows_not_added++;
            }
        }
        
        $response_data = json_encode(array("message" => 
                count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
                $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
                "moves" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
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

function handleUpdateMove(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    
    // ---------------------------------------------------------------------------------------------------------------------------------------
    // new response for validation
    // $moves = "";
    $valid_rows = array();
    $rows_not_added = 0;
    
    $moves_model = new MovesModel();
    $parsed_body = $request->getParsedBody();
    
    // ---------------------------------------------------------------------------------------------------------------------------------------
    // validation for $parsed body / request body
    // checking for request body
    if (!$parsed_body || !(is_array($parsed_body) && array_is_list($parsed_body)) || empty($parsed_body)) {
        $response_code = HTTP_BAD_REQUEST;
        $response_data = json_encode(getErrorBadRequest("Missing or badly formatted request body."));
        $response->getBody()->write($response_data);
        return $response->withStatus($response_code);
    }
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        foreach ($parsed_body as $single_move) {
            // ---------------------------------------------------------------------------------------------------------------------------------------
            // validation added in foreach loop, checking the $single move row using validate function
            // validation excludes if the id of the row isset, to add in validation pls
            // id can also checked if it exists in db, but still technically valid if it doesnt. query will just not happen ---> it is this ($moves_model->getMoveById($single_move["move_id"])
            if (validateMove($single_move) && isset($single_move["move_id"]) && $moves_model->getMoveById($single_move["move_id"])) {
                // going through each field in a row
                $move_id = $single_move["move_id"];
                $move_name = $single_move["name"];
                $move_desc = $single_move["description"];
                $move_category = $single_move["category"];
                
                // ---------------------------------------------------------------------------------------------------------------------------------------
                // optional params in body, checking if they are set and using null if they are not
                $move_power = isset($single_move["power"]) ? $single_move["power"] : null;
                $move_accuracy = isset($single_move["accuracy"]) ? $single_move["accuracy"] : null;
                
                $move_power_points = $single_move["power_points"];
                $move_type = $single_move["type"];
                
                // turning boolean value into 1 or 0
                $move_secondary_effect = ($single_move["has_secondary_effect"]) ? 1 : 0;

                $move_record = array(
                    "name" => $move_name, 
                    "description" => $move_desc, 
                    "category" => $move_category, 
                    "power" => $move_power, 
                    "accuracy" => $move_accuracy, 
                    "power_points" => $move_power_points, 
                    "type" => $move_type, 
                    "has_secondary_effect" => $move_secondary_effect
                );
                $moves_model->updateMove($move_record,array( "move_id" => $move_id));

                // ---------------------------------------------------------------------------------------------------------------------------------------
                // preparing response message
                array_push($valid_rows, $move_record);
            }
            // ---------------------------------------------------------------------------------------------------------------------------------------
            // coutning rows that were not added/invalid
            else {
                $rows_not_added++;
            }
        }
        
        // ---------------------------------------------------------------------------------------------------------------------------------------
        // new reponse to include information about moves that were invalid and so not added
        $response_data = json_encode(array("message" => 
                count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " updated, " .
                $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
                "moves" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    // ---------------------------------------------------------------------------------------------------------------------------------------
    // change response code if all of the rows were invalid, so nothing was added to the db
    if (empty($valid_rows) && $rows_not_added > 0) {
        $response_code = HTTP_BAD_REQUEST;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAllMoves(Request $request, Response $response, array $args) {
    $moves = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $move_model = new MovesModel();
    
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    $page_number = ($input_page_number > 0) ? $input_page_number : 1;
    $per_page = ($input_per_page > 0) ? $input_per_page : 10;

    $move_model->setPaginationOptions($page_number, $per_page);

    $filter_params = $request->getQueryParams();
    
    // Filtering
    $moves = $move_model->getAllMovesFiltered($filter_params);

    if (!$moves) {
        $response_data = makeCustomJSONError("resourceNotFound", "There are no records of moves");
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($moves, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetMovesByPokemon(Request $request, Response $response, array $args) {
    $moves = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $moves_model = new MovesModel();

    
    $pokemonId = $args["pokemonId"];
    if (isset($pokemonId)) {
          
        $moves = $moves_model->getAllMovesByPokemon($pokemonId);

        if (!$moves) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No moves were found for the specified pokemon.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($moves, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function validateMove($single_move) {
    if (isset($single_move["power"])) {
        if (!(is_numeric($single_move["power"]) && $single_move["power"] >= 0)) {
            return false;
        }
    }
    
    if (isset($single_move["accuracy"])) {
        if (!(is_numeric($single_move["accuracy"]) && $single_move["accuracy"] >= 0)) {
            return false;
        }
    }
    
    return isset($single_move["name"]) && 
            isset($single_move["description"]) &&
            isset($single_move["category"]) &&
            in_array($single_move["category"], MOVE_CATEGORIES) &&
            isset($single_move["power_points"]) &&
            is_numeric($single_move["power_points"]) &&
            $single_move["power_points"] % 5 == 0 &&
            isset($single_move["type"]) &&
            in_array($single_move["type"], POKEMON_TYPES) &&
            isset($single_move["has_secondary_effect"]) &&
            ($single_move["has_secondary_effect"] === 1 || $single_move["has_secondary_effect"] === 0);
}