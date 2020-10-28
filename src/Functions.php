<?php

declare(strict_types=1);

namespace PersiLiao\Utils;

/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @see    https://www.github.com/persiliao
 */

use PersiLiao\Utils\ApplicationContext;
use PersiLiao\Utils\Arr;
use PersiLiao\Utils\Backoff;
use PersiLiao\Utils\Collection;
use PersiLiao\Utils\HigherOrderTapProxy;
use PersiLiao\Utils\Str;

if(!function_exists('value')){
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}
if(!function_exists('env')){
    /**
     * Gets the value of an environment variable.
     *
     * @param string     $key
     * @param mixed|null $default
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if(false === $value){
            return value($default);
        }
        switch(strtolower($value)){
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
        if(($valueLength = strlen($value)) > 1 && '"' === $value[0] && '"' === $value[$valueLength - 1]){
            return substr($value, 1, -1);
        }

        return $value;
    }
}
if(!function_exists('retry')){
    /**
     * Retry an operation a given number of times.
     *
     * @param float|int $times
     * @param int       $sleep millisecond
     *
     * @throws \Throwable
     */
    function retry($times, callable $callback, int $sleep = 0)
    {
        $backoff = new Backoff($sleep);
        beginning:
        try{
            return $callback();
        }catch(\Throwable $e){
            if(--$times < 0){
                throw $e;
            }
            $backoff->sleep();
            goto beginning;
        }
    }
}
if(!function_exists('with')){
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param mixed $value
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}

if(!function_exists('collect')){
    /**
     * Create a collection from the given value.
     *
     * @param mixed|null $value
     *
     * @return Collection
     */
    function collect($value = null)
    {
        return new Collection($value);
    }
}
if(!function_exists('data_fill')){
    /**
     * Fill in data where it's missing.
     *
     * @param mixed        $target
     * @param array|string $key
     * @param mixed        $value
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}
if(!function_exists('data_get')){
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param array|int|string|null $key
     * @param mixed|null            $default
     * @param mixed                 $target
     */
    function data_get($target, $key, $default = null)
    {
        if(is_null($key)){
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string)$key : $key);
        while(!is_null($segment = array_shift($key))){
            if('*' === $segment){
                if($target instanceof Collection){
                    $target = $target->all();
                }elseif(!is_array($target)){
                    return value($default);
                }
                $result = [];
                foreach($target as $item){
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }
            if(Arr::accessible($target) && Arr::exists($target, $segment)){
                $target = $target[$segment];
            }elseif(is_object($target) && isset($target->{$segment})){
                $target = $target->{$segment};
            }else{
                return value($default);
            }
        }

        return $target;
    }
}
if(!function_exists('data_set')){
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed        $target
     * @param array|string $key
     * @param bool         $overwrite
     * @param mixed        $value
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if('*' === ($segment = array_shift($segments))){
            if(!Arr::accessible($target)){
                $target = [];
            }
            if($segments){
                foreach($target as &$inner){
                    data_set($inner, $segments, $value, $overwrite);
                }
            }elseif($overwrite){
                foreach($target as &$inner){
                    $inner = $value;
                }
            }
        }elseif(Arr::accessible($target)){
            if($segments){
                if(!Arr::exists($target, $segment)){
                    $target[$segment] = [];
                }
                data_set($target[$segment], $segments, $value, $overwrite);
            }elseif($overwrite || !Arr::exists($target, $segment)){
                $target[$segment] = $value;
            }
        }elseif(is_object($target)){
            if($segments){
                if(!isset($target->{$segment})){
                    $target->{$segment} = [];
                }
                data_set($target->{$segment}, $segments, $value, $overwrite);
            }elseif($overwrite || !isset($target->{$segment})){
                $target->{$segment} = $value;
            }
        }else{
            $target = [];
            if($segments){
                $target[$segment] = [];
                data_set($target[$segment], $segments, $value, $overwrite);
            }elseif($overwrite){
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}
if(!function_exists('head')){
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     */
    function head($array)
    {
        return reset($array);
    }
}
if(!function_exists('last')){
    /**
     * Get the last element from an array.
     *
     * @param array $array
     */
    function last($array)
    {
        return end($array);
    }
}
if(!function_exists('tap')){
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param callable|null $callback
     * @param mixed         $value
     */
    function tap($value, $callback = null)
    {
        if(is_null($callback)){
            return new HigherOrderTapProxy($value);
        }
        $callback($value);

        return $value;
    }
}

if(!function_exists('call')){
    /**
     * Call a callback with the arguments.
     *
     * @param mixed $callback
     *
     * @return mixed|null
     */
    function call($callback, array $args = [])
    {
        $result = null;
        if($callback instanceof \Closure){
            $result = $callback(...$args);
        }elseif(is_object($callback) || (is_string($callback) && function_exists($callback))){
            $result = $callback(...$args);
        }elseif(is_array($callback)){
            [
                $object,
                $method,
            ] = $callback;
            $result = is_object($object) ? $object->{$method}(...$args) : $object::$method(...$args);
        }else{
            $result = call_user_func_array($callback, $args);
        }

        return $result;
    }
}

if(!function_exists('class_basename')){
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param object|string $class
     *
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if(!function_exists('setter')){
    /**
     * Create a setter string.
     */
    function setter(string $property): string
    {
        return 'set' . Str::studly($property);
    }
}

if(!function_exists('getter')){
    /**
     * Create a getter string.
     */
    function getter(string $property): string
    {
        return 'get' . Str::studly($property);
    }
}

if(!function_exists('make')){
    /**
     * Create a object instance, if the DI container exist in ApplicationContext,
     * then the object will be create by DI container via `make()` method, if not,
     * the object will create by `new` keyword.
     */
    function make(string $name, array $parameters = [])
    {
        if(ApplicationContext::hasContainer()){
            $container = ApplicationContext::getContainer();
            if(method_exists($container, 'make')){
                return $container->make($name, $parameters);
            }
        }
        $parameters = array_values($parameters);

        return new $name(...$parameters);
    }
}
