/*browser:true*/
/*global define*/
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'tino_payment',
            component: 'Tino_Payment/js/view/payment/method-renderer/tino-form'
        }
    );

    return Component.extend({});
});
