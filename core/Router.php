<?php

class Router
{
    public function run()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : 'login/index';
        $url = rtrim($url, '/');
        $urlParts = explode('/', $url);

        $controllerName = ucfirst($urlParts[0]) . 'Controller';
        $method = isset($urlParts[1]) ? $urlParts[1] : 'index';
        $params = array_slice($urlParts, 2);

        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            if (class_exists($controllerName)) {
                $controller = new $controllerName();

                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                } else {
                    $this->handleError('404');
                }
            } else {
                $this->handleError('500');
            }
        } else {
            $this->handleError('404');
        }
    }

    private function handleError($code)
    {
        http_response_code((int)$code);
        require_once __DIR__ . '/../app/controllers/ErrorController.php';
        $errorController = new ErrorController();
        $errorController->index($code);
        exit;
    }
}