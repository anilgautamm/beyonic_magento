define(
        [
            'jquery',
            'Magento_Checkout/js/view/payment/default',
            'mage/url',
            'Magento_Checkout/js/model/quote'
        ],
        function ($, Component, url, quote) {
            'use strict';
            return Component.extend({
                defaults: {
                    template: 'Beyonic_Beyonicgateway/payment/Beyonicgateway'
                },
                validate: function () {
                    var telephone = quote.billingAddress().telephone;
//                    if (typeof window.checkoutConfig.customerData.addresses != 'undefined') {
//                        for (var i = 0; i < window.checkoutConfig.customerData.addresses.length; i++) {
//                            if (typeof window.checkoutConfig.customerData.addresses[i].default_billing != 'undefined' && window.checkoutConfig.customerData.addresses[i].default_billing == 1) {
//                                var telephone = window.checkoutConfig.customerData.addresses[i].telephone;
//                            } else {
//                                var telephone = window.checkoutConfig.customerData.addresses[i].telephone;
//                            }
//                            var reg = new RegExp('^\\+[0-9]*$');
//                            if (reg.test(telephone)) {
//                                return true;
//                            }
//                            alert("Please make sure your billing phone number is in international format, starting with a + sign before place order.");
//                            return false;
//                        }
//                    }
//                    var telephone = jQuery("input[name='telephone']").val();
                    var reg = new RegExp('^\\+[0-9]*$');
                    if (reg.test(telephone)) {
                        return true;
                    }
                    alert("Please make sure your billing phone number is in international format, starting with a + sign before place order.");
                    return false;
                },
                getBeyonicDescription: function () {
                    return window.checkoutConfig.payment.beyonicgateway.beyonic_description;
                },
                getBeyonicDescription2: function () {
                    return window.checkoutConfig.payment.beyonicgateway.beyonic_description2;
                },
                getBeyonicTelephone: function () {

                    return quote.billingAddress().telephone;

//                    if (typeof window.checkoutConfig.customerData.addresses != 'undefined') {
//                        for (var i = 0; i < window.checkoutConfig.customerData.addresses.length; i++) {
//                            if (typeof window.checkoutConfig.customerData.addresses[i].default_billing != 'undefined' && window.checkoutConfig.customerData.addresses[i].default_billing == 1) {
//                                return window.checkoutConfig.customerData.addresses[i].telephone;
//                            } else {
//                                console.log(quote.billingAddress());
//                            }
//                        }
//                    }
//                    return jQuery("input[name='telephone']").val();
                },
                redirectAfterPlaceOrder: false,
                afterPlaceOrder: function () {
                    window.location.replace(url.build('beyonicgateway/redirect/index/'));
                }
            });
        }
);