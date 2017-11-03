<?php
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use User\UserRepository;

$app->get('/', function(Request $request, Response $response){
    $token = "token1234";
    $_SERVER['HTTP_AUTHORIZATION'] = $token;
    var_dump($_SERVER);
    return $response;
});

/**
 * register
 */
$app->post('/user/register', function(Request $request, Response $response){
    
    try{
        $userController = new UserController(new UserRepository($this->db));
        $data = $userController->register($request->getParsedBody());
    } catch (Exception $e) {
        $data['Status'] = 400;
    }
    /**
     * TODO: later on a class
     */
    switch ($data['Status']){
        case 200:
            return $response->withStatus(200)->withJson($data);
        case 201:
            return $response->withStatus(201)->withJson($data);
        case 400:
        default:
            return $response->withStatus(404)->withJson(["Report"=>"Failure","Msg"=>"not found"]);
    }
});

/**
 * signin
 */
$app->post('/user/signin', function(Request $request, Response $response){

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
            return $response->withStatus(200)->withJson($data);
        case 201:
            return $response->withStatus(201)->withJson($data);
        case 400:
        default:
            return $response->withStatus(404)->withJson(["Report"=>"Failure","Msg"=>"not found"]);
    }
});
