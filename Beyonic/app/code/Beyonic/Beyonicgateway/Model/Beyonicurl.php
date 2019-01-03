<?php

namespace Beyonic\Beyonicgateway\Model;

use Magento\Backend\Block\Template\Context;

class Beyonicurl extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $output = "";
        $output .='<tr id="row_payment_us_beyonicgateway_ipn_url"><td class="label"  style="width: 28% !important;"><label for="payment_us_beyonicgateway_ipn_url"><span data-config-scope="[GLOBAL]">Callback notification URL</span></label></td><td class="value with-tooltip" style="width: 60% !important;"><input id="payment_us_beyonicgateway_ipn_url" name="groups[beyonicgateway][fields][ipn_url][value]" data-ui-id="text-groups-beyonicgateway-fields-ipn-url-value" value="' . $storeManager->getStore()->getBaseUrl() . 'beyonicgateway/redirect/ipn/" class=" input-text admin__control-text" type="text" readonly><div class="tooltip"><span class="help"><span></span></span><div class="tooltip-content">This is the notification URL that will be used to send payment notifications to your website. You do not need to change it. NOTE : It must start with "https". If it does not start with https, then that means that your website does not have a secure HTTPS certificate.</div></div></td><td class=""></td></tr>';
        return $output;
    }

}
