<?php
class Router {
    private $routes = [];
    private $middleware = [];
    
    public function get($path, $handler, $middleware = []) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    public function post($path, $handler, $middleware = []) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    private function addRoute($method, $path, $handler, $middleware) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch() {
        // Get and decode URI (handle URL-encoded spaces)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = urldecode($uri);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Calculate base path from script location
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /haris web/index.php
        $scriptDir = dirname($scriptName); // e.g., /haris web
        $basePath = rtrim($scriptDir, '/');
        
        // If basePath is just '/', it means we're in root, so no base path
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Decode base path for comparison (handles spaces in folder names)
        $decodedBasePath = $basePath ? urldecode($basePath) : '';
        
        // Remove base path from URI
        if ($basePath && $basePath !== '/') {
            // Try direct match first
            if (strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            } 
            // Try decoded match (for spaces in folder names like "haris web")
            elseif ($decodedBasePath && strpos($uri, $decodedBasePath) === 0) {
                $uri = substr($uri, strlen($decodedBasePath));
            }
            // Try URL-encoded match
            elseif (strpos($uri, urlencode($basePath)) === 0) {
                $uri = substr($uri, strlen(urlencode($basePath)));
            }
        }
        
        // Handle /index.php or /index requests
        if ($uri === '/index.php' || $uri === '/index') {
            $uri = '/';
        }
        
        // Ensure URI starts with /
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        $uri = rtrim($uri, '/') ?: '/';
        
        // Temporary debug (remove after fixing)
        if (isset($_GET['show_uri'])) {
            die("URI: " . $uri . " | BasePath: " . $basePath . " | DecodedBasePath: " . $decodedBasePath . " | Original: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . " | ScriptName: " . $scriptName);
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    if (!$this->runMiddleware($mw)) {
                        return;
                    }
                }
                
                // Execute handler
                $this->executeHandler($route['handler'], $matches);
                return;
            }
        }
        
        // Debug: Show available routes (remove in production)
        if (isset($_GET['debug'])) {
            echo "<h3>404 - Page Not Found</h3>";
            echo "<p>Requested URI: <strong>" . htmlspecialchars($uri) . "</strong></p>";
            echo "<p>Method: <strong>" . $method . "</strong></p>";
            echo "<h4>Available Routes:</h4><ul>";
            foreach ($this->routes as $route) {
                if ($route['method'] === $method) {
                    echo "<li>" . $route['method'] . " " . htmlspecialchars($route['path']) . "</li>";
                }
            }
            echo "</ul>";
            return;
        }
        
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    private function convertToRegex($path) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function runMiddleware($middleware) {
        if (is_string($middleware)) {
            $middlewareClass = $middleware;
            if (class_exists($middlewareClass)) {
                $mw = new $middlewareClass();
                return $mw->handle();
            }
        } elseif (is_callable($middleware)) {
            return $middleware();
        }
        return true;
    }
    
    private function executeHandler($handler, $params = []) {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            
            // Determine if this is an admin route
            $isAdminRoute = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
            
            // Try different controller paths (admin first if admin route)
            // Check for Admin prefix first (AdminProductController, AdminOrderController)
            $adminControllerName = 'Admin' . $controller;
            $controllerPaths = $isAdminRoute ? [
                'controllers\\admin\\' . $adminControllerName, // AdminProductController, AdminOrderController
                'controllers\\admin\\' . $controller, // Fallback to original name
                'controllers\\' . $controller,
                $controller
            ] : [
                'controllers\\' . $controller,
                'controllers\\admin\\' . $controller,
                $controller
            ];
            
            $controllerClass = null;
            foreach ($controllerPaths as $path) {
                // Convert namespace path to file path
                $filePath = str_replace('\\', '/', $path);
                $filePath = str_replace('controllers/', __DIR__ . '/../controllers/', $filePath);
                $filePath .= '.php';
                
                // Check if file exists first
                if (file_exists($filePath)) {
                    // Load the file if class not already loaded
                    if (!class_exists($path)) {
                        require $filePath;
                    }
                    
                    // Now check if class exists and has the method
                    if (class_exists($path)) {
                        try {
                            $testInstance = new $path();
                            if (method_exists($testInstance, $method)) {
                                $controllerClass = $path;
                                break;
                            }
                        } catch (Exception $e) {
                            // Skip this controller if instantiation fails
                            continue;
                        }
                    }
                } elseif (class_exists($path)) {
                    // Class already loaded, check if it has the method
                    try {
                        $testInstance = new $path();
                        if (method_exists($testInstance, $method)) {
                            $controllerClass = $path;
                            break;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            
            if ($controllerClass) {
                try {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        call_user_func_array([$controllerInstance, $method], $params);
                        return;
                    } else {
                        error_log("Method {$method} not found in {$controllerClass}. Available methods: " . implode(', ', get_class_methods($controllerInstance)));
                        http_response_code(500);
                        echo "500 - Method not found: {$method} in {$controllerClass}";
                        return;
                    }
                } catch (Exception $e) {
                    error_log("Error in {$controllerClass}::{$method}: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    http_response_code(500);
                    echo "500 - Internal Server Error: " . htmlspecialchars($e->getMessage());
                    return;
                } catch (Error $e) {
                    error_log("Fatal error in {$controllerClass}::{$method}: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    http_response_code(500);
                    echo "500 - Fatal Error: " . htmlspecialchars($e->getMessage());
                    return;
                }
            } else {
                error_log("Controller class not found or method missing. Tried: " . implode(', ', $controllerPaths));
                http_response_code(500);
                echo "500 - Controller not found or method missing: {$controller}@{$method}";
                return;
            }
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        http_response_code(500);
        echo "500 - Internal Server Error";
    }
}
