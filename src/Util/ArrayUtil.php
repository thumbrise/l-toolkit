<?php

namespace Thumbrise\Toolkit\Util;

use Illuminate\Support\Str;

class ArrayUtil
{
    /**
     * <code>
     * $data = [
     *  'a' => 1,
     *  'b' => 2,
     *  'c.d' => 3,
     *  'c.e' => 4,
     *  'f.g.h' => 5,
     * ];.
     *
     * $keys = [
     *  'a',
     *  'c.d',
     *  'newwanted1' => 'c.d',
     *  'newwanted2' => 'f.g.h'
     * ];
     *
     * $result = ArrayUtil::flatMapKeys($data, $keys);
     *
     * $result...
     * [
     *  'a' => 1, // Не изменилось
     *  'd' => 2, // Спустилось на нижний уровень
     *  'newwanted1' => 3, // Преобразовалось, спустилось на нижний уровень
     *  'newwanted2' => 5, // Преобразовалось, спустилось на нижний уровень
     * ];
     * </code>
     */
    public static function flatMapKeys(array $data, array $keys): array
    {
        $result = [];

        $data = collect($data);

        foreach ($keys as $keyWanted => $keyOld) {
            $value = $data->pull($keyOld);

            if (is_string($keyWanted)) {
                $result[$keyWanted] = $value;

                continue;
            }

            if (str_contains($keyOld, '.')) {
                $keyReal = str_replace('.', '', strrchr($keyOld, '.'));
            } else {
                $keyReal = $keyOld;
            }

            $result[$keyReal] = $value;
        }

        return $result;
    }

    public static function keysToCamel(?array $data = []): ?array
    {
        return self::keysTo($data, Str::camel(...));
    }

    public static function keysToSnake(?array $data = []): ?array
    {
        return self::keysTo($data, Str::snake(...));
    }

    private static function keysTo(?array $data, callable $realization): ?array
    {
        if (empty($data)) {
            return $data;
        }

        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$realization($key)] = self::keysTo($value, $realization);
            } else {
                $result[$realization($key)] = $value;
            }
        }

        return $result;
    }
}
