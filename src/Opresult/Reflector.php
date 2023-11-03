<?php

namespace Thumbrise\Toolkit\Opresult;


/**
 * @internal
 */
class Reflector
{
    /**
     * @param array<array{class: class-string, function: callable-string}> $registry
     * @return array|null
     */
    public static function getCallInfo(array $registry): ?array
    {
        $trace = debug_backtrace(! DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = array_reverse($trace);

        foreach ($registry as $each) {

            $classRegistry = $each['class'];
            $functionRegistry = $each['function'];

            foreach ($trace as $info) {

                $classTrace = $info['class'];
                $functionTrace = $info['function'];

                if ($classRegistry === $classTrace && $functionRegistry === $functionTrace) {
                    return $info;
                }
            }
        }

        return null;
    }
}