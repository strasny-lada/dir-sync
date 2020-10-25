<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 18:06
 */

namespace StrasnyLada\DirSync\Transformer;

/**
 * Class ObjectToArrayTransformer
 * @package StrasnyLada\DirSyncBase\Transformer
 */
class ObjectToArrayTransformer
{
    /**
     * @param \stdClass $object
     * @return array
     */
    public function transform(\stdClass $object)
    {
        return self::transformObjectToArray($object);
    }

    /**
     * @param \stdClass $object
     * @return array
     */
    protected static function transformObjectToArray(\stdClass $object)
    {
        $array = [];
        foreach($object as $key => $value) {
            if (!is_object($value)) {
                $array[$key] = $value;
            } else {
                $array[$key] = self::transformObjectTOArray($value);
            }
        }
        return $array;
    }
}