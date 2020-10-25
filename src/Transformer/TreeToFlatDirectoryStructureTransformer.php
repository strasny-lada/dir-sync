<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 18:56
 */

namespace StrasnyLada\DirSync\Transformer;

/**
 * Class TreeToFlatDirectoryStructureTransformer
 * @package StrasnyLada\DirSyncBase\Transformer
 */
class TreeToFlatDirectoryStructureTransformer
{
    /**
     * @param array $tree
     * @return array
     */
    public function transform(array $tree)
    {
        $flat = self::transformTreeToFlat($tree);
        ksort($flat);
        return $flat;
    }

    /**
     * @param array $tree
     * @return array
     */
    protected static function transformTreeToFlat(array $tree)
    {
        $array = [];
        foreach($tree as $key => $value) {
            if (!is_array($value) || !self::isAssociativeArray($value)) {
                $array[$key] = $value;
            } else {
                foreach(self::transformTreeToFlat($value) as $childKey => $childValue) {
                    $array[$key] = '';
                    $array[sprintf('%s/%s', $key, $childKey)] = $childValue;
                }
            }
        }
        return $array;
    }

    /**
     * Determines if the array is associative or sequential
     *
     * @param array $array
     * @return bool
     */
    protected static function isAssociativeArray(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}