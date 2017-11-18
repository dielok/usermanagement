<?php
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use Token\TokenController;
use Log\LogController;


//##############################################################################
/**
 * Homepage
 */
$app->get('/', function(Request $request, Response $response, Array $args){
    return $response->write("API - Usermanagement");
});



/* sessions */

/**
 * signin 
 */
$app->post('/sessions/', function(Request $request, Response $response, Array $args){
    try {
        $tokenController = new TokenController($this->db);
        $userController  = new UserController($this->db);
        $logController   = new LogController($this->db);
        
        $logController->createLog();
        
        if($tokenController->read($tokenController->headerToken())){
            $user = $userController->signin($request->getParsedBody(), $tokenController->newToken());
            $tokenController->delete($tokenController->headerToken());
            $tokenController->create($user['token']);
            return $response->withJson($user, 201);    
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});

/**
 * exists Session
 */
$app->get('/sessions/[{token}]', function(Request $request, Response $response, Array $args){
    try{
        $tokenController = new TokenController($this->db);
        $logController   = new LogController($this->db);
        
        $logController->createLog();
        
        if($tokenController->read($tokenController->headerToken())){
            $token = $tokenController->read($args['token']);
            return $response->withJson($token, 201); 
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});

/**
 * delete Session
 */
$app->delete('/sessions/[{token}]', function(Request $request, Response $response, Array $args){
    try{
        $tokenController = new TokenController($this->db);
        $logController   = new LogController($this->db);
        
        $logController->createLog();
        
        if($tokenController->read($tokenController->headerToken())){
            $token = $tokenController->delete($args['token']);
            return $response->withJson($token, 201);
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});



/* users */

/**
 * create
 */
$app->post('/users/', function(Request $request, Response $response, Array $args) {
    try{
        $tokenController = new TokenController($this->db);
        $userController  = new UserController($this->db);
        $logController   = new LogController($this->db);
        
        $logController->createLog();

        $user  = $userController->create($request->getParsedBody(), $tokenController->newToken());  
        $tokenController->create($user['token']);
        return $response->withJson($user, 201);
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});

/**
 * read
 */
$app->get('/users/[{email}]', function(Request $request, Response $response, Array $args){
    try{
        $userController  = new UserController($this->db);
        $logController   = new LogController($this->db);
        $tokenController = new TokenController($this->db);
        
        $logController->createLog();

        if($tokenController->read($tokenController->headerToken())){
            $user = $userController->read($args['email']);  
            return $response->withJson($user, 201);
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});

/**
 * update
 */
$app->put('/users/', function(Request $request, Response $response){
    try{
        $userController  = new UserController($this->db);
        $logController   = new LogController($this->db);
        $tokenController = new TokenController($this->db);
        
        $logController->createLog();

        if($tokenController->read($tokenController->headerToken())){
            $user  = $userController->update($request->getParsedBody()); 
            return $response->withJson($user, 201);
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});

/**
 * delete
 */
$app->delete('/users/', function(Request $request, Response $response){
    try{
        $userController  = new UserController($this->db);
        $logController   = new LogController($this->db);
        $tokenController = new TokenController($this->db);
        
        $logController->createLog();

        if($tokenController->read($tokenController->headerToken())){
            $user  = $userController->delete($request->getParsedBody(),$tokenController->headerToken());
            $tokenController->delete($user['token']);
            return $response->withJson($user, 201);
        }throw new Exception("The Token is not valid!");
    }
    catch (Exception $e) {
        $answer = [
            'message' => $e->getMessage()
        ];
        return $response->withJson($answer, 418);
    }
});