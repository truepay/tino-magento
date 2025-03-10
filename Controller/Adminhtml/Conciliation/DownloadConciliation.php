<?php

declare(strict_types=1);

namespace Tino\Payment\Controller\Adminhtml\Conciliation;

use Magento\Framework\App\ActionInterface;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Tino\Payment\Api\ApiInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class DownloadConciliation implements ActionInterface
{
    private JsonFactory $jsonFactory;
    private RequestInterface $request;
    private ApiInterface $api;
    private DateTime $dateTime;

    public function __construct(
        JsonFactory $jsonFactory,
        ApiInterface $api,
        RequestInterface $request,
        DateTime $dateTime
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->api = $api;
        $this->request = $request;
        $this->dateTime = $dateTime;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $startDate = $this->request->getParam('start_date');
        $endDate = $this->request->getParam('end_date');
        $type = $this->request->getParam('type');

        if (!$startDate) {
            $startDate = $this->dateTime->date('Y-m-d', strtotime('-90 days'));
        }

        if (!$endDate) {
            $endDate = $this->dateTime->date('Y-m-d');
        }

        $resultJson = $this->jsonFactory->create();

        $urlDownload = $this->getUrlFromApi($type, $startDate, $endDate);

        if (!$urlDownload) {
            return $resultJson->setData(
                ['error' => true, 'message' => __('Unable to download at this time.')]
            );
        }

        return $resultJson->setData(['url' => $urlDownload , 'message' => __('Download successful!')]);
    }

    /**
     * @param $type
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    private function getUrlFromApi($type, $startDate, $endDate)
    {
        $urlDownload = "";
        $response = $this->api->downloadStatementsReport($type, $startDate, $endDate);

        if ($response['status_code'] == 200 && isset($response['response']['downloadUrl'])) {
            $urlDownload = $response['response']['downloadUrl'];
        }

        return $urlDownload;
    }
}
