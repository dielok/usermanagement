<?php
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use Token\TokenController;





// Helper
$userController = new UserController;
$tokenController = new TokenController;





//##############################################################################
/**
 * Homepage
 */
$app->get('/', function(Request $request, Response $response, Array $args){
    return $response->write("API - Usermanagement");
});





//SESSION USERS
/**
 * signin 
 */
$app->post('/sessions/', function(Request $request, Response $response, Array $args) use($userController) {
    $userController->init_DB($this->db);
    $userController->signinSession();
    // log
});

/**
 * exists Session
 */
$app->get('/sessions/[{token}]', function(Request $request, Response $response, Array $args) use($tokenController) {
    $tokenController->init_DB($this->db);
    $jsonInfo = $tokenController->findToken($args['token']);

    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    // log
});

/**
 * delete Session
 */
$app->delete('/sessions/[{token}]', function(Request $request, Response $response, Array $args) use($tokenController) {
    $tokenController->init_DB($this->db);
    $jsonInfo = $tokenController->delete($args['token']);
    
    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    // log
});





// CRUD USERS
/**
 * create
 */
$app->post('/users/', function(Request $request, Response $response, Array $args) use($userController,$tokenController) {
    $userController->init_DB($this->db);
    $jsonInfo= $userController->create($request->getParsedBody());  
    
    if(intval($jsonInfo['Error'])==0){
        $tokenController->init_DB($this->db);
        $session = $tokenController->create((string)$jsonInfo['Token']);  
    }
    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
});

/**
 * read
 */
$app->get('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController) {
    $userController->init_DB($this->db);
    $jsonInfo = $userController->read($args['id']);
    
    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
});

/**
 * update
 */
$app->put('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController) {
    $userController->init_DB($this->db);
    $jsonInfo = $userController->update($args['id'], $request->getParsedBody());
    
    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
});

/**
 * delete
 */
$app->delete('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController,$tokenController) {
    $userController->init_DB($this->db);
    $jsonInfo = $userController->delete($args['id'], $request->getParsedBody());
    
    if(intval($jsonInfo['UserId']) > 0){
        $tokenController->init_DB($this->db);
        $session = $tokenController->delete($tokenController->headerToken());
    }
    return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
});