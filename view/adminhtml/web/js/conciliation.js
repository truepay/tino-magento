define([
    'jquery'
], function ($) {

    return function (config) {

        var currentDate = new Date().toISOString().split('T')[0];
        var minDate = new Date();
        minDate.setDate(minDate.getDate() - 90);
        var formattedMinDate = minDate.toISOString().split('T')[0];

        $('#start_date').attr('max', currentDate);
        $('#start_date').attr('min', formattedMinDate);

        $('#end_date').attr('max', currentDate);
        $('#end_date').attr('min', formattedMinDate);

        $('#start_date').on('change', function () {
            var startDate = $(this).val();
            $('#end_date').attr('min', startDate);
            $('#end_date').val('');
        });

        $('.tino.action-primary').on('click', function () {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var type = $(this).attr('id');
            var messagesContainer = $('.messages');

            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {start_date: startDate, end_date: endDate, type: type},
                showLoader: true,
                success: function (response) {
                    var messageType = response.error ? 'error' : 'success';

                    if (response.error && response.message) {
                        showMessage(response.message, messageType);
                    }

                    if (!response.error && response.url) {
                        window.location.href = response.url;
                        showMessage(response.message, messageType);
                    }
                }
            });

            function showMessage(message, type) {
                var messageHtml = '<div class="message message-' + type + '"><div>' + message + '</div></div>';
                messagesContainer.prepend(messageHtml);
                setTimeout(function () {
                    $('.message').fadeOut();
                }, 5000);
            }
        });
    }
});
