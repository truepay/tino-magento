<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class CancelResponseValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];

        $isValid = true;
        $errorMessages = [];

        if (!isset($response['object']['status_code']) || $response['object']['status_code'] !== 200) {
            $isValid = false;
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
