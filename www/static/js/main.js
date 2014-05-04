jQuery(function($) {

    $('textarea[class*=autosize]').autosize({append: "\n"});

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

    $('.order-enable-edit').click(function() {
        var tr = $(this).parents('tr');

        var $orderPosition = tr.find('td.order .order-edit .order-position');
        // make order edit rows
        tr.find('table.order-table tr').each(function(index) {
            var hardware = $(this).find('.hardware').text();
            var purchase = $(this).find('.purchase-price').text();
            var supplier = $(this).find('.supplier').text();
            if (index == 0) {
                $orderPosition.find('.order-hardware').val(hardware);
                $orderPosition.find('.order-purchase-price').val(purchase);
                $orderPosition.find('.order-supplier').val(supplier);
            }
            else {
                var $clone = $orderPosition.clone();
                $clone.find('.order-hardware').val(hardware);
                $clone.find('.order-purchase-price').val(purchase);
                $clone.find('.order-supplier').val(supplier);

                tr.find('td.order .order-edit').append($clone);
            }
        });

        tr.find('td.delivery_time [name=delivery_time]').val(tr.find('td.delivery_time .order-text').text());
        tr.find('td.contacts [name=contacts]').val(tr.find('td.contacts .order-text').text());
        tr.find('td.notes [name=notes]').val(tr.find('td.notes .order-text').text());
        tr.find('td.delivery_address [name=delivery_address]').val(tr.find('td.delivery_address .order-text').text());
        tr.find('td.sale_price [name=sale_price]').val(tr.find('td.sale_price .order-text').text());
        tr.find('td.sale_price [name=sale_price]').val(tr.find('td.sale_price .order-text').text());
        tr.find('td.assembly_price [name=assembly_price]').val(tr.find('td.assembly_price .order-text').text());
        tr.find('td.delivery_price [name=delivery_price]').val(tr.find('td.delivery_price .order-text').text());

        tr.find('td').each(function() {
            $(this).find('.order-text').hide();
            $(this).find('.order-edit').show();
        });
    });
});

// пересчёт общей цены закупки по позициям
function recalculateTotalPurchase($orderTd)
{
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
