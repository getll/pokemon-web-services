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
                $response_data = (makeCustomJSONError("resourceNotFound", 
                        "No matching record was found for pokedex ". $pokedex ."."));
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

function handleCreatePokedex(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $pokedexes = "";
    
    $pokedex_model = new PokedexModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        $trainer_id = $args["trainerId"];
        //check for generation id
        
        if (isset($trainer_id)) {
            foreach ($parsed_body as $single_pokedex) {
                // going through each field in a row
                $pokedex_id = $single_pokedex["pokedex_id"];
                $pokedex_nickname = $single_pokedex["nickname"];
                $pokedex_level = $single_pokedex["level"];
                $pokedex_friendship_level = $single_pokedex["friendship_level"];
                $pokedex_nature = $single_pokedex["nature"];
                $pokedex_gender = $single_pokedex["gender"];
                $pokedex_pokemon_id = $single_pokedex["pokemon_id"];

                $pokedex_record = array(
                    "pokedex_id" => $pokedex_id, 
                    "nickname" => $pokedex_nickname, 
                    "level" => $pokedex_level, 
                    "friendship_level" => $pokedex_friendship_level, 
                    "nature" => $pokedex_nature, 
                    "gender" => $pokedex_gender, 

                    // trainer id from uri
                    "trainer_id" => $trainer_id, 
                    "pokemon_id" => $pokedex_pokemon_id
                );
                $pokedex_model->createPokedex($pokedex_record);

                // preparing response message
                $pokedexes .= ((empty($pokedexes)) ? "Created rows for pokedex " . $pokedex_id : ", " . $pokedex_id);
            }

            $response_data = json_encode(array("message" => $pokedexes, 
                    "pokedex" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdatePokedex(Request $request, Response $response, array $args) {
    $response_code = HTTP_CREATED;
    $pokedexes = "";
    
    $pokedex_model = new PokedexModel();
    $parsed_body = $request->getParsedBody();
    
    $requested_format = $request->getHeader('Accept');
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        
        $trainer_id = $args["trainerId"];
        //check for generation id
        
        if (isset($trainer_id)) {
            foreach ($parsed_body as $single_pokedex) {
                // going through each field in a row
                $pokedex_id = $single_pokedex["pokedex_id"];
                $pokedex_nickname = $single_pokedex["nickname"];
                $pokedex_level = $single_pokedex["level"];
                $pokedex_friendship_level = $single_pokedex["friendship_level"];
                $pokedex_nature = $single_pokedex["nature"];
                $pokedex_gender = $single_pokedex["gender"];
                $pokedex_pokemon_id = $single_pokedex["pokemon_id"];

                $pokedex_record = array( 
                    "nickname" => $pokedex_nickname, 
                    "level" => $pokedex_level, 
                    "friendship_level" => $pokedex_friendship_level, 
                    "nature" => $pokedex_nature, 
                    "gender" => $pokedex_gender, 

                    // trainer id from uri
                    "trainer_id" => $trainer_id, 
                    "pokemon_id" => $pokedex_pokemon_id
                );
                $pokedex_model->updatePokedex($pokedex_record, array("pokedex_id" => $pokedex_id));

                // preparing response message
                $pokedexes .= ((empty($pokedexes)) ? "Updated rows for pokedex " . $pokedex_id : ", " . $pokedex_id);
            }

            $response_data = json_encode(array("message" => $pokedexes, 
                    "pokedex" => $parsed_body), JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetPokedexByTrainer(Request $request, Response $response, array $args) {
    $pokedex = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $pokedex_model = new PokedexModel();

    
    $trainerId = $args["trainerId"];
    if (isset($trainerId)) {
        // TODO: Implement filtering by Nickname, by Pokemon Name, by Level (optional/low priority), by Nature (optional/low priority)
        $pokedex = $pokedex_model->getAllPokedexByTrainer($trainerId);

        if (!$pokedex) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No pokedex were found for the specified trainer.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');

    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($pokedex, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }

    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}