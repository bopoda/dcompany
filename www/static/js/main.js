preventOpenedTd = '';

jQuery.nl2br = function(varTest){
	return varTest.replace(/(\r\n|\n\r|\r|\n)/g, "<br>");
};

function saveOrderField(td)
{
	var $field = td.find('input');
	if (!$field.length) {
		$field = td.find('textarea');
	}
	if (!$field.length) {
		$field = td.find('select');
	}

	var orderId = td.parents('tr').attr('data-orderId');
	var fieldName = $field.attr('name');
	var fieldValue = $field.val();

	$.ajax({
		url: '/ajax/order/editField',
		type: 'post',
		data: {orderId: orderId, fieldName: fieldName, fieldValue: fieldValue},
		dataType: 'json',
		success: function(result) {
			if (result.success) {
				td.find('.order-text').html($.nl2br(result.fieldEscapedValue));
				td.find('.order-text').show();
				td.find('.order-edit [name='+fieldName+']').val(fieldValue);
				td.find('.order-edit').hide();
			}
		}
	});
}

function cancelOrderField(td)
{
	td.find('.order-text').show();
	td.find('.order-edit').hide();

	var $field = td.find('input');
	if (!$field.length) {
		$field = td.find('textarea');
	}
	if (!$field.length) {
		$field = td.find('select');
	}

	$field.val(td.find('.order-text').text());
}

jQuery(function($) {
	$('[data-rel=tooltip]').tooltip({container:'body'});
	$('[data-rel=popover]').popover({container:'body'});

	$('textarea[class*=autosize]').autosize({append: "\n"});

	// sidebar menu
	var pathname = window.location.pathname;
	$('#sidebar a').each(function() {
		if ($(this).attr('href') == pathname) {
			$(this).parents('li').addClass('active');
		}
	});

	//редактирование полей заказа
	$('#orders-editable td').click(function(e) {
		if (!($(this).find('.order-edit').length)) {
			return false;
		}
		if (
			e.target.nodeName == 'BUTTON'
			|| e.target.nodeName == 'TEXTAREA'
			|| e.target.nodeName == 'INPUT'
			|| e.target.nodeName == 'SELECT'
		) {
			return false;
		}

		if ($(this).find('.order-edit').is(":visible")) {
			return false;
		}

		if (preventOpenedTd) {
			cancelOrderField(preventOpenedTd);
		}
		preventOpenedTd = $(this);

		// вставляем кнопки, если ещё нету.
		if (!($(this).find('.order-edit button.btn-orderField-edit').length)) {
			$(this).find('.order-edit').append('<br>' +
				'<button type="button" onClick="return saveOrderField($(this).parents(\'td\'));" class="btn btn-orderField-edit btn-success btn-xs">Сохранить</button>&nbsp;&nbsp;&nbsp; ' +
				'<button type="button" onClick="return cancelOrderField($(this).parents(\'td\'));" class="btn btn-default btn-xs">Отменить</button>'
			);
		}

		$(this).find('.order-text').hide();
		$(this).find('.order-edit').show();
	});

});