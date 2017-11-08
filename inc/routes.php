<?php
use Helper\Token;
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use User\UserRepository;
use Middleware\TokenAuth;
use Session\SessionRepository;
use Session\SessionController;

/**
 * HELPER
 */
$token = new Token();
// Later a Class
$data = [
    "Report" => "Failure",
    "Msg" => "An unexpected error occurred",
    "Status" => 400
];

/**
 * Homepage
 */
$app->get('/', function(Request $request, Response $response){  
    // API - Service
    $data = [
        "API" => "Version ....",
        "Created" => "0000-00-00 00:00:00"
    ];
    return $response->withJson($data, 200);
    //var_dump(getallHeaders()['Token']);
    //return $response;
});



/* #############################################################################
 * Sessions 
 * 
*/

/**
 * signin
 */
$app->post('/sessions/', function(Request $request, Response $response) use($token,$data) {
    try{        
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->signin($request->getParsedBody(),$token->newToken());
        $sessionController = new SessionController(new SessionRepository($this->db));   
        $session = $sessionController->sessionUpdate($data['UserId'], $data['Token']);
    } catch (Exception $e) {}
    return $response->withJson($data, $data['Status']);
});

/**
 * True Session
 */
$app->get('/sessions/[{token}]', function(Request $request, Response $response, $args) use($data) {
    
    // TODO: log message?
    try{        
        $sessionController = new SessionController(new SessionRepository($this->db));
        $data = $sessionController->checkToken($args['token']);
    } catch (Exception $e) {}
    return $response->withJson($data, $data['Status']);   
})->add(new TokenAuth());

/**
 * Delete Session
 */
$app->delete('/sessions/[{token}]', function(Request $request, Response $response, $args) use($data) {

    try{        
        $sessionController = new SessionController(new SessionRepository($this->db));
        $data = $sessionController->deleteSession($args['token']);
    } catch (Exception $e) {}
    return $response->withJson($data, $data['Status']);
})->add(new TokenAuth());



/* #############################################################################
 * Users 
 * 
*/

/*
 * create
 */
$app->post('/users/', function(Request $request, Response $response) use($token,$data) {   
    try{
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->register($request->getParsedBody(), $token->newToken());
        $sessionController = new SessionController(new SessionRepository($this->db));   
        $session = $sessionController->sessionInsert($data['UserId'], $data['Token']);
    } catch (Exception $e) {}
    return $response->withJson($data, $data['Status']);     
});

/**
 * retrieve
 */
$app->get('/users/[{id}]', function(Request $request, Response $response, $args) use($data) {
    try{
        $sessionController = new SessionController(new SessionRepository($this->db));   
        $session = $sessionController->checkSession($args['id']);
        if($session){
            $userController = new UserController(new UserRepository($this->db));
            $data = $userController->getUserData($args['id']); 
        }
        $statusCode = $data->status;
    } catch (Exception $e) {
        $statusCode = $data['Status'];
    }
    return $response->withJson($data, $statusCode);
})->add(new TokenAuth());

/**
 * update
 */
$app->put('/users/[{id}]', function(Request $request, Response $response, $args) use($data) {
    try{
        $sessionController = new SessionController(new SessionRepository($this->db));   
        $session = $sessionController->checkSession($args['id']);
        if($session){
            $userController = new UserController(new UserRepository($this->db));
            $data = $userController->userUpdate($args['id'],$request->getParsedBody());
        }
        $statusCode = $data->status;
    } catch (Exception $e) {
        $statusCode = $data['Status'];
    }
    return $response->withJson($data, $statusCode);
})->add(new TokenAuth());

/**
 * delete
 */
$app->delete('/users/[{id}]', function(Request $request, Response $response, $args) use($data) {   
    try{   
        $sessionController = new SessionController(new SessionRepository($this->db));   
        $session = $sessionController->checkSession($args['id']);
        if($session){
            $userController = new UserController(new UserRepository($this->db));
            $data = $userController->deleteUser($args['id'], $request->getParsedBody());
            $session = $sessionController->deleteUserSession($args['id']);
        }
    } catch (Exception $e) {}
    return $response->withJson($data, $data['Status']);
})->add(new TokenAuth());

