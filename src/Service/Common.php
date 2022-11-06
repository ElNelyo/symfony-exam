<?php

namespace App\Service;

class Common
{
    /**
     * Get an array in parameter, loop on it and get the same array without the keys ( keys are replaced by number)
     *
     * @param array<int, String> $array
     * @return array<int,String>
     */
    public static function boo(array $array): array
    {
        $result = [];
        array_walk_recursive($array, function ($a) use (&$result) {
            $result[] = $a;
        });

        return $result;
    }

    /**
     * Switch Array1 and concatenate a second one..
     * Attention, the second array must have 2 keys 'k' & 'v', only these keys will be concatenated.
     * key 'k' will be the key and 'v' will be the value
     *
     * @param array<int,String> $array1
     * @param array<String,String> $array2
     * @return array<int|string,String>
     */
    public static function foo(array $array1, array $array2): array
    {
        return [...$array1, $array2['k'] => $array2['v']];
    }

    /**
     * Detect if keys in array 1 aren't in value array2
     * true: keys from array1 aren't in value from array2
     * false: at least 1 key from array 1 is in array2 as a value
     *
     * @param array<string,String> $array1
     * @param array<string,String> $array2
     * @return bool
*/
    public static function bar(array $array1, array $array2): bool
    {
        $r = array_filter(array_keys($array1), fn ($k) =>!in_array($k, $array2));

        return count($r) == 0;
    }
}
