<?php

namespace Thumbrise\Toolkit\Opresult\Internal\Traits;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

trait HttpResponseTrait
{
    protected int $httpCode = 200;
    protected array $httpHeaders = [];
    protected \Illuminate\Foundation\Application|Response|Application|ResponseFactory|null $httpResponse = null;

    public function asHttpResponse(\Illuminate\Foundation\Application|Response|Application|ResponseFactory|null $response = null): static
    {
        $this->httpResponse = $response;

        return $this;
    }

    public function toResponse($request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        if (! empty($this->httpResponse)) {
            return $this->httpResponse;
        }

        return response($this->toArray(), $this->httpCode, $this->httpHeaders);
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
}