<?php
// see app.php for application logic

if (!file_exists('config.php')) die('APP not configured');

$config = require 'config.php';

// AUTOLOADING

spl_autoload_register(function ($class){
    require __DIR__ . '/lib/' . str_replace('\\', '/', $class) . '.php';
});

// ERRORS
require __DIR__ . '/lib/tracy.php';
use \Tracy\Debugger;
Debugger::enable(!empty($config['show_debug']) ? Debugger::DEVELOPMENT : Debugger::DETECT);
Debugger::$showBar = false;
Debugger::$errorTemplate = __DIR__ . '/tpl/500.html';

// FRAMEWORK API

$routeCollector = new FastRoute\RouteCollector(new FastRoute\RouteParser\Std(), new FastRoute\DataGenerator\GroupCountBased());

function user()
{
    return $_SESSION['user'] ?? false;
}

// TODO - requireLogin

$dependencies = $singletons = [];

function di($service)
{
    global $dependencies, $singletons;
    if (isset($dependencies[$service])) {
        return $dependencies[$service];
    } elseif (isset($singletons[$service])) {
        $dependencies[$service] = $singletons[$service]();
        return $dependencies[$service];
    } else {
        throw new Exception('Dependency ' . $service . ' not found!');
    }
}

function flash($msg, $type='info')
{
    $_SESSION['flashes'][$type][] = $msg;
}

function redirect($url, $status = 302)
{
    if (strpos($url, '/')===0) {
        // TODO - zde nemít zadrátované HTTP
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
    }
    return response('See ' . $url, 302, ['Location'=>$url]);
}

function render($view, $data=[])
{
    $tplFile = __DIR__ . '/tpl/' . $view . '.php';
    if (file_exists($tplFile)) {
        extract($data, EXTR_SKIP);
        ob_start();
        include $tplFile;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    } else {
        throw new Exception('Template '.$view.' not found in file '.$tplFile);
    }

}

function response($body, $status=200, $headers=[])
{
    return new Nyholm\Psr7\Response($status, $headers, $body);
}

function jsonResponse($data, $status=200)
{
    return response(json_encode($data), $status, ['Content-type'=>'application/json']);
}

function notFound()
{
    $err404 = file_get_contents(__DIR__ . '/tpl/404.html');
    return response($err404, 404);
}

function route($method, $url, $callback)
{
    global $routeCollector;
    if (empty($method)) $method = ['GET', 'POST'];
    $routeCollector->addRoute($method, $url, $callback);
}



// ROUTES
require 'app.php';

// finally running the APP

session_start(); // TODO - nakonfit session

$routeDispatcher = new FastRoute\Dispatcher\GroupCountBased($routeCollector->getData());
$request = new Nyholm\Psr7\ServerRequest($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), getallheaders());
if ($request->getMethod()=='POST') {
    $request = $request->withParsedBody($_POST);
}
if ($request->getMethod()=='POST' && in_array('application/json', $request->getHeader('Content-Type'))) {
    $rawPostData = file_get_contents('php://input');
    $request = $request->withParsedBody(json_decode($rawPostData, true));
}

$routeInfo = $routeDispatcher->dispatch($request->getMethod(), $request->getUri());
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        require __DIR__ . '/tpl/404.html';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        throw new Exception('Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // todo - zde přidat atributy
        ob_start();
        $response = $handler($request, $vars);
        $output = ob_get_clean();
        if (empty($response) && !empty($output)) $response = $output;
        if (is_string($response)) $response = response($response);

        $emmitter = new Narrowspark\HttpEmitter\SapiEmitter();
        $emmitter->emit($response);

        break;
}