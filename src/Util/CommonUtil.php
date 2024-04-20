<?php

namespace Thumbrise\Toolkit\Util;

class CommonUtil
{


    public static function firstNotEmpty(...$values)
    {
        foreach ($values as $value) {
            if (! empty($value)) {
                return $value;
            }
        }

        return null;
    }


}
