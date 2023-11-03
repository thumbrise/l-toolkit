<?php

namespace Thumbrise\Toolkit\Opresult;

use Illuminate\Validation\ValidationException;

class Validator
{
    public static function error(array $messages): OperationResult
    {
        $laravelException = ValidationException::withMessages($messages);

        return OperationResult::error($laravelException->errors(), Errors::VALIDATION);
    }

    public static function validate(array $data, array $rules, array $messages = [], array $attributes = []): OperationResult
    {
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules, $messages, $attributes);
        if ($validator->fails()) {
            return OperationResult::error($validator->errors()->toArray(), Errors::VALIDATION);
        }

        return OperationResult::success();
    }
}
