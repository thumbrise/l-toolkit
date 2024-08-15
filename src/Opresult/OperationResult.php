<?php

namespace Thumbrise\Toolkit\Opresult;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use Stringable;

use function response;

/**
 * @template T
 *
 * @mixin    JsonResponse
 */
final class OperationResult implements Stringable, JsonSerializable, Responsable
{
    use ForwardsCalls;

    public const STACK_REFS_REGISTRY = [
        ['class' => Validator::class, 'function' => 'validate'],
        ['class' => Validator::class, 'function' => 'error'],
        ['class' => self::class, 'function' => 'error'],
        ['class' => self::class, 'function' => 'withError'],
    ];

    protected ?JsonResponse $httpResponse = null;

    /**
     * @param null|mixed|T $data
     */
    public function __construct(
        public mixed $data = null,
        public ?Error $error = null,
    ) {}

    /**
     * @throws Exception
     */
    public function __call(string $method, array $args)
    {
        $this->ensureHttpResponseExists();

        return $this->forwardDecoratedCallTo($this->httpResponse, $method, $args);
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public static function error(mixed $message = '', mixed $code = Error::CODE_DEFAULT, array $additional = []): OperationResult
    {
        return new OperationResult(null, Error::make($message, $code, null, $additional));
    }

    public static function success(mixed $data = null): OperationResult
    {
        return new OperationResult($data, null);
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
            'data' => $this->data,
        ];
    }

    public function toResponse($request): Application|\Illuminate\Foundation\Application|JsonResponse|Response|ResponseFactory
    {
        $this->ensureHttpResponseExists();

        return $this->httpResponse->setContent($this);
    }

    public function withData(mixed $data): OperationResult
    {
        $this->data = $data;

        return $this;
    }

    public function withError(mixed $message = '', $code = Error::CODE_DEFAULT): OperationResult
    {
        $this->error = $this->makeError($message, $code);

        return $this;
    }

    public function withLastErrorOnly(): OperationResult
    {
        $this->error = $this->error->withoutPrevious();

        return $this;
    }

    public function withoutData(): OperationResult
    {
        $this->data = null;

        return $this;
    }

    public function withoutError(): OperationResult
    {
        $this->error = null;

        return $this;
    }

    public function withoutErrorContext(): OperationResult
    {
        if (empty($this->error)) {
            return $this;
        }

        $this->error = $this->error->withoutContext();

        return $this;
    }

    private function ensureHttpResponseExists(): void
    {
        if (empty($this->httpResponse)) {
            $this->httpResponse = response()->json();
        }
    }

    private function makeError(mixed $message = '', $code = Error::CODE_DEFAULT): Error
    {
        if (! empty($this->error)) {
            return $this->error->wrap($message, $code);
        }

        return Error::make($message, $code);
    }
}
