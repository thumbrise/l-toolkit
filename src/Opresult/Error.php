<?php

namespace Thumbrise\Toolkit\Opresult;

use Exception;
use JsonSerializable;
use Stringable;
use Thumbrise\Toolkit\Opresult\Internal\Reflector;
use UnitEnum;

class Error extends Exception implements Stringable, JsonSerializable
{
    /**
     * Реестр нужен, чтобы была возможность забрать контекст создания ошибки.
     */
    public const CONTEXTUAL_FUNCTIONS_REGISTRY = [
        ...OperationResult::CONTEXTUAL_FUNCTIONS_REGISTRY,
        ['class' => self::class, 'function' => 'make'],
        ['class' => self::class, 'function' => 'wrap'],
    ];
    public const CODE_DEFAULT = 'UNKNOWN';
    private mixed $context;
    private ?Error $previous;
    private readonly mixed $messageOriginal;
    private readonly mixed $codeOriginal;

    private function __construct(mixed $message = '', mixed $code = self::CODE_DEFAULT, ?Error $previous = null)
    {
        $code = $this->prepareCode($code);

        $this->codeOriginal = $code;
        $this->messageOriginal = $message;
        $this->previous = $previous;
        $this->context = Reflector::getCallInfo(self::CONTEXTUAL_FUNCTIONS_REGISTRY);


        parent::__construct(
            var_export($this->toArray(), true),
            0,
            $previous
        );
    }

    public static function make(mixed $message = '', mixed $code = self::CODE_DEFAULT, ?Error $previous = null): static
    {
        return new static($message, $code, $previous);
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function code(): mixed
    {
        return $this->codeOriginal;
    }

    public function is(mixed $code = null): bool
    {
        if (is_null($code)) {
            return false;
        }

        $code = $this->prepareCode($code);

        $result = $this->codeOriginal === $code;

        if ($result) {
            return true;
        }

        if (! empty($this->previous)) {
            return $this->previous->is($code);
        }

        return false;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function message(): mixed
    {
        return $this->messageOriginal;
    }

    /**
     * @param mixed $code
     * @return mixed|string
     */
    public function prepareCode(mixed $code): mixed
    {
        if ($code instanceof UnitEnum) {
            $code = $code->name;
        }
        return $code;
    }

    public function toArray(): array
    {
        $result = [
            'error_message' => $this->message(),
            'error_code' => $this->code(),
        ];

        if (! empty($this->context)) {
            $result['error_context'] = $this->context;
        }
        if (! empty($this->previous)) {
            $result['error_previous'] = $this->previous->toArray();
        }

        return $result;
    }

    public function withoutContext(): static
    {
        $this->context = null;
        if (! empty($this->previous)) {
            $this->previous = $this->previous->withoutContext();
        }

        return $this;
    }

    public function withoutPrevious(): static
    {
        $this->previous = null;
        return $this;
    }

    public function wrap(mixed $message = '', mixed $code = self::CODE_DEFAULT): static
    {
        return new static($message, $code, $this);
    }

    private function messageForParentException(mixed $message, mixed $code): string
    {
        if (is_array($message)) {
            $message = var_export($message, true);
        }

        return sprintf("\ncode: %s\nmessage:\n%s\n", $code, $message);
    }
}
