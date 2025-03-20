<?php

namespace App\Core;
use App\Http\Request;
use App\Http\Response;


Class Core
{
    public static function dispatch(array $routes)
    {
        $url = '/';

        isset($_GET['url']) && $url .= $_GET['url'];

        $url !== '/' && $url = rtrim($url, '/');
        $prefix_contoller = 'App\\Controllers\\';
        $route_found = false;
        foreach($routes as $route)
        {    

            $pattern = '#^'. preg_replace('/{id}/','([\w-]+)', $route['path']) .'$#';
            if(preg_match($pattern, $url, $matches)){
                array_shift($matches);
                $route_found = true;

                
                if ($route['method'] !== Request::method()) {
                    Response::json([
                        'error'   => true,
                        'success' => false,
                        'message' => 'Sorry, method not allowed.'
                    ], 405);
                    return;
                }

                [$controller,$action] = explode('@',$route['action']);

                $controller = $prefix_contoller.$controller;
                $extend_controller = new $controller();
                $extend_controller->$action(new Request, new Response, $matches);
            }
        }

        if(!$route_found)
        {
            $controller = $prefix_contoller . 'not_found_controller';
            $extend_controller = new $controller();
            $extend_controller->index(new Request, new Response);

            
        }
    }
}