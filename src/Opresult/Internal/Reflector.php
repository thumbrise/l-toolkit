<?php

namespace Thumbrise\Toolkit\Opresult\Internal;


/**
 * @internal
 */
class Reflector
{


    /**
     * @param array<array{class: class-string|string|mixed, function: callable-string|string|mixed}> $registry
     *
     * @return array|null
     */
    public static function getCallInfo(array $registry): ?array
    {
        /** @phpstan-ignore-next-line */
        $trace = debug_backtrace(! DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($registry as $each) {
            $classRegistry    = @$each['class'];
            $functionRegistry = @$each['function'];

            foreach ($trace as $info) {
                $classTrace    = @$info['class'];
                $functionTrace = @$info['function'];

                if ($classRegistry === $classTrace && $functionRegistry === $functionTrace) {
                    return [
                        'where' => $info['file'].':'.$info['line'],
                    ];
                }
            }
        }

        return null;
    }


}
