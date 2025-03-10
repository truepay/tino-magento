<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class RequestBuilder implements BuilderInterface
{

    private BuilderInterface $builderComposite;

    public function __construct(BuilderInterface $builder)
    {
        $this->builderComposite = $builder;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        return $this->builderComposite->build($buildSubject);
    }
}
