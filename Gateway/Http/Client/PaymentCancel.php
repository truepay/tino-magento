<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Http\Client;

use Exception;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Tino\Payment\Api\ApiInterface;

class PaymentCancel implements ClientInterface
{

    private Logger $logger;
    private ApiInterface $api;

    public function __construct(
        ApiInterface $api,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->api = $api;
    }

    /**
     * @inheritDoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $response = [
            'object' => [],
        ];

        $log = [
            'request' => $data,
            'client' => static::class
        ];

        $reservationId = $data['reservation_id'] ?? "";

        try {
            $response['object'] = $this->api->cancelReservation($reservationId);
        } catch (Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->debug([$message]);
            throw new ClientException($message);
        } finally {
            $log['response'] = (array) $response['object'];
            $this->logger->debug($log);
        }

        return $response;
    }
}
