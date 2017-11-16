<?php
namespace Middleware;

use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
/**
 * Description of Middleware
 *
 * @author martinleue
 */
class Middleware
{
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        try{
            //$response->getBody()->write('BEFORE');
            $pdo = new PDO('mysql:host=localhost;dbname=usermanagement', 'root', 'root');
            $tokenController = new \Token\TokenController();
            $tokenController->init_DB($pdo);
            $headerToken = $tokenController->headerToken();

            if($headerToken){
                $db_token = $tokenController->findToken($headerToken);
                if(!empty($db_token['Token'])){
                    $response = $next($request, $response);
                    return $response;
                }
            }
            return $response->withStatus(401)->write("Unauthorized. Token missing.");
        } catch (\Slim\Exception $e){
            return $response->withStatus(400)->write("Anwendungsfehler, wir kümmern uns gerade um das Problem, versuchen Sie es später nocheinmal!");
        }
    }
}