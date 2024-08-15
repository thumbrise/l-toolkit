<?php

namespace Thumbrise\Toolkit\Opresult;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\TranslatableMessage;

class Validator
{
    private static mixed $globalMessage = 'Validation error.';

    private static mixed $globalCode = ErrorsBasic::Validation;

    private static mixed $globalHttpCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public static function error(
        array $inputErrors,
        mixed $errorMessage,
        mixed $errorCode,
        mixed $errorHttpCode,
    ): OperationResult {
        $laravelException = ValidationException::withMessages($inputErrors);

        return self::makeOpresultValidationError(
            $laravelException->errors(),
            $errorMessage,
            $errorCode,
            $errorHttpCode
        );
    }

    /**
     * Set or get global default code for validation errors. Default is <pre>\Thumbrise\Toolkit\Opresult\ErrorsBasic::Validation</pre>.
     */
    public static function globalCode(mixed $value = null): mixed
    {
        if (null !== $value) {
            self::$globalCode = $value;
        }

        return self::$globalCode;
    }

    /**
     * Set or get global default http code for validation errors. Default is 422.
     */
    public static function globalHttpCode(mixed $value = null): mixed
    {
        if (null !== $value) {
            self::$globalHttpCode = $value;
        }

        return self::$globalHttpCode;
    }

    /**
     * Set or get global default message for validation errors.
     */
    public static function globalMessage(null|string|TranslatableMessage $value = null): string|TranslatableMessage
    {
        if (null !== $value) {
            self::$globalMessage = __($value);
        }

        return self::$globalMessage;
    }

    public static function validate(
        array $data,
        array $rules,
        array $ruleMessages = [],
        array $ruleAttributes = [],
        mixed $errorMessage = null,
        mixed $errorCode = null,
        mixed $errorHttpCode = null
    ): OperationResult {
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules, $ruleMessages, $ruleAttributes);
        if ($validator->fails()) {
            return self::makeOpresultValidationError(
                $validator->errors()->toArray(),
                $errorMessage,
                $errorCode,
                $errorHttpCode
            );
        }

        return OperationResult::success();
    }

    private static function makeOpresultValidationError(
        array $inputErrors,
        mixed $errorMessage,
        mixed $errorCode,
        mixed $errorHttpCode,
    ): OperationResult {
        $errorMessage  = $errorMessage  ?? self::$globalMessage;
        $errorCode     = $errorCode     ?? self::$globalCode;
        $errorHttpCode = $errorHttpCode ?? self::$globalHttpCode;

        $additional = [
            'error_fields' => $inputErrors,
        ];

        // @phpstan-ignore-next-line
        return OperationResult::error(
            $errorMessage,
            $errorCode,
            $additional,
        )->setStatusCode($errorHttpCode);
    }
}
