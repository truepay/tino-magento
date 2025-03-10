<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Config;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const URL_API_STAGING = "https://stg.supplier-api.truepay.com.br";
    const URL_API_PRODUCTION = "https://supplier-api.truepay.app";
    const URL_API_BANNER_STAGING = "https://us-east1-vtex-payment-provider-stg-001.cloudfunctions.net/banner";
    const URL_API_BANNER_PRODUCTION = "https://us-east1-vtex-payment-provider-prd-001.cloudfunctions.net/banner";

    const CONFIG_PATH_ACTIVE = 'active';
    const CONFIG_PATH_TITLE = 'title';
    const CONFIG_PATH_PRODUCTION_MODE = 'production_mode';
    const CONFIG_PATH_API_KEY_STAGING = 'api_key_staging';
    const CONFIG_PATH_SDK_API_STAGING = 'sdk_api_key_staging';
    const CONFIG_PATH_API_KEY_PRODUCTION = 'api_key_production';
    const CONFIG_PATH_SDK_API_PRODUCTION = 'sdk_api_key_production';
    const CONFIG_PATH_STORE_CNPJ = 'store_cnpj';
    const CONFIG_PATH_CNPJ_ATTRIBUTE = 'cnpj_attribute';

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->getValue(self::CONFIG_PATH_ACTIVE);
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getValue(self::CONFIG_PATH_TITLE);
    }

    /**
     * @return bool
     */
    public function isProductionMode(): bool
    {
        return (bool) $this->getValue(self::CONFIG_PATH_PRODUCTION_MODE);
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        if (!$this->isProductionMode()) {
            return $this->getValue(self::CONFIG_PATH_API_KEY_STAGING);
        }

        return $this->getValue(self::CONFIG_PATH_API_KEY_PRODUCTION);
    }

    /**
     * @return string|null
     */
    public function getSdkApiKey(): ?string
    {
        if (!$this->isProductionMode()) {
            return $this->getValue(self::CONFIG_PATH_SDK_API_STAGING);
        }

        return $this->getValue(self::CONFIG_PATH_SDK_API_PRODUCTION);
    }

    /**
     * @return string|null
     */
    public function getStoreCnpj(): ?string
    {
        return $this->getValue(self::CONFIG_PATH_STORE_CNPJ);
    }

    /**
     * @return string|null
     */
    public function getCnpjAttribute(): ?string
    {
        return $this->getValue(self::CONFIG_PATH_CNPJ_ATTRIBUTE);
    }

    /**
     * @return string
     */
    public function getUrlApiBanner(): string
    {
        if (!$this->isProductionMode()) {
            return self::URL_API_BANNER_STAGING;
        }

        return self::URL_API_BANNER_PRODUCTION;
    }


    /**
     * @return string
     */
    public function getBaseUrlApi(): string
    {
        if (!$this->isProductionMode()) {
            return self::URL_API_STAGING;
        }

        return self::URL_API_PRODUCTION;
    }

    /**
     * @return string
     */
    public function getAliasExternalId(): string
    {
        return "magento_";
    }

}
