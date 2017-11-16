<?php
use Slim\Http\Request;
use Slim\Http\Response;
use User\UserController;
use Token\TokenController;
use Log\LogController;
use Middleware\Middleware;





// Helper 
// Klassen
$userController  = new UserController;
$tokenController = new TokenController;
$logController   = new LogController();










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
$app->post('/sessions/', function(Request $request, Response $response, Array $args) use($userController,$tokenController,$logController) {
    try {
        $tokenController->init_DB($this->db);
        $userController->init_DB($this->db);
        $logController->init_DB($this->db);
        $jsonInfo = $userController->signinSession($request->getParsedBody(), $tokenController->newToken());

        if(intval($jsonInfo['Error'])==0){
            // is ip or token true | token = oldToken
            $session = $tokenController->create((string)$jsonInfo['Token']);  
            if($session['error'] == true){
                $jsonInfo['Token'] = $session['token'];
            }
        }
        // log
        $logController->createLog();

        return $response->withJson($jsonInfo , intval($jsonInfo['Status']));
    }
    catch (Exception $e) {
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());

/**
 * exists Session
 */
$app->get('/sessions/[{token}]', function(Request $request, Response $response, Array $args) use($tokenController,$logController) {
    try{
        $tokenController->init_DB($this->db);
        $logController->init_DB($this->db);
        $jsonInfo = $tokenController->findToken($args['token']);

        // log
        $logController->createLog();

        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch (Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());;

/**
 * delete Session
 */
$app->delete('/sessions/[{token}]', function(Request $request, Response $response, Array $args) use($tokenController,$logController) {
    try{
        $tokenController->init_DB($this->db);
        $logController->init_DB($this->db);
        $jsonInfo = $tokenController->delete($args['token']);

        // log
        $logController->createLog();

        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch(Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());













/* users */


/**
 * create
 */
$app->post('/users/', function(Request $request, Response $response, Array $args) use($userController,$tokenController) {
    try{
        $tokenController->init_DB($this->db);
        $userController->init_DB($this->db);
        $jsonInfo= $userController->create($request->getParsedBody(), $tokenController->newToken());  

        if(intval($jsonInfo['Error'])==0){ 
            $session = $tokenController->create((string)$jsonInfo['Token']); 
            if($session['error'] == true){
                $jsonInfo['Token'] = $session['token'];
            }
        }
        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch(Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());

/**
 * read
 */
$app->get('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController) {
    try{
        $userController->init_DB($this->db);
        $jsonInfo = $userController->read($args['id']);

        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch(Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());

/**
 * update
 */
$app->put('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController) {
    try{
        $userController->init_DB($this->db);
        $jsonInfo = $userController->update($args['id'], $request->getParsedBody());

        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch(Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());

/**
 * delete
 */
$app->delete('/users/[{id}]', function(Request $request, Response $response, Array $args) use($userController,$tokenController) {
    try{
        $tokenController->init_DB($this->db);
        $userController->init_DB($this->db);
        $jsonInfo = $userController->delete($args['id'], $request->getParsedBody());

        if(intval($jsonInfo['UserId']) > 0){
            // token delete
            $tokenController->delete($tokenController->headerToken());
        }
        return $response->withJson($jsonInfo, intval($jsonInfo['Status']));
    }
    catch(Exception $e){
        return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
    }
})->add(new Middleware());