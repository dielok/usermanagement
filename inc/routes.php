<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Helper\Session;
use User\UserController;
use User\UserRepository;

/**
 * HELPER
 */
$session = new Session();

/**
 * Homepage
 */
$app->get('/', function(Request $request, Response $response){
    
    // API - Service
    $data = [
        "API" => "Version ....",
        "Created" => "0000-00-00 00:00:00"
    ];
    return $response->withJson($data, $data['Status']);    
});

// Bedeutet: wir starten eine neue Sitzung. Man muss bei REST wie in einer Ordnerstruktur denken.
// Schauen Sie sich nochmal die REST Spezifikationen an, die ich Ihnen geschickt habe.
// Generiert Token, speichert ihn in Datensatz und gibt ihn in diesem Endpoint auch zurück
/**
 * signin
 */
$app->post('/sessions/', function(Request $request, Response $response) use($session) {

    try{
        $session->start();
        $session->regenerate();
        
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->signin($request->getParsedBody(),$session->session_id());
        
    } catch (Exception $e) {
        $data = [
            "Report" => "Failure",
            "Msg" => "An unexpected error occurred",
            "Status" => 400
        ];
    }
    return $response->withJson($data, $data['Status']);
});

/* returns the session data if token identifies a valid and currently active session */
$app->get('/sessions/[token]', function(Request $request, Response $response) use($session) {
    
    // TODO: log message?
    // $this->logger->info("Slim-Skeleton '/' route");
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

/* #############################################################################
 *                                  Users 
 */

/**
 * register
 */
$app->post('/users/', function(Request $request, Response $response) use($session) {
    
    // CREATE
    try{
        $session->start();
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->register($request->getParsedBody(), $session->session_id());
    } catch (Exception $e) {
        $data = [
            "Report" => "Failure",
            "Msg" => "An unexpected error occurred",
            "Status" => 400
        ];
    }
    return $response->withJson($data, $data['Status']);     
});

/**
 * retrieve
 */
$app->get('/users/[{id}]', function(Request $request, Response $response, $args) {

    try{
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->getUserdata($args['id']);
        $statusCode = $data->status;
    } catch (Exception $e) {
        $data = [
            "Report" => "Failure",
            "Msg" => "An unexpected error occurred",
            "Status" => 400
        ]; 
        $statusCode = $data['Status'];
    }
    return $response->withJson($data, $statusCode);
});

/**
 * update
 */
$app->put('/users/[{id}]', function(Request $request, Response $response) {
    
    // CRU[pdate]D
    
    // TODO: update user account 

});

/**
 * delete
 */
$app->delete('/users/[{id}]', function(Request $request, Response $response) {

    // CRUD[elete]
    
    // TODO: remove user account from whole system (alls dependencies)

});

