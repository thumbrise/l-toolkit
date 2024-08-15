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
     * @param object   $from       исходный
     *                             объект,
     *                             из
     *                             которого
     *                             будут
     *                             взяты
     *                             значения
     * @param object|T $to         целевой
     *                             объект, в
     *                             который будут
     *                             записаны
     *                             значения
     * @param array    $keysFromTo массив,
     *                             содержащий
     *                             соответствие
     *                             ключей между
     *                             объектами $from и
     *                             $to
     *
     * @return T возвращает целевой объект $to с преобразованными значениями
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
