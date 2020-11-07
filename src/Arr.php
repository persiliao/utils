<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @see    https://www.github.com/persiliao
 */

namespace PersiLiao\Utils;

use ArrayAccess;
use InvalidArgumentException;

use function array_search;
use function is_array;
use function is_string;

/**
 * Most of the methods in this file come from illuminate/support,
 * thanks Laravel Team provide such a useful class.
 */
class Arr{

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param mixed $value
     */
    public static function add(array $array, string $key, $value): array
    {
        if(is_null(static::get($array, $key))){
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array|\ArrayAccess $array
     * @param int|string|null    $key
     * @param mixed              $default
     */
    public static function get($array, $key = null, $default = null)
    {
        if(!static::accessible($array)){
            return value($default);
        }
        if(is_null($key)){
            return $array;
        }
        if(static::exists($array, $key)){
            return $array[$key];
        }
        if(!is_string($key) || false === strpos($key, '.')){
            return $array[$key] ?? value($default);
        }
        foreach(explode('.', $key) as $segment){
            if(static::accessible($array) && static::exists($array, $segment)){
                $array = $array[$segment];
            }else{
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param array|\ArrayAccess $array
     * @param int|string         $key
     */
    public static function exists($array, $key): bool
    {
        if($array instanceof ArrayAccess){
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param int|string|null $key
     * @param mixed           $value
     */
    public static function set(array &$array, $key, $value): array
    {
        if(is_null($key)){
            return $array = $value;
        }
        if(!is_string($key)){
            $array[$key] = $value;

            return $array;
        }
        $keys = explode('.', $key);
        while(count($keys) > 1){
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if(!isset($array[$key]) || !is_array($array[$key])){
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     */
    public static function collapse(array $array): array
    {
        $results = [];
        foreach($array as $values){
            if($values instanceof Collection){
                $values = $values->all();
            }elseif(!is_array($values)){
                continue;
            }
            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param array ...$arrays
     */
    public static function crossJoin(...$arrays): array
    {
        $results = [ [] ];
        foreach($arrays as $index => $array){
            $append = [];
            foreach($results as $product){
                foreach($array as $item){
                    $product[$index] = $item;
                    $append[] = $product;
                }
            }
            $results = $append;
        }

        return $results;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param array $array
     *
     * @return array
     */
    public static function divide($array)
    {
        return [
            array_keys($array),
            array_values($array),
        ];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];
        foreach($array as $key => $value){
            if(is_array($value) && !empty($value)){
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            }else{
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array|string $keys
     */
    public static function except(array $array, $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array|string $keys
     */
    public static function forget(array &$array, $keys): void
    {
        $original = &$array;
        $keys = (array)$keys;
        if(0 === count($keys)){
            return;
        }
        foreach($keys as $key){
            // if the exact key exists in the top-level, remove it
            if(static::exists($array, $key)){
                unset($array[$key]);
                continue;
            }
            $parts = explode('.', $key);
            // clean up before each pass
            $array = &$original;
            while(count($parts) > 1){
                $part = array_shift($parts);
                if(isset($array[$part]) && is_array($array[$part])){
                    $array = &$array[$part];
                }else{
                    continue 2;
                }
            }
            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param mixed|null $default
     */
    public static function last(array $array, callable $callback = null, $default = null)
    {
        if(is_null($callback)){
            return empty($array) ? value($default) : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param mixed|null $default
     */
    public static function first(array $array, callable $callback = null, $default = null)
    {
        if(is_null($callback)){
            if(empty($array)){
                return value($default);
            }
            foreach($array as $item){
                return $item;
            }
        }
        foreach($array as $key => $value){
            if(call_user_func($callback, $value, $key)){
                return $value;
            }
        }

        return value($default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param float|int $depth
     */
    public static function flatten(array $array, $depth = INF): array
    {
        $result = [];
        foreach($array as $item){
            $item = $item instanceof Collection ? $item->all() : $item;
            if(!is_array($item)){
                $result[] = $item;
            }elseif(1 === $depth){
                $result = array_merge($result, array_values($item));
            }else{
                $result = array_merge($result, static::flatten($item, $depth - 1));
            }
        }

        return $result;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param array|\ArrayAccess $array
     * @param array|string|null  $keys
     */
    public static function has($array, $keys): bool
    {
        if(is_null($keys)){
            return false;
        }
        $keys = (array)$keys;
        if(!$array){
            return false;
        }
        if([] === $keys){
            return false;
        }
        foreach($keys as $key){
            $subKeyArray = $array;
            if(static::exists($array, $key)){
                continue;
            }
            foreach(explode('.', $key) as $segment){
                if(static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)){
                    $subKeyArray = $subKeyArray[$segment];
                }else{
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array|string $keys
     */
    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param array|string      $value
     * @param array|string|null $key
     */
    public static function pluck(array $array, $value, $key = null): array
    {
        $results = [];
        [
            $value,
            $key,
        ] = static::explodePluckParameters($value, $key);
        foreach($array as $item){
            $itemValue = data_get($item, $value);
            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if(is_null($key)){
                $results[] = $itemValue;
            }else{
                $itemKey = data_get($item, $key);
                if(is_object($itemKey) && method_exists($itemKey, '__toString')){
                    $itemKey = (string)$itemKey;
                }
                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param array|string      $value
     * @param array|string|null $key
     */
    protected static function explodePluckParameters($value, $key): array
    {
        $value = is_string($value) ? explode('.', $value) : $value;
        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

        return [
            $value,
            $key,
        ];
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param mixed|null $key
     * @param mixed      $value
     */
    public static function prepend(array $array, $value, $key = null): array
    {
        if(is_null($key)){
            array_unshift($array, $value);
        }else{
            $array = [ $key => $value ] + $array;
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param mixed|null $default
     */
    public static function pull(array &$array, string $key, $default = null)
    {
        $value = static::get($array, $key, $default);
        static::forget($array, $key);

        return $value;
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @throws \InvalidArgumentException
     */
    public static function random(array $array, int $number = null)
    {
        $requested = is_null($number) ? 1 : $number;
        $count = count($array);
        if($requested > $count){
            throw new InvalidArgumentException("You requested {$requested} items, but there are only {$count} items available.");
        }
        if(is_null($number)){
            return $array[array_rand($array)];
        }
        if(0 === $number){
            return [];
        }
        $keys = array_rand($array, $number);
        $results = [];
        foreach((array)$keys as $key){
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Shuffle the given array and return the result.
     */
    public static function shuffle(array $array, int $seed = null): array
    {
        if(is_null($seed)){
            shuffle($array);
        }else{
            mt_srand($seed);
            usort($array, function(){
                return random_int(-1, 1);
            });
        }

        return $array;
    }

    /**
     * Sort the array using the given callback or "dot" notation.
     *
     * @param callable|string|null $callback
     */
    public static function sort(array $array, $callback = null): array
    {
        return Collection::make($array)->sortBy($callback)->all();
    }

    /**
     * Recursively sort an array by keys and values.
     */
    public static function sortRecursive(array $array): array
    {
        foreach($array as &$value){
            if(is_array($value)){
                $value = static::sortRecursive($value);
            }
        }
        if(static::isAssoc($array)){
            ksort($array);
        }else{
            sort($array);
        }

        return $array;
    }

    /**
     * Determines if an array is associative.
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Convert the array into a query string.
     */
    public static function query(array $array): string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Filter the array using the given callback.
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param mixed $value
     */
    public static function wrap($value): array
    {
        if(is_null($value)){
            return [];
        }

        return !is_array($value) ? [ $value ] : $value;
    }

    /**
     * Make array elements unique.
     */
    public static function unique(array $array): array
    {
        $result = [];
        foreach($array ?? [] as $key => $item){
            if(is_array($item)){
                $result[$key] = self::unique($item);
            }else{
                $result[$key] = $item;
            }
        }

        if(!self::isAssoc($result)){
            return array_unique($result);
        }

        return $result;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful.
     */
    public static function searchValues(array $values, array $array)
    {
        $result = [];
        foreach($values as $value){
            $result[$value] = array_search($value, $array);
        }

        return $result;
    }

    /**
     * Convert to infinity tree
     *
     * @param array  $items
     * @param string $primaryKey
     * @param string $parentKey
     * @param string $childrenKey
     *
     * @return array
     */
    public static function transformToChildrenTree(array $items, string $primaryKey = 'id', string $parentKey = 'pid', string $childrenKey = 'children')
    {
        $tree = [];
        foreach($items as $item){
            if(isset($items[$item[$parentKey]])){
                $items[$item[$parentKey]][$childrenKey][] = &$items[$item[$primaryKey]];
            }else{
                $tree[] = &$items[$item[$primaryKey]];
            }
        }

        return $tree;
    }

    public static function transformToLevelTree(array $items, int $childrenId = 0, int $level = 0, string $primaryKey = 'id', string $childrenKey = 'pid', bool $isClear = true)
    {
        static $res = [];
        if($isClear === true){
            $res = [];
        }
        foreach($items as $item){
            if($item[$childrenKey] == $childrenId){
                $item['level'] = $level;
                $res[] = $item;
                self::transformToLevelTree($items, (int)$item[$primaryKey], $level + 1, $primaryKey, $childrenKey, false);
            }
        }

        return $res;
    }

}
