<?php

namespace Thumbrise\Toolkit\Util;

/**
 * @template T
 */
class ObjectUtil
{


    /**
     * Преобразует объект $from в объект $to, используя соответствие ключей из массива $keysFromTo.
     *
     * @param object   $from       Исходный
     *                             объект,
     *                             из
     *                             которого
     *                             будут
     *                             взяты
     *                             значения.
     * @param T|object $to         Целевой
     *                             объект, в
     *                             который будут
     *                             записаны
     *                             значения.
     * @param array    $keysFromTo Массив,
     *                             содержащий
     *                             соответствие
     *                             ключей между
     *                             объектами $from и
     *                             $to.
     *
     * @return T Возвращает целевой объект $to с преобразованными значениями.
     */
    public static function mapByKeys(object $from, mixed $to, array $keysFromTo)
    {
        foreach ($keysFromTo as $keyFrom => $keyTo) {
            if (! isset($from->{$keyFrom})) {
                continue;
            }

            $to->{$keyTo} = $from->{$keyFrom};
        }

        return $to;
    }


}
