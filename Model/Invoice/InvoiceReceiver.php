<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Invoice;

use Tino\Payment\Api\InvoiceReceiverInterface;
use Magento\Framework\Exception\InputException;

class InvoiceReceiver implements InvoiceReceiverInterface
{

    private InvoiceProcessor $invoiceProcessor;

    public function __construct(
        InvoiceProcessor $invoiceProcessor
    ) {
        $this->invoiceProcessor = $invoiceProcessor;
    }

    /**
     * @inheritDoc
     */
    public function receiveInvoice(string $incrementId, array $nfes, bool $lastBatch): array
    {
        // Validate the webhook structure
        if (!$this->validatePayload($incrementId, $nfes)) {
            return ['error' => 'Invalid payload structure'];
        }

        $this->invoiceProcessor->processNfe($incrementId, $nfes, $lastBatch);

        return [];
    }

    /**
     * Validate the webhook payload.
     *
     * @param string $incrementId
     * @param array $nfes
     * @return bool
     * @throws InputException
     */
    private function validatePayload(string $incrementId, array $nfes): bool
    {
        $requiredFields = ['incrementId' => $incrementId, 'nfes' => $nfes];

        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                throw new InputException(__('This field "%1" is required.', $field));
            }
        }

        $requiredNfeFields = ['amount_cents', 'nf_data', 'items', "nf_external_id"];

        foreach ($nfes as $nf) {
            foreach ($requiredNfeFields as $field) {
                if (empty($nf[$field])) {
                    throw new InputException(__('This field "%1" is required in nfes.', [$field]));
                }
            }
        }

        return true;
    }
}
