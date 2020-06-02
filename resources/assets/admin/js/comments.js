$(document).ready(function() {
	// Sửa bình luận nhanh
	$('body').on('click', '*[data-quick_edit_comment]', function(e) {
		e.preventDefault();
		e = $(this);
		form = e.closest('form');
		id = $(this).closest('tr').data('id');
		content = form.find('textarea[name=edit_content_'+id+']').val();
		image = [];
		$.each(form.find('input[name^=edit_image_'+id+']'), function() {
			image.push($(this).val());
		})
		if (checkEmpty(content)) {
			alertText($(this).data('empty'), 'error');
		} else {
			data = {
				content: content,
				image: image,
			};
			loadAjaxPost(form.attr('action'), data, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        	} else {
		        		alertText(result.message, 'error');
		        	}
		        	e.closest('.modal').find('.close').click();
		        	loadData('no_animate');
		        },
		        error: function (error) {}
		    }, 'progress');	
		}
	});
	// Trả lời bình luận nhanh
	$('body').on('click', '*[data-quick_reply_comment]', function(e) {
		e.preventDefault();
		e = $(this);
		form = e.closest('form');
		id = $(this).closest('tr').data('id');
		// Giá trị trong form
		type = form.find('input[name=type]').val();
		type_id = form.find('input[name=type_id]').val();
		name = form.find('input[name=name_'+id+']').val();
		phone = form.find('input[name=phone_'+id+']').val();
		email = form.find('input[name=email_'+id+']').val();
		content = form.find('textarea[name=content_'+id+']').val();
		image = [];
		$.each(form.find('input[name^=image_'+id+']'), function() {
			image.push($(this).val());
		})
		if (checkEmpty(content)) {
			alertText($(this).data('empty_content'), 'error');
		} else if (checkEmpty(name)) {
			alertText($(this).data('empty_name'), 'error');
		} else {
			data = {
				type: type,
				type_id: type_id,
				name: name,
				phone: phone,
				email: email,
				image: image,
				content: content,
			};
			loadAjaxPost(form.attr('action'), data, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        	} else {
		        		alertText(result.message, 'error');
		        	}
		        	e.closest('.modal').find('.close').click();
		        	loadData('no_animate');
		        },
		        error: function (error) {}
		    }, 'progress');
		}
	});
});