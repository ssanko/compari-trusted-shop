<?php

namespace Ssanko\Compari;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Ssanko\Compari\Config\ArukeresoConfig;
use Ssanko\Compari\Config\CompariConfig;
use Ssanko\Compari\Exception\ResponseException;
use Ssanko\Compari\Exception\TrustedShopException;
use Ssanko\Compari\Response\Response;

class TrustedShop
{
    protected ?string $email = null;
    /** @var array<int, array{Name: string, Id: string|int}> */
    protected array $products = [];

    public function __construct(
        protected ?string $webApiKey,
        protected CompariConfig|ArukeresoConfig $config
    ) {}

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $productName - A product name from the customer's cart.
     * @param ?string $productId - A product id, it must be same as in the feed.
     */
    public function addProduct(string $productName, ?string $productId = null): static
    {
        $content = [
            'Name' => $productName
        ];

        if ($productId !== null) {
            $content['Id'] = $productId;
        }

        $this->products[] = $content;

        return $this;
    }

    /**
     * Create the Trusted code, which provides data sending from the customer's browser to arukereso.
     * @return string - Prepared Trusted code (HTML).
     * @throws ResponseException
     * @throws TrustedShopException
     * @throws GuzzleException
     */
    public function createTrustedCode(): string
    {
        if (empty($this->webApiKey)) {
            throw TrustedShopException::emptyWebApiKey();
        }

        if (empty($this->email)) {
            throw TrustedShopException::emptyEmail();
        }

        $query = $this->getQuery([
            'Version'   => $this->config->getApiVersion(),
            'WebApiKey' => $this->webApiKey,
            'Email'     => $this->email,
            'Products'  => json_encode($this->products)
        ]);

        return Response::create($this->webApiKey, $query, $this->config);
    }

    /**
     * Performs a request on Compari servers to get a token and assembles query params with it.
     * @param array $params - Parameters to send with token request.
     * @return string - Query string to assemble sending code snippet on client's side with it.
     * @throws GuzzleException
     * @throws ResponseException
     */
    protected function getQuery(array $params): string
    {
        $client = new Client([
            'base_uri'        => $this->config->getServiceUrlSend(),
            'timeout'         => 5,
            'connect_timeout' => 5,
        ]);

        try {
            $response = $client->post($this->config->getServiceTokenRequest(), [
                RequestOptions::FORM_PARAMS => $params,
            ]);

            $responseBody = json_decode(
                json       : $response->getBody()->getContents(),
                associative: false,
                flags      : JSON_THROW_ON_ERROR
            );

            if (empty($responseBody)) {
                throw ResponseException::trustedShopNotEnabled();
            }

            return match ($response->getStatusCode()) {
                200     => '?' . http_build_query([
                        'Token'     => $responseBody->Token,
                        'webApiKey' => $this->webApiKey,
                        'C'         => '',
                    ]),
                400     => throw ResponseException::badRequest($responseBody->ErrorCode, $responseBody->ErrorMessage),
                default => throw ResponseException::requestFailed()
            };
        } catch (ConnectException) {
            throw ResponseException::requestTimedOut();
        } catch (RequestException $exception) {
            throw ResponseException::requestFailed($exception->getMessage());
        } catch (\JsonException $exception) {
            throw ResponseException::jsonError($exception->getMessage());
        }
    }
}
