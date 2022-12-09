<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

//By the way use these commands on cmd when using the AA
// get composer 
// composer require tuupola/slim-jwt-auth
// composer require vlucas/phpdotenv

require __DIR__ . '/vendor/autoload.php';
require_once './includes/app_constants.php';
require_once './includes/helpers/helper_functions.php';
require_once './includes/helpers/Paginator.php';
require_once './includes/helpers/WebServiceInvoker.php';
require_once './includes/models/BaseModel.php';
require_once './includes/controllers/PokeAPIController.php';
require_once './includes/helpers/JWTManager.php';

define('APP_BASE_DIR', __DIR__);
// IMPORTANT: This file must be added to your .ignore file. 
define('APP_ENV_CONFIG', 'config.env');

//--Step 1) Instantiate App.
$app = AppFactory::create();

//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();

//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

//parsing middleware
$app->addBodyParsingMiddleware();

//-- Step 4)
// TODO: change the name of the sub directory here. You also need to change it in .htaccess
$app->setBasePath("/pokemon-api");


$jwt_secret = JWTManager::getSecretKey();
$api_base_path = "/pokemon-api";
/*
$app->add(new Tuupola\Middleware\JwtAuthentication([
            'secret' => $jwt_secret,
            'algorithm' => 'HS256',
            'secure' => false, // only for localhost for prod and test env set true            
            "path" => $api_base_path, // the base path of the API
            "attribute" => "decoded_token_data",
            "ignore" => ["$api_base_path/token", "$api_base_path/account"],
            "error" => function ($response, $arguments) {
                $data["status"] = "error";
                $data["message"] = $arguments["message"];
                $response->getBody()->write(
                        json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
                return $response->withHeader("Content-Type", "application/json;charset=utf-8");
            }
        ]));
*/
//-- Step 5) Include the files containing the definitions of the callbacks.
//require_once './includes/routes/artists_routes.php';
require_once './includes/routes/pokemon_routes.php';
require_once './includes/routes/ability_routes.php';
require_once './includes/routes/games_routes.php';
require_once './includes/routes/generations_routes.php';
require_once './includes/routes/gyms_routes.php';
require_once './includes/routes/locations_routes.php';
require_once './includes/routes/pokedex_routes.php';
require_once './includes/routes/pokemon_ability_routes.php';
require_once './includes/routes/trainers_routes.php';
require_once './includes/routes/moves_routes.php';
require_once './includes/routes/pokemon_moves_routes.php';
require_once './includes/routes/token_routes.php';

//-- Step 6)
// TODO: And here we define app routes.

$app->any("/", "handleBase");

//-------------------------------------------------------------------------------------------------
//Get operations

// Main resources:
// get all
$app->get("/generations", "handleGetAllGenerations");
$app->get("/pokemon", "handleGetAllPokemons");
$app->get("/abilities", "handleGetAllAbilities");
$app->get("/moves", "handleGetAllMoves");
$app->get("/trainers", "handleGetAllTrainers");

// get by id
$app->get("/generations/{gens}", "handleGetGenerationById");
$app->get("/pokemon/{pokemonId}", "handleGetPokemonById");
$app->get("/abilities/{abili}", "handleGetAbilityById");
$app->get("/moves/{moves}", "handleGetMoveById");
$app->get("/trainers/{trainers}", "handleGetTrainerById");

// Sub-Resources:
// get all
$app->get("/generations/{generationId}/games", "handleGetGamesByGeneration");
$app->get("/trainers/{trainerId}/pokedex", "handleGetPokedexByTrainer");
$app->get("/games/{gameId}/locations", "handleGetLocationsByGame");
$app->get("/locations/{locationId}/gyms", "handleGetGymsByLocation");
$app->get("/gyms/{gymId}/trainers", "handleGetTrainersByGym");
$app->get("/generations/{generationId}/pokemon", "handleGetPokemonsByGeneration");
$app->get("/pokemon/{pokemonId}/abilities", "handleGetAbilitiesByPokemon");
$app->get("/pokemon/{pokemonId}/moves", "handleGetMovesByPokemon");

// Unsupported operation
$app->get("/generations/{generationId}/games/{gamez}", "handleUnsupportedOperation");
$app->get("/trainers/{trainerId}/pokedex/{pokedex}", "handleUnsupportedOperation");
$app->get("/games/{gameId}/location/{location}", "handleUnsupportedOperation");
$app->get("/locations/{locationsId}/gyms/{gyms}", "handleUnsupportedOperation");
$app->get("/gyms/{gymId}/trainers/{trainerId}", "handleUnsupportedOperation");

// Complex
$app->get("/generations/{generationId}/pokemon/{pokemonId}", "handleGetSpecificPokemonRelatedToGeneration");
$app->get("/pokemon/{pokemonId}/abilities/{pokemonAbility}", "handleGetSpecificAbilitiesRelatedToPokemon");
$app->get("/pokemon/{pokemonId}/moves/{pokemonMove}", "handleGetSpecificMovesRelatedToPokemon");



//-------------------------------------------------------------------------------------------------
//Delete operations

$app->delete("/generations", "handleUnsupportedOperation");
$app->delete("/pokemon", "handleUnsupportedOperation");
$app->delete("/abilities", "handleUnsupportedOperation");
$app->delete("/moves", "handleUnsupportedOperation");
$app->delete("/trainers", "handleUnsupportedOperation");

$app->delete("/moves/{moves}", "deleteOneMove");
$app->delete("/pokemon/{pokemonId}", "deleteOnePokemon");
$app->delete("/abilities/{abili}", "deleteOneAbility");
$app->delete("/generations/{gens}", "deleteOneGeneration");
$app->delete("/trainers/{trainers}", "deleteOneTrainer");

$app->delete("/games/{gamez}", "deleteOneGame");
$app->delete("/gyms/{gyms}", "deleteOneGym");
$app->delete("/locations/{location}", "deleteOneLocation");
$app->delete("/pokedex/{pokedex}", "deleteOnePokedex");
$app->delete("/pokemon_ability/{pokebi}", "deleteOnePokeAbility");
$app->delete("/pokemonMove/{pokemonMoves}", "deleteOnePokemonMove");
$app->delete("/pokemon/{pokemonId}/abilities", "deleteAbilityByPokemon");
$app->delete("/pokemon/{pokemonId}/moves", "deleteMovesByPokemon");

//sub resource with ID
$app->delete("/pokemon/{pokemonId}/abilities/{abilityId}", "handleDeleteSpecificAbilitiesRelatedToPokemon");
$app->delete("/pokemon/{pokemonId}/moves/{moveId}", "handleDeleteSpecificMoveRelatedToPokemon");
$app->delete("/generations/{generationId}/games/{gameId}", "handleDeleteSpecificGameRelatedToGeneration");

$app->delete("/generations/{generationId}/games", "handleUnsupportedOperation");
$app->delete("/trainers/{trainerId}/pokedex", "handleUnsupportedOperation");
$app->delete("/gyms/{gymId}/trainers", "handleUnsupportedOperation");
$app->delete("/games/{gameId}/locations", "handleUnsupportedOperation");
$app->delete("/locations/{locationId}/gyms", "handleUnsupportedOperation");
$app->delete("/generations/{generationId}/pokemon", "handleUnsupportedOperation");

$app->delete("/trainers/{trainerId}/pokedex/{pokedexId}", "handleUnsupportedOperation");
$app->delete("/games/{gameId}/location/{locationId}", "handleUnsupportedOperation");
$app->delete("/locations/{locationsId}/gyms/{gymId}", "handleUnsupportedOperation");
$app->delete("/gyms/{gymId}/trainers/{trainerId}", "handleUnsupportedOperation");
$app->delete("/generations/{generationId}/pokemon/{pokemonId}", "handleUnsupportedOperation");


//-------------------------------------------------------------------------------------------------
//post operations 

//base resources
$app->post("/generations", "handleCreateGeneration");
$app->post("/pokemon", "handleCreatePokemon");
$app->post("/abilities", "handleCreateAbility");
$app->post("/moves", "handleCreateMove");
$app->post("/trainers", "handleCreateTrainer");
$app->post("/token", "handleGetToken");
$app->post("/account", "handleCreateUserAccount");

//base resources - unsupported operations
$app->post("/generations/{generationId}", "handleUnsupportedOperation");
$app->post("/pokemon/{pokemonId}", "handleUnsupportedOperation");
$app->post("/abilities/{abilityId}", "handleUnsupportedOperation");
$app->post("/moves/{moveId}", "handleUnsupportedOperation");
$app->post("/trainers/{trainerId}", "handleUnsupportedOperation");

//dependant resources
$app->post("/generations/{generationId}/games", "handleCreateGame");
$app->post("/trainers/{trainerId}/pokedex", "handleCreatePokedex");

//dependant resources - unsupported operations
$app->post("/games/{gameId}/locations", "handleUnsupportedOperation");
$app->post("/gyms/{gymId}/trainers", "handleUnsupportedOperation");
$app->post("/locations/{locationsId}/gyms", "handleUnsupportedOperation");
$app->post("/generations/{generationId}/pokemon", "handleUnsupportedOperation");
$app->post("/pokemon/{pokemonId}/abilities", "handleUnsupportedOperation");
$app->post("/pokemon/{pokemonId}/moves", "handleUnsupportedOperation");

$app->post("/generations/{generationId}/games/{gameId}", "handleUnsupportedOperation");
$app->post("/trainers/{trainerId}/pokedex/{pokedexId}", "handleUnsupportedOperation");
$app->post("/games/{gameId}/locations/{locationId}", "handleUnsupportedOperation");
$app->post("/locations/{locationsId}/gyms/{gymId}", "handleUnsupportedOperation");
$app->post("/gyms/{gymId}/trainers/{trainerId}", "handleUnsupportedOperation");
$app->post("/generations/{generationId}/pokemon/{pokemonId}", "handleUnsupportedOperation");
$app->post("/pokemon/{pokemonId}/abilities/{abilityId}", "handleUnsupportedOperation");
$app->post("/pokemon/{pokemonId}/moves/{moveId}", "handleUnsupportedOperation");

//-------------------------------------------------------------------------------------------------
//Put Operations

$app->put("/generations", "handleUpdateGeneration");
$app->put("/pokemon", "handleUpdatePokemon");
$app->put("/abilities", "handleUpdateAbility");
$app->put("/moves", "handleUpdateMove");
$app->put("/trainers", "handleUpdateTrainer");

//base resources - unsupported operations
$app->put("/generations/{generationId}", "handleUnsupportedOperation");
$app->put("/pokemon/{pokemonId}", "handleUnsupportedOperation");
$app->put("/abilities/{abilityId}", "handleUnsupportedOperation");
$app->put("/moves/{moveId}", "handleUnsupportedOperation");
$app->put("/trainers/{trainerId}", "handleUnsupportedOperation");

//dependent resources
$app->put("/trainers/{trainerId}/pokedex", "handleUpdatePokedex");

//dependant resources without id
$app->put("/generations/{generationId}/games", "handleUnsupportedOperation");
$app->put("/gyms/{gymId}/trainers", "handleUnsupportedOperation");
$app->put("/games/{gameId}/locations", "handleUnsupportedOperation");
$app->put("/locations/{locationId}/gyms", "handleUnsupportedOperation");
$app->put("/generations/{generationId}/pokemon", "handleUnsupportedOperation");
$app->put("/pokemon/{pokemonId}/abilities", "handleUnsupportedOperation");
$app->put("/pokemon/{pokemonId}/moves", "handleUnsupportedOperation");

//dependant resources with id
$app->put("/generations/{generationId}/games/{gameId}", "handleUnsupportedOperation");
$app->put("/trainers/{trainerId}/pokedex/{pokedexId}", "handleUnsupportedOperation");
$app->put("/games/{gameId}/location/{locationId}", "handleUnsupportedOperation");
$app->put("/locations/{locationsId}/gyms/{gymId}", "handleUnsupportedOperation");
$app->put("/gyms/{gymId}/trainers/{trainerId}", "handleUnsupportedOperation");
$app->put("/generations/{generationId}/pokemon/{pokemonId}", "handleUnsupportedOperation");
$app->put("/pokemon/{pokemonId}/abilities/{abilityId}", "handleUnsupportedOperation");
$app->put("/pokemon/{pokemonId}/moves/{moveId}", "handleUnsupportedOperation");

//not to sure ab this since we can already update trainers through base resource
//$app->put("/gyms/{gymId}/trainers", "callback");

function handleUnsupportedOperation(Request $request, Response $response, array $args) {
    $requested_format = $request->getHeader('Accept');
    
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_code = HTTP_METHOD_NOT_ALLOWED;
        $response_data = json_encode(array("Error" => "unsupportedMethod", "Message" => $request->getMethod(). " method is unsupported on this resource :)"), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
        $response_data = json_encode(getErrorUnsupportedFormat(), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleBase(Request $request, Response $response, array $args) {
    $response_code = HTTP_OK;
    
    $requested_format = $request->getHeader('Accept');
    
    $resp = array(
        "message" => "Welcome to our pokemon api! Below are our resources and subresources.",
        "main resources" => array(
            "generations" => "pokemon-api/generations",
            "pokemon" => "pokemon-api/pokemon",
            "abilities" => "pokemon-api/abilities",
            "moves" => "pokemon-api/moves",
            "trainers" => "pokemon-api/trainers"
        ),
        "subresources" => array(
            "games by generation" => "pokemon-api/generations/{generationId}/games",
            "pokedex by trainers" => "pokemon-api/trainers/{trainerId}/pokedex",
            "locations by games" => "pokemon-api/games/{gameId}/locations",
            "gyms by location" => "pokemon-api/locations/{locationsId}/gyms",
            "trainers as gym leaders" => "pokemon-api/gyms/{gymId}/trainers",
            "pokemon by generation" => "pokemon-api/generation/{generationId}/pokemon",
            "pokemon abilities" => "pokemon-api/pokemon/{pokemonId}/abilities",
            "pokemon moves" => "pokemon-api/pokemon/{pokemonId}/moves"
        )
    );
    
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($resp);
    }
    else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

// Run the app.
$app->run();
