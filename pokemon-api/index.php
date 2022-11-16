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

// Define app routes.
$app->get('/hello/{your_name}', function (Request $request, Response $response, $args) {
    //var_dump($args);
    $response->getBody()->write("Hello!" . $args["your_name"]);
    return $response;
});

// Run the app.
$app->run();
