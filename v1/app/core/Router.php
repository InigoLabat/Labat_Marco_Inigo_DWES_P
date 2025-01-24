<?php 
require_once __DIR__ . '/../../config/conf.php' ;

class Router {
    
    protected $routes = [];

    public function __construct() {
        $this->routes = [
            '/' => 'TermostatoController@index',
            '/updatetemperatura' => 'TermostatoController@update'
        ];
    }

    public function add($route, $params){
        $this->routes[$route] = $params;
    }

    /**
     * Coincide la solicitud con la ruta correspondiente.
     *
     * Este método toma la URI solicitada, elimina el prefijo de la URL base,
     * y luego compara la URI resultante con las rutas definidas. Si encuentra
     * una coincidencia, instancia el controlador correspondiente y llama al
     * método especificado. Si no encuentra una coincidencia o si el controlador
     * o el método no existen, envía una respuesta 404.
     *
     * @param string $uri La URI solicitada.
     */
    public function match($uri) {

        // Eliminar el prefijo de la URL usando la constante BASE_URL definida en el fichero conf.php
        $uri = str_replace(BASE_URL, '', $uri);

        // Extraer solo la ruta del URL
        $uri = parse_url($uri, PHP_URL_PATH);
    
        // Obtener el método HTTP de la solicitud
        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        // Recorrer todas las rutas definidas
        foreach ($this->routes as $route => $params) {

            // Reemplazar los parámetros en la ruta con expresiones regulares
            $routePattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)?', $route);

            // Escapar las barras para la expresión regular
            $routePattern = str_replace('/', '\/', $routePattern);

            // Comprobar si la URI coincide con el patrón de la ruta
            if (preg_match('/^' . $routePattern . '$/', $uri, $matches)) {

                // Eliminar el primer elemento del array de coincidencias
                array_shift($matches);
    
                // Dividir el controlador y el método
                list($controller, $method) = explode('@', $params);
    
                // TODO: Ruta completa al archivo del controlador
                $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
    
                // Verificar si el archivo del controlador existe
                if (file_exists($controllerFile)) {

                    require_once $controllerFile;
    
                    // Verificar si la clase existe sin espacio de nombres
                    if (class_exists($controller) && method_exists($controller, $method)) {

                        // Instanciar el controlador
                        $controllerInstance = new $controller();
    
                        // Manejar los datos JSON para métodos POST, PUT y DELETE
                        if (in_array($requestMethod, ['POST', 'PUT', 'DELETE'])) {

                            $input = json_decode(file_get_contents('php://input'), true);
                            $matches[] = $input;
                        }
    
                        // Llamar al método del controlador con los parámetros
                        call_user_func_array([$controllerInstance, $method], $matches);
                        return;
                    }
                }
                
            } 
        }
        echo $this->sendNotFound();
    
    }
    

    private function sendNotFound() {
        header('Content-Type: application/json');
        http_response_code(404);
        $response = [
            'status' => 'not succed',
            'codigo' => 404,
            'mensaje' => '404 Not Found'
        ];
        return json_encode($response, JSON_PRETTY_PRINT);
    }
}

?>