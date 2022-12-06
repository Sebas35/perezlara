<?php

namespace App\Traits\Models;

use ReflectionClass;

trait TConstruct
{
    private function construct() :void
    {
        if (!empty($args = func_get_args())) {
            $end = end($args);
            if(is_string($end) && str_contains($end, '__construct')) {
                method_exists($this, $method = array_splice($args,count($args) - 1 ,1)[0]);
            }elseif (!method_exists($this, $method = '__construct' . func_num_args())) {
                $methods = (new ReflectionClass($this)) -> getMethods();
                $constructs_params = [];
                $methods_possible = [];
                $type_args = array_map(function ($value) {
                    $type = gettype($value);
                    return match ($type) {
                        'object' => get_class($value),
                        'integer' => 'int',
                        default => $type,
                    };
                }, $args);
                $methods = array_filter($methods, function ($reflectionMethod) use ($args){
                    return preg_match('/(__construct([a-zA-Z_]+))/', $reflectionMethod -> getName()) &&
                           $reflectionMethod -> getNumberOfParameters() === count($args);
                });
                foreach ($methods as $method) {
                    foreach ($method -> getParameters() as $parameter) {
                        $constructs_params[$method -> getName()][] = explode('|', $parameter -> getType());
                    }
                }
                foreach ($constructs_params as $index => $construct) {
                    $c = 0;
                    foreach ($construct as $index2 => $params) {
                        for ($i = 0; $i < count($params); $i++) {
                            if ($params[$i] === $type_args[$index2]) {
                                $c++;
                                break;
                            }
                        }
                    }
                    $methods_possible[$index] = $c;
                }
                $method = array_search(max($methods_possible), $methods_possible);
            }
            call_user_func_array([$this, $method], $args);
        }
    }
}
