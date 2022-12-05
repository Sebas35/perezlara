<?php

namespace Core;

use Exception;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

class FrontController
{
    private string $action;

    public function __construct(string $action)
    {
        $this -> action = $action;
    }

    public function run() :void
    {
        $routes = require_once 'routes/app.php';
        if (array_key_exists($this -> action, $routes)) {
            $this -> getView($routes[$this -> action]);
        }elseif (array_key_exists(strtolower($this -> action), $routes)) {
            header('Location: ' . strtolower($_SERVER['REQUEST_URI']));
        }elseif (array_key_exists($key = dirname($this -> action), $routes)) {
            $controller = $routes[$key];
            $this -> getController(is_array($controller) ? $controller[0] : $controller, basename($this -> action));
        }elseif ($this -> action === '403') {
            require_once error('403.php');
        }else {
            require_once error('404.php');
        }
    }

    public function getView(string|array $url) :void
    {
        if (is_array($url)) {
            $info_view = $url[1];
            if (is_array($info_view)) {
                $view = view($info_view[0] . '.php');
                if (array_key_exists(1, $info_view)) {
                    $method = $info_view[1];
                    (new ($url[0])()) -> $method($view);
                }else {
                    (new ($url[0])()) -> view($view);
                }
            }else {
                require_once view($info_view . '.php');
            }
        }else {
            require_once view($url . '.php');
        }
    }

    public function data(array $data) :array
    {
        $data = (array_map(function ($value){
            $json = json_decode($value);
            return json_last_error() == JSON_ERROR_NONE ? $json : $value;
        }, $data));
        return $this -> combine($data);
    }

    public function files(array $files) :array
    {
        return array_map(function ($file){
            return array_filter($file, function ($value, $index){
                return in_array($index, ['id', 'name', 'tmp_name', 'type']);
            }, ARRAY_FILTER_USE_BOTH);
        }, $files);
    }

    public function combine(array $array) :array
    {
        foreach ($array as $index => $value) {
            if (is_array($value) || is_object($value)) {
                $array[$index] = $this -> combine((array)$value);
            }elseif (is_string($value) && is_numeric($value)) {
                $array[$index] = str_contains($value, '.') ? floatval($value) : intval($value);
            }elseif (preg_match('/(file)/i', $index)) {
                if (count($_FILES) > 0) {
                    $files = $_FILES;
                    foreach (array_keys($array) as $index2 => $array_key) {
                        if (str_contains($array_key, 'file_id')) {
                            $id_file = $array[$array_key];
                            $files = array_map(function ($file) use ($id_file){
                                return ['id' => $id_file] + $file;
                            }, $files);
                            array_splice($array, $index2, 1);
                            break;
                        }
                    }
                    $leaked_files = $this -> files($files);
                    if (count($leaked_files) > 1) {
                        $array[$index] = $leaked_files;
                    }else {
                        $first_value = reset($leaked_files);
                        $array[$index] = str_contains($index, 'collection') ? ['file0' => $first_value] : $first_value;
                    }
                }
            }
        }
        return $array;
    }

    public function getTypeParameters(string $class, string $method, ReflectionParameter $parameter) :array
    {
        $parameters = [];
        foreach (preg_split("/[|?]/", strval($parameter -> getType())) as $type) {
            if (class_exists($type)) {
                $parameters['classes'][] = $type;
            }elseif ($type === 'array') {
                $collection = require 'App/collection.php';
                if (array_key_exists($class, $collection) && in_array($method, $collection[$class][0])) {
                    $obj_collection = $collection[$class][1];
                    if (is_array($obj_collection)) {
                        $parameter_name = $parameter -> getName();
                        foreach ($obj_collection as $index => $value) {
                            if ($index === $parameter_name) {
                                $parameters['collection'] = $value;
                            }
                        }
                    }else {
                        $parameters['collection'] = $collection[$class][1];
                    }
                }else {
                    $parameters['others'][] = $type;
                }
            }else {
                $parameters['others'][] = $type;
            }
        }
        return $parameters;
    }

    public function getClass(int|string $info_data, array $parameters) :array|null|string
    {
        try {
            if (str_contains($info_data, 'collection')) {
                $class = [$parameters['collection'], true];
            }elseif (count($parameters) > 1) {
                $classes = $parameters['classes'];
                for ($i = 0; $i < count($classes); $i++) {
                    if (str_contains($classes[$i], $info_data)) {
                        $class = [$classes[$i]];
                    }
                }
                if (!isset($class)) {
                    if (array_key_exists('others', $parameters)) {
                        $class = null;
                    }else {
                        throw new Exception('No se pudo encontrar la clase');
                    }
                }
            }else {
                $class = [$parameters['classes'][0]];
            }
            return $class;
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    public function getMethod(array $use_class, array $array, array $info_data) :Exception|string
    {
        try {
            if (array_key_exists(1, $use_class)) {
                $methods[] = str_contains(array_key_first($array),'collection')
                    ? count((array)reset($array)[0])
                    : count((array)reset($array));
            } else {
                $methods[] = count($array);
            }
            $methods[] = array_key_exists(1, $info_data) ? $info_data[1] : $info_data[0];
            for ($i = 0; $i < count($methods); $i++) {
                if (method_exists($use_class[0], $method_name = '__construct' . $methods[$i])) {
                    $method = $method_name;
                }
            }
            return $method ?? throw new Exception('Método no encontrado para ' . $use_class[0]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function instance(string $use_class, string $use_method, array $array) :object|string
    {
        try {
            $array = $this -> transformData($use_class, $use_method, $array);
            if (is_string($array)) {
                throw new Exception('No se pudieron transformar los datos. ' . $array);
            }
            $array = array_map(function ($value){
                return $value === '' ? null : $value;
            }, $array);
            $array[] = $use_method;
            call_user_func_array([$new = new $use_class(), '__construct'], $array);
            return $new;
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    public function transformData(string $class, string $method, array $data) :array|string
    {
        try {
            $reflection_method = new ReflectionMethod($class, $method);
            foreach ($reflection_method -> getParameters() as $index => $parameter) {
                $parameters = $this -> getTypeParameters($class, $method, $parameter);
                if (array_key_exists('classes', $parameters) || array_key_exists('collection', $parameters)) {
                    $one_parameter = $reflection_method -> getNumberOfParameters() === 1;
                    if (($reflection_method -> getNumberOfParameters() - $reflection_method ->
                        getNumberOfRequiredParameters() > 0)) {
                        if (preg_grep('/(optional)/', array_keys($data))) {
                            if ($parameter -> isOptional() && !preg_match(
                                    '/(' . $parameter -> getName() . ')/',
                                    array_keys
                                    (
                                        $data
                                    )[$index]
                                )) {
                                continue;
                            }
                        }else {
                            $one_parameter = $reflection_method -> getNumberOfRequiredParameters() === 1;
                        }
                    }
                    if ($one_parameter) {
                        $array = is_array(reset($data)) ? reset($data) : $data;
                    }else {
                        $array = (array)array_values($data)[$index];
                    }
                    if (!((count($array) === 1 && reset($array) === '') || empty($array))) {
                        $info_data = explode('_', array_keys($data)[$index]);
                        $use_class = $this -> getClass($info_data[0], $parameters);
                        if (is_string($use_class)) {
                            throw new Exception($use_class);
                        }
                        if ($use_class !== null) {
                            if (is_object($use_method = $this -> getMethod($use_class, $array, $info_data))) {
                                throw new Exception($use_method -> getMessage());
                            }
                            if (array_key_exists(1, $use_class)) {
                                $array = $this -> createCollection($array, $use_class[0], $use_method);
                                $data = $one_parameter
                                    ? $array
                                    : array_replace($data, [
                                        array_keys($data)[$index] =>
                                            $array
                                    ]);
                            }else {
                                $array = $this -> instance($use_class[0], $use_method, $array);
                                if (is_string($array)) {
                                    throw new Exception($array);
                                }
                                $data = array_replace($data, [array_keys($data)[$index] => $array]);
                            }
                            $use_method = null;
                        }
                    }
                }
            }
            return array_values($data);
        } catch (ReflectionException|Exception $e) {
            return $e -> getMessage();
        }
    }

    public function createCollection(array $array, string $use_class, string $use_method) :array|string
    {
        try {
            foreach ($array as $index_collection => $value) {
                $array[$index_collection] = str_contains($index_collection, 'collection')
                    ? $this -> createCollection($value, $use_class, $use_method)
                    : $this -> instance($use_class, $use_method, (array)$value);
                if (is_string($array[$index_collection])) {
                    throw new Exception($array[$index_collection]);
                }
            }
            return $array;
        } catch (Exception $e) {
            return $e -> getMessage();
        }
    }

    public
    function getController(
        string $controller,
        string $method
    ) :void{
        try {
            //if (!empty($_POST)) {
            $data = $this -> transformData($controller, $method, $this -> data($_POST));
            if (is_string($data)) {
                throw new Exception($data);
            }
            $return = call_user_func_array([new $controller(), $method], $data);
            if ($return !== null) {
                echo json_encode($return);
            }
            //} else {
            //    require_once error('404.php');
            //}
        } catch (ReflectionException|Exception $e) {
            echo $e -> getMessage();
        }
    }
}
