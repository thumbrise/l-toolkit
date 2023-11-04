<?php

namespace Thumbrise\Toolkit\Opresult;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use JsonSerializable;
use Stringable;
use Thumbrise\Toolkit\Opresult\Internal\Traits\HttpResponseTrait;

/**
 * @template T
 * @see \Illuminate\Foundation\Application
 * @mixin Response
 * @mixin Application
 * @mixin ResponseFactory
 * @mixin JsonResponse
 */
class OperationResult implements Stringable, JsonSerializable, Responsable
{
    use HttpResponseTrait;

    public const CONTEXTUAL_FUNCTIONS_REGISTRY = [
        ['class' => self::class, 'function' => 'error'],
        ['class' => self::class, 'function' => 'withError'],
    ];

    /**
     * @param mixed|null|T $data
     * @param Error|null $error
     */
    public function __construct(
        public mixed  $data = null,
        public ?Error $error = null,
    )
    {
    }

    public static function error(mixed $message = '', $code = Error::CODE_DEFAULT): static
    {
        return new static(null, Error::make($message, $code));
    }

    public static function success(mixed $data = null): static
    {
        return new static($data, null);
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function isError(mixed $code = null): bool
    {
        $errorExists = ! empty($this->error);

        if (! is_null($code) && $errorExists) {
            return $this->error->is($code);
        }

        return $errorExists;
    }

    public function isSuccess(): bool
    {
        return ! $this->isError();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        if ($this->isError()) {
            return $this->error->toArray();
        }

        return [
            'data' => $this->data
        ];
    }

    public function withData(mixed $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function withError(mixed $message = '', $code = Error::CODE_DEFAULT): static
    {
        $this->error = $this->makeError($message, $code);
        return $this;
    }

    public function withLastErrorOnly(): static
    {
        $this->error = $this->error->withoutPrevious();
        return $this;
    }

    public function withoutData(): static
    {
        $this->data = null;
        return $this;
    }

    public function withoutError(): static
    {
        $this->error = null;
        return $this;
    }

    public function withoutErrorContext(): static
    {
        if (empty($this->error)) {
            return $this;
        }

        $this->error = $this->error->withoutContext();

        return $this;
    }

    private function makeError(mixed $message = '', $code = Error::CODE_DEFAULT): Error
    {
        if (! empty($this->error)) {
            return $this->error->wrap($message, $code);
        }

        return Error::make($message, $code);
    }


}
