<?php

namespace Thumbrise\Toolkit\Opresult\Internal\Traits;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HttpResponseTrait
{
    protected int $httpCode = 200;
    protected array $httpHeaders = [];
    protected \Illuminate\Foundation\Application|Response|Application|ResponseFactory|JsonResponse|null $httpResponse = null;

    /**
     * @throws Exception
     */
    public function __call(string $method, array $args)
    {
        $response = $this->ensureHttpResponse();
        if ($method == 'json') {
            $response = response()->json(...$args);
            $this->httpResponse = $response;

            return $this;
        }

        if (! method_exists($response, $method)) {
            throw new Exception('Method ' . $method . ' does not exist');
        }

        $this->httpResponse = $response->{$method}(...$args);

        return $this;
    }

    public function asHttpResponse(\Illuminate\Foundation\Application|Response|Application|ResponseFactory|JsonResponse|null $response = null): static
    {
        $this->httpResponse = $response;

        return $this;
    }

    public function toResponse($request): \Illuminate\Foundation\Application|Response|Application|JsonResponse|ResponseFactory
    {
        if (! empty($this->httpResponse)) {
            return $this->httpResponse->setContent(json_encode($this->toArray()));
        }

        return response()->json($this->toArray(), $this->httpCode, $this->httpHeaders);
    }

    public function withHttpCode(int $code = 200): static
    {
        $this->httpCode = $code;

        return $this;
    }

    public function withHttpHeaders(array $headers): static
    {
        $this->httpHeaders = $headers;

        return $this;
    }

    private function ensureHttpResponse()
    {
        if (empty($this->httpResponse)) {
            $this->httpResponse = \response()->json();
        }

        return $this->httpResponse;
    }
}