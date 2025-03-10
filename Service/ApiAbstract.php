<?php

declare(strict_types=1);

namespace Tino\Payment\Service;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Payment\Model\Method\Logger;
use Tino\Payment\Gateway\Config\Config;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

abstract class ApiAbstract
{
    const KEYS_ANONYMIZE = [
        'merchantDocumentNumber',
        'merchant_document_number',
        'buyerDocumentNumber',
        'email'
    ];

    const TIME_OUT_API_BANNER = 2;

    protected string $apiKey = "";
    protected string $baseUrl = "";
    protected string $baseUrlBanner = "";

    private Curl $curl;
    private Config $config;
    private JsonSerializer $jsonSerializer;
    private Logger $logger;

    public function __construct(
        Curl $curl,
        Config $config,
        JsonSerializer $jsonSerializer,
        Logger $logger
    ) {
        $this->curl = $curl;
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;

        $this->apiKey = $this->config->getApiKey() ?? '';
        $this->baseUrl = $this->config->getBaseUrlApi();
        $this->baseUrlBanner = $this->config->getUrlApiBanner();

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException("API Key must be configured correctly.");
        }
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $headers
     * @param array $data
     * @return array
     */
    protected function request(string $endpoint, string $method = 'GET', array $headers = [], array $data = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $this->curl->addHeader('X-Api-Key', $this->apiKey);

        if (!empty($data)) {
            $this->curl->addHeader('Content-Type', 'application/json');
            $payload = $this->jsonSerializer->serialize($data);
        }

        switch (strtoupper($method)) {
            case 'POST':
                $this->curl->post($url, $payload ?? '');
                break;
            case 'DELETE':
                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                $this->curl->get($url);
                break;
            case 'PATCH':
                $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
                $this->curl->post($url, $payload ?? '');
                break;
            default:
                $this->curl->get($url);
                break;
        }

        $response = $this->curl->getBody();
        $httpCode = $this->curl->getStatus();

        $responseJson = [
            'url' => $url,
            'status_code' => $httpCode,
            'response'    => $this->jsonSerializer->unserialize($response),
            'error'       => $httpCode >= 400 ? $response : '',
        ];

        $this->logger->debug($this->anonymizeResponse($responseJson));

        return $responseJson;
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function requestBanner(string $endpoint, array $data): array
    {
        $url = $this->baseUrlBanner . '/' . ltrim($endpoint, '/');

        $payload = [];

        if (!empty($data)) {
            $this->curl->addHeader('Content-Type', 'application/json');
            $payload = $this->jsonSerializer->serialize($data);
        }

        $this->curl->setTimeout(self::TIME_OUT_API_BANNER);
        $this->curl->post($url, $payload);

        $response = $this->curl->getBody();
        $httpCode = $this->curl->getStatus();

        $responseArray = [
            'url' => $url,
            'status_code' => $httpCode,
            'response'    => $this->jsonSerializer->unserialize($response),
            'error'       => $httpCode >= 400 ? $response : '',
        ];

        $this->logger->debug($this->anonymizeResponse($responseArray));

        return $responseArray;
    }

    /**
     * @param array $response
     * @param $replacement
     * @return array
     */
    private function anonymizeResponse(array $response, $replacement = '***')
    {
        $keysToAnonymize = self::KEYS_ANONYMIZE;
        array_walk_recursive($response, function (&$value, $key) use ($keysToAnonymize, $replacement) {
            if (in_array($key, $keysToAnonymize, true)) {
                $value = substr($value, 4) . $replacement;
            }
        });

        return $response;
    }
}
