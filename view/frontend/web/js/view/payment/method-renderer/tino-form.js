/*browser:true*/
/*global define*/
define([
    'underscore',
    'jquery',
    'ko',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'tinojs'
], function (_, $, ko, Component, quote, messageList, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Tino_Payment/payment/tino-form',
            config: null,
            elements: null,
            paymentElement: null,
            reservationId: null,
            externalId: null,
            urlBanner: ko.observable(null),
            logo: ko.observable(null),
            title: ko.observable(null)
        },

        /**
         * @returns {exports.initialize}
         */
        initialize: function () {
            this._super();
            var self = this;

            this.config = window.checkoutConfig.payment[this.getCode()];
            this.initTinoPayment();

            window.addEventListener('message', function (event) {
                if (event.data && event.data.type === 'tino-checkout-success') {

                    const paymentReservationId = event.data.reservationId;
                    const paymentExternalId = event.data.externalId;

                    // SAVE RESERVATION ID
                    self.setReservationId(paymentReservationId);
                    self.setExternalId(paymentExternalId);

                    // PLACE ORDER
                    self.placeOrder();
                }
            });

            return this;
        },

        /**
         * @returns {Boolean}
         */
        isActive: function () {
            return this.getCode() === this.isChecked();
        },

        /**
         * @returns {Object}
         */
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'reservation_id': this.reservationId,
                    'external_id': this.externalId
                }
            };
        },

        /**
         * @param {String} reservationId
         */
        setReservationId: function (reservationId) {
            this.reservationId = reservationId;
        },

        /**
         * @param {String} externalId
         */
        setExternalId: function (externalId) {
            this.externalId = externalId;
        },

        initTinoPayment: function () {
            var self = this;
            var paymentContent = JSON.parse(self.config.paymentContent);


            if (paymentContent.titlePayment != "" || paymentContent.titlePayment) {
                self.title(paymentContent.titlePayment);
            }

            if (paymentContent.bannerPayment != "" || paymentContent.bannerPayment) {
                self.urlBanner(paymentContent.bannerPayment);
            }

            if (paymentContent.logoPayment != "" || paymentContent.logoPayment) {
                self.logo(paymentContent.logoPayment);
            }
        },

        getTitlePayment: function () {
            return this.title();
        },

        getLogoPayment: function () {
            return this.logo();
        },

        getBannerPayment: function () {
            return this.urlBanner();
        },

        placeOrderTino: function () {
            if (!this.config) {
                return;
            }

            var checkoutConfig = window.checkoutConfig;

            const sdkPayload = {
                apiKey: this.config.sdkApiKey,
                externalId: this.config.externalIdAlias + quote.getQuoteId(),
                amountCents: Math.round(quote.totals().grand_total * 100),
                cart: {
                    address: quote.shippingAddress().street.join(' '),
                    zipCode: quote.shippingAddress().postcode,
                    shippingCents: Math.round(quote.totals().shipping_amount * 100),
                    discountCents: Math.round(quote.totals().discount_amount * 100),
                    items: quote.getItems().map(item => ({
                        name: item.name,
                        quantity: item.qty,
                        amountCents: Math.round(item.price * 100)
                    }))
                },
                customer: {
                    documentNumber: this.config.taxvat ? this.config.taxvat : quote.shippingAddress().vatId,
                    email: checkoutConfig.customerData.email
                },
                openingMode: 'self',
                transitionType: 'automatic'
            };

            window.tino.preconfig(sdkPayload);

            if (typeof window.tino === 'undefined' || !this.config) {
                messageList.addErrorMessage({ message: $t('Error loading payment. Please try again.') });
                return;
            }

            if (typeof window.tino.open !== 'function') {
                messageList.addErrorMessage({ message: $t('Error opening payment. Please try again.') });
                return;
            }

            window.tino.open();
        }
    });
});
