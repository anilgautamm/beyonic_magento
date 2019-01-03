define(
        [
            'uiComponent',
            'Magento_Checkout/js/model/payment/renderer-list'
        ],
        function (
                Component,
                rendererList
                ) {
            'use strict';
            rendererList.push(
                    {
                        type: 'beyonicgateway',
                        component: 'Beyonic_Beyonicgateway/js/view/payment/method-renderer/beyonicgateway'
                    }
            );
            return Component.extend({});
        }
);
