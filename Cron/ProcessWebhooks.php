<?php
declare(strict_types=1);

namespace Tino\Payment\Cron;

use Psr\Log\LoggerInterface;
use Tino\Payment\Gateway\Config\Config;
use Tino\Payment\Model\Webhook\WebhookProcessor;

class ProcessWebhooks
{
    private WebhookProcessor $webhookProcessor;
    private LoggerInterface $logger;
    private Config $config;

    public function __construct(
        WebhookProcessor $webhookProcessor,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->webhookProcessor = $webhookProcessor;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isActive()){
            return;
        }

        try {
            $this->webhookProcessor->processOrdersWebhooks();
        } catch (\Exception $e) {
            $this->logger->error('Error during webhook tino processing cron job: ' . $e->getMessage());
        }
    }
}
