<?php

namespace Thumbrise\Toolkit\Opresult;

use Exception;
use JsonSerializable;
use Stringable;
use Thumbrise\Toolkit\Opresult\Internal\Reflector;
use UnitEnum;

final class Error extends Exception implements Stringable, JsonSerializable
{
    /**
     * Реестр нужен, чтобы была возможность забрать контекст создания ошибки.
     */
    public const STACK_REFS_REGISTRY = [
        ...OperationResult::STACK_REFS_REGISTRY,
        ['class' => self::class, 'function' => 'make'],
        ['class' => self::class, 'function' => 'wrap'],
    ];
    public const CODE_DEFAULT = 'UNKNOWN';

    private static bool $disableSensitiveDetails = false;
    private mixed $context;
    private ?Error $previous;
    private readonly mixed $messageOriginal;
    private readonly mixed $codeOriginal;
    private readonly mixed $additional;

    private function __construct(mixed $message = '', mixed $code = self::CODE_DEFAULT, ?Error $previous = null, array $additional = [])
    {
        $code = $this->prepareCode($code);

        $this->codeOriginal    = $code;
        $this->messageOriginal = $message;
        $this->previous        = $previous;
        $this->additional      = $additional;
        $this->context         = Reflector::getCallInfo(self::STACK_REFS_REGISTRY);

        parent::__construct(
            var_export($this->toArray(), true),
            0,
            $previous,
        );
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public static function disableSensitiveDetails(bool $value = true): void
    {
        Error::$disableSensitiveDetails = $value;
    }

    public static function make(mixed $message = '', mixed $code = self::CODE_DEFAULT, ?Error $previous = null, array $additional = []): static
    {
        return new self($message, $code, $previous, $additional);
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
     * @return mixed|string
     */
    public function prepareCode(mixed $code): mixed
    {
        if ($code instanceof UnitEnum) {
            $separator = '/';
            $code      = class_basename($code).$separator.$code->name;
        }

        return $code;
    }

    public function toArray(): array
    {
        $result = [
            'error_message' => $this->message(),
            'error_code'    => $this->code(),
            ...$this->additional,
        ];

        if (Error::$disableSensitiveDetails) {
            return $result;
        }

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
}
