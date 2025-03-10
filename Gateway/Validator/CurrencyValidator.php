<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class CurrencyValidator extends AbstractValidator
{
    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (isset($validationSubject['currency']) && $validationSubject['currency'] != 'BRL') {
            return $this->createResult(false);
        }

        return $this->createResult(
            true,
            ['status' => 200]
        );
    }
}
