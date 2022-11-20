<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once './includes/app_constants.php';
require_once './includes/helpers/helper_functions.php';

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

//-- Step 6)
// TODO: And here we define app routes.
//$app->get("/artists", "handleGetAllArtists");
$app->get("/pokemon/{pokemonId}", "handleGetPokemonById");
$app->get("/abilities/{abili}", "handleGetAbilityById");
$app->get("/game/{gamez}", "handleGetGameById");
$app->get("/generation/{gens}", "handleGetGenerationById");
$app->get("/gym/{gyms}", "handleGetGymById");
$app->get("/location/{location}", "handleGetLocationById");
$app->get("/pokedex/{pokedex}", "handleGetPokedexById");
$app->get("/pokemon_ability/{pokebi}", "handleGetPokeAbilityById");
$app->get("/trainer/{trainers}", "handleGetTrainerById");
$app->get("/moves/{moves}", "handleGetMoveById");
$app->get("/pokemonMove/{pokemonMoves}", "handleGetPokemonMoveById");


$app->delete("/pokemon/{pokemonId}", "deleteOnePokemon");
$app->delete("/abilities/{abili}", "deleteOneAbility");
$app->delete("/game/{gamez}", "deleteOneGame");
$app->delete("/generation/{gens}", "deleteOneGeneration");
$app->delete("/gym/{gyms}", "deleteOneGym");
$app->delete("/location/{location}", "deleteOneLocation");
$app->delete("/pokedex/{pokedex}", "deleteOnePokedex");
$app->delete("/pokemon_ability/{pokebi}", "deleteOnePokeAbility");
$app->delete("/trainer/{trainers}", "deleteOneTrainer");
$app->delete("/moves/{moves}", "deleteOneMove");
$app->delete("/pokemonMove/{pokemonMoves}", "deleteOnePokemonMove");


//-------------------------------------------------------------------------------------------------
//post operations 

//base resources
$app->post("/generation", "handleCreateGeneration");
$app->post("/pokemon", "handleCreatePokemon");
$app->post("/abilities", "handleCreateAbility");
$app->post("/moves", "handleCreateMove");
$app->post("/trainer", "handleCreateTrainer");

//to be mapped
//$app->post("/generation/{generationId}", "handleUnsupportedOperation");
//$app->post("/pokemon/{pokemonId}", "handleUnsupportedOperation");
//$app->post("/abilities/{abilityId}", "handleUnsupportedOperation");
//$app->post("/moves/{moveId}", "handleUnsupportedOperation");
//$app->post("/trainer/{trainerId}", "handleUnsupportedOperation");

//dependant resources
$app->post("/generations/{generationId}/games", "handleCreateGame");
$app->post("/trainers/{trainerId}/pokedex", "handleCreatePokedex");

// needs to be reevaluated for priority
//$app->post("/gyms/{gymId}/trainers", "handleCreateTrainer");

//to be mapped
//$app->post("/games/{gameId}/locations", "handleUnsupportedOperation");
//$app->post("/locations/{locationsId}/gyms", "handleUnsupportedOperation");
//$app->post("/generation/{generationId}/pokemon", "handleUnsupportedOperation");
//$app->post("/pokemon/{pokemonId}/abilities", "handleUnsupportedOperation");
//$app->post("/pokemon/{pokemonId}/moves", "handleUnsupportedOperation");

//to be mapped
//$app->post("/generations/{generationId}/games/{gameId}", "handleUnsupportedOperation");
//$app->post("/trainers/{trainerId}/pokedex/{pokedexId}", "handleUnsupportedOperation");
//$app->post("/games/{gameId}/locations/{locationId}", "handleUnsupportedOperation");
//$app->post("/locations/{locationsId}/gyms/{gymId}", "handleUnsupportedOperation");
//$app->post("/gyms/{gymId}/trainers/{trainerId}", "handleUnsupportedOperation");
//$app->post("/generation/{generationId}/pokemon/{pokemonId}", "handleUnsupportedOperation");
//$app->post("/pokemon/{pokemonId}/abilities/{abilityId}", "handleUnsupportedOperation");
//$app->post("/pokemon/{pokemonId}/moves/{moveId}", "handleUnsupportedOperation");


    


























// Define app routes.
$app->get('/hello/{your_name}', function (Request $request, Response $response, $args) {
    //var_dump($args);
    $response->getBody()->write("Hello!" . $args["your_name"]);
    return $response;
});

function handleUnsupportedOperation(Request $request, Response $response, array $args) {
    $requested_format = $request->getHeader('Accept');
    
    if (isset($requested_format[0]) && $requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_code = HTTP_METHOD_NOT_ALLOWED;
        $response_data = json_encode(getErrorUnsupportedMethod($request->getMethod), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    else {
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
        $response_data = json_encode(getErrorUnsupportedFormat(), JSON_INVALID_UTF8_SUBSTITUTE);
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

// Run the app.
$app->run();
