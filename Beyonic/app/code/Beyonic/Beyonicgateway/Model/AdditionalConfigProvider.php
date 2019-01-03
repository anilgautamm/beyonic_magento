<?php

namespace Beyonic\Beyonicgateway\Model;

class AdditionalConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface {

    const CODE = 'beyonicgateway';

    protected $beyonic_description;
    protected $beyonic_description2;

    /**
     * Payment ConfigProvider constructor.
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->beyonic_description = $scopeConfig->getValue('payment/beyonicgateway/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->beyonic_description2 = $scopeConfig->getValue('payment/beyonicgateway/description2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig() {
        $output['payment'][self::CODE]['beyonic_description'] = $this->beyonic_description;
        $output['payment'][self::CODE]['beyonic_description2'] = $this->beyonic_description2;
        return $output;
    }

}
