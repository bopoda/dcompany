jQuery(function($) {
    // sidebar menu
    var pathname = window.location.pathname;
    $('#sidebar a').each(function() {
        if ($(this).attr('href') == pathname) {
            $(this).parents('li').addClass('active');
        }
    });

    $('.btn-add-order').click(function(){
        var $addOrderField = $('#newOrder');
        var curDate = $(this).parents('td').find('.value').text();

        if (!$addOrderField.length) {
            $.ajax({
                url: '/ajax/order/addOrderHtml',
                type: 'post',
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        $('#main-container .col-xs-12').append(result.html);
                        $('.date-picker').datepicker({
                            autoclose: true,
                            todayHighlight: true
                        });
                        $addOrderField = $('#newOrder');
                        prepareAddOrderAfterAddBtnClick($addOrderField, curDate);
                    }
                }
            });
        }
        else {
            prepareAddOrderAfterAddBtnClick($addOrderField, curDate);
        }
    });

    // обработка вставки строк c excel
    $(".row").delegate( ".order-hardware", "paste", function() {
        var _this = this;

        var $orderPosition = $(this).parents('.order-position');

        // Short pause to wait for paste to complete
        setTimeout( function() {
            var text = $(_this).val();
            var chunks = text.split('\n'); //explode rows

            if (chunks.length < 1) {
                return true;
            }

            var result = [];
            $(chunks).each(function(index, hardwareString) {
                hardwareString = jQuery.trim(hardwareString);
                var pattern = /^(.+)\t([\d.]+)$/i;
                var match = hardwareString.match(pattern);

                var arr = [];
                arr['hardware'] = match ? match[1] : hardwareString;
                arr['cost'] = match ? match[2] : '';
                result.push(arr);
            });

            if (result) {
                $(result).each(function(index, data) {
                    if (index == 0) {
                        $orderPosition.find('.order-hardware').val(data['hardware']);
                        $orderPosition.find('.order-purchase-price').val(data['cost']);
                    }
                    else {
                        var $clone = $orderPosition.clone();
                        $clone.find('.order-hardware').val(data['hardware']);
                        $clone.find('.order-purchase-price').val(data['cost']);
                        $(_this).parents('td').append($clone);
                    }
                });

                recalculateTotalPurchase($(_this).parents('td'));
            }

        }, 100);
    });

    // изменение цены закупки
    $(".row").delegate( ".order-purchase-price", "change", function() {
        var $orderTd = $(this).parents('td');
        var purchasePrice = recalculateTotalPurchase($orderTd);
    });

});

function recalculateTotalPurchase($orderTd)
{
    console.log($orderTd);

    if (!$orderTd.hasClass('order')) {
        console.log('call recalculateTotalPurchase with wrong parameter');
        return;
    }

    var purchasePrice = 0;
    $orderTd.find('.order-purchase-price').each(function() {
        var price = parseInt($(this).val());
        if (price && price != 'NaN') {
            purchasePrice += price;
        }
    });

    var text = purchasePrice ? purchasePrice + '$' : '';

    $('#calculatedPurchasePrice').text(text);
}

function prepareAddOrderAfterAddBtnClick($addOrderField, curDate)
{
    $addOrderField.find('[name=delivery_date]').val(curDate);
    $addOrderField[0].scrollIntoView();
    recalculateTotalPurchase($addOrderField.find('td.order'));
}
