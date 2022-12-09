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

        if (isset($trainers)) {
            $trainer_info = $trainer_model->getTrainerById($trainers);
            $trainer_name = $trainer_model->getTrainerById($trainers);
            if (!$trainer_info) {
                $response_data = (makeCustomJSONError("resourceNotFound",
                                "No matching record was found for trainer " . $trainers . "."));
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
            $trainer_info = $trainer_model->delSingleTrainer($trainers);
        }
        $response_data = json_encode(array("Message" => "Trainer " . $trainers . " deleted.",
            "Trainer information" => $trainer_name), JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
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


    $trainers = $args["trainers"];
    if (isset($trainers)) {

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

function handleCreateTrainer(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;

    $valid_rows = array();
    $rows_not_added = 0;

    $trainer_model = new TrainersModel();
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

        foreach ($parsed_body as $single_trainer) {

            if (validateTrainer($single_trainer)) {
                // going through each field in a row
                $trainer_name = $single_trainer["name"];
                $trainer_gender = $single_trainer["gender"];
                $trainer_class = $single_trainer["trainer_class"];
                $trainer_quote = $single_trainer["quote"];
                $trainer_money = $single_trainer["money"];

                $trainer_record = array(
                    "name" => $trainer_name,
                    "gender" => $trainer_gender,
                    "trainer_class" => $trainer_class,
                    "quote" => $trainer_quote,
                    "money" => $trainer_money
                );
                $trainer_model->createTrainer($trainer_record);

                // preparing response message
                array_push($valid_rows, $trainer_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "trainers" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
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

function handleUpdateTrainer(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $valid_rows = array();
    $rows_not_added = 0;

    $trainer_model = new TrainersModel();
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

        foreach ($parsed_body as $single_trainer) {
            if (validateTrainer($single_trainer)) {
                // going through each field in a row
                $trainer_id = $single_trainer["trainer_id"];
                $trainer_name = $single_trainer["name"];
                $trainer_gender = $single_trainer["gender"];
                $trainer_class = $single_trainer["trainer_class"];
                $trainer_quote = $single_trainer["quote"];
                $trainer_money = $single_trainer["money"];

                $trainer_record = array(
                    "name" => $trainer_name,
                    "gender" => $trainer_gender,
                    "trainer_class" => $trainer_class,
                    "quote" => $trainer_quote,
                    "money" => $trainer_money
                );
                $trainer_model->updateTrainer($trainer_record, array("trainer_id" => $trainer_id));

                // preparing response message
                array_push($valid_rows, $trainer_record);
            } else {
                $rows_not_added++;
            }
        }

        $response_data = json_encode(array("message" =>
            count($valid_rows) . ((count($valid_rows) == 1) ? " row" : " rows") . " added, " .
            $rows_not_added . (($rows_not_added == 1) ? " row" : " rows") . " invalid.",
            "trainers" => $valid_rows), JSON_INVALID_UTF8_SUBSTITUTE);
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

function handleGetAllTrainers(Request $request, Response $response, array $args) {
    $trainers = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $trainer_model = new TrainersModel();

    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    $page_number = ($input_page_number > 0) ? $input_page_number : 1;
    $per_page = ($input_per_page > 0) ? $input_per_page : 10;

    $trainer_model->setPaginationOptions($page_number, $per_page);

    // TODO: Implement filtering by Name, by Quote and by TrainerClass
    $trainers = $trainer_model->getAllTrainers();

    if (!$trainers) {
        $response_data = makeCustomJSONError("resourceNotFound", "There are no records of abilities");
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($trainers, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetTrainersByGym(Request $request, Response $response, array $args) {
    $trainers = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $trainers_model = new TrainersModel();

    $gymId = $args["gymId"];
    if (isset($gymId)) {

        $trainers = $trainers_model->getAllTrainersByGym($gymId);

        if (!$trainers) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No trainers were found for the specified gym.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($trainers, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function validateTrainer($single_trainer) {
    return isset($single_trainer["name"]) &&
            isset($single_trainer["gender"]) &&
            in_array($single_trainer["gender"], POKEMON_GENDERS) &&
            isset($single_trainer["trainer_class"]) &&
            isset($single_trainer["quote"]) &&
            isset($single_trainer["money"]) &&
            is_numeric($single_trainer["money"]) &&
            floor($single_trainer["money"]) == $single_trainer["money"];
}
