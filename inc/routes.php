<?php
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use User\UserRepository;

$app->get('/', function(Request $request, Response $response){
    
    // Hier keinen Token
    
    // Als Wurzelverzeichnis höchsten API Infos
    
    return $response;
    
});


/**
 * signin
 */


// Bedeutet: wir starten eine neue Sitzung. Man muss bei REST wie in einer Ordnerstruktur denken.
// Schauen Sie sich nochmal die REST Spezifikationen an, die ich Ihnen geschickt habe.
// Generiert Token, speichert ihn in Datensatz und gibt ihn in diesem Endpoint auch zurück
$app->post('/sessions/', function(Request $request, Response $response){

    try{
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->signin($request->getParsedBody());
    } catch (Exception $e) {
        $data['Status'] = 400;
    }
    
    /**
     * TODO: later on a class
     */
    switch ($data['Status']){
        case 200:
            return $response->withStatus(200)->withJson($data); // Hier sollte 200 nie auftreten. Der Erfolgsfall ist 201 (CREATED) siehe https://de.wikipedia.org/wiki/HTTP-Statuscode#2xx_.E2.80.93_Erfolgreiche_Operation
        case 201:
            return $response->withStatus(201)->withJson($data);
        case 400:
        default:
            return $response->withStatus(404)->withJson(["Report"=>"Failure","Msg"=>"not found"]);
    }
});

/* returns the session data if token identifies a valid and currently active session */
$app->get('/sessions/[token]', function(Request $request, Response $response) {
    
    // TODO: log message?
    // $this->logger->info("Slim-Skeleton '/' route");
    
    // Build JSON response
    $data = [
        'started_at' => "88.88.8888 88:88:88",
        'user' => 88,
        'token' => "GJrgj345Fr&df3" // Was auch immer - selbstverständlich: Kommt aus Datenbank
    ];
    
    return $response->withJson($data, 200);
    
});

/* deletes a currently active session. */
$app->delete('/sessions/[token]', function(Request $request, Response $response) {

    // TODO: Means: simply removes the token from the dataset and return an acknowlegement

});

/* Users */

/**
 * register
 */

// Was bedeutet "registrieren"? Einen neuen User anlegen... Also statt Endpoint "/register/" eher:

$app->post('/users/', function(Request $request, Response $response){
    
    // C[reate]RUD
    
    try{
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->register($request->getParsedBody());
    } catch (Exception $e) {
        $data['Status'] = 400;
    }

    switch ($data['Status']){
        case 200:
            // Hier sollte 200 nie auftreten. Der Erfolgsfall ist 201 (CREATED) siehe https://de.wikipedia.org/wiki/HTTP-Statuscode#2xx_.E2.80.93_Erfolgreiche_Operation
            return $response->withStatus(200)->withJson($data);
        case 201:
            return $response->withStatus(201)->withJson($data);
        case 400:
        default:
            return $response->withStatus(404)->withJson(["Report"=>"Failure","Msg"=>"not found"]);
    }
    
    // Wenn es so funktioniert, machen Sie das so
    
});

$app->get('/users/[{id}]', function(Request $request, Response $response) {

    // CR[eceive]UD
    
    // TODO: respond with user object

});

$app->put('/users/[{id}]', function(Request $request, Response $response) {
    
    // CRU[pdate]D
    
    // TODO: update user account 

});

$app->delete('/users/[{id}]', function(Request $request, Response $response) {

    // CRUD[elete]
    
    // TODO: remove user account from whole system (alls dependencies)

});

