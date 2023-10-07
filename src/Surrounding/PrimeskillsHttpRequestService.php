<?php

namespace Primeskills\ApiCommon\Surrounding;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Primeskills\ApiCommon\Exceptions\PrimeskillsException;
use Primeskills\ApiCommon\Traits\PrimeskillsLog;
use Psr\Http\Message\ResponseInterface;

class PrimeskillsHttpRequestService
{
    use PrimeskillsLog;

    /**
     * @var string $url
     */
    private $url = "";

    /**
     * @var array|object|null $body
     */
    private $body = null;

    /**
     * @var null|array $headers
     */
    private $headers = null;

    /**
     * @var Client $client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PrimeskillsHttpRequestService
     */
    public function setUrl(string $url): PrimeskillsHttpRequestService
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array|object|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array|object|null $body
     * @return PrimeskillsHttpRequestService
     */
    public function setBody($body): PrimeskillsHttpRequestService
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    /**
     * @param array|null $headers
     * @return PrimeskillsHttpRequestService
     */
    public function setHeaders(?array $headers): PrimeskillsHttpRequestService
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function post(): ResponseInterface
    {
        try {
            return $this->client->post($this->getUrl(), ['headers' => $this->getHeaders(), 'json' => $this->getBody()]);
        } catch (GuzzleException $guzzleException) {

            if ($guzzleException->getCode() >= 300 && $guzzleException->getCode() <= 499)  throw new PrimeskillsException($guzzleException->getCode(), "Invalid request");
            throw new PrimeskillsException(500, $guzzleException->getMessage());
        } catch (\Exception $exception) {
            throw new PrimeskillsException(500, "Error when validate token");
        }
    }

    /**
     * @return Response
     */
    public function get(): Response
    {
        return Http::withHeaders($this->getHeaders())->get($this->getUrl());
    }


}
