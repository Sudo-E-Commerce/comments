<?php
App::booted(function() {
	$namespace = 'Sudo\Comment\Http\Controllers';
	
	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// Bình luận
		Route::resource('comments', 'CommentController');
	});

	Route::namespace($namespace)->name('admin.ajax.comments.')->prefix(config('app.admin_dir').'/ajax')->middleware(['web'])->group(function() {
		// Trả lời nhanh
		Route::post('comments/{id}/quick_reply', 'CommentController@quickReply')->name('quick_reply');
		// Sửa nhanh
		Route::post('comments/{id}/quick_edit', 'CommentController@quickEdit')->name('quick_edit');
	});

	Route::namespace($namespace)->name('app.ajax.comments.')->prefix('/ajax')->middleware(['web'])->group(function() {
		// Load bình luận ajax
		Route::post('comments', 'AjaxController@loadComment')->name('load_comments');
		// Thêm/trả lời bình luận
		Route::post('comments/add', 'AjaxController@addComment')->name('add_comments');
		// Tìm kiếm
		Route::post('comments/search', 'AjaxController@searchComment')->name('search_comments');
	});
});