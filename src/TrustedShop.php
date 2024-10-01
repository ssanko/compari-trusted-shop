<?php

namespace Ssanko\Compari;

use Ssanko\Compari\Config\Constant;
use Ssanko\Compari\Exception\ResponseException;
use Ssanko\Compari\Exception\TrustedShopException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class TrustedShop
{
    protected ?string $email = null;
    /** @var array<int, array{Name: string, Id: string|int}> */
    protected array $products = [];

    public function __construct(
        protected ?string $webApiKey
    ) {}

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $productName - A product name from the customer's cart.
     * @param ?string $productId - A product id, it must be same as in the feed.
     */
    public function addProduct(string $productName, ?string $productId = null): void
    {
        $content = [];
        $content['Name'] = $productName;
        if ($productId !== null) {
            $content['Id'] = $productId;
        }
        $this->products[] = $content;
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

        $params = [];
        $params['Version'] = Constant::VERSION;
        $params['WebApiKey'] = $this->webApiKey;
        $params['Email'] = $this->email;
        $params['Products'] = json_encode($this->products);

        $query = $this->getQuery($params);

        return Response::create($this->webApiKey, $query);
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
            'base_uri'        => Constant::SERVICE_URL_SEND,
            'timeout'         => 5,
            'connect_timeout' => 5,
        ]);

        $request = new Request('POST', Constant::SERVICE_TOKEN_REQUEST, $params);

        try {
            $response = $client->send($request);

            $responseBody = json_decode($response->getBody(), true, flags: JSON_THROW_ON_ERROR);

            return match ($response->getStatusCode()) {
                200 => '?' . http_build_query([
                        'Token' => $responseBody->Token,
                        'webApiKey' => $this->webApiKey,
                        'C' => '',
                    ]),
                400 => throw ResponseException::badRequest($responseBody->ErrorCode, $responseBody->ErrorMessage),
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
