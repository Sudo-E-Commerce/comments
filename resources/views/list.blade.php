@php
	/*
		@include('Comment::list', [
			'type' => 'posts',
			'type_id' => 1,
			'regulation_link' => '#',
			'no_comment_text' => 'Hãy để lại bình luận của bạn tại đây!'
		])
	*/
	$comments = \Sudo\Comment\Models\Comment::loadComment($type, $type_id, 4);
	$comment_totals = $comments['comment_totals'] ?? 0;
	$comment_parents = $comments['comment_parents'] ?? [];
	$comment_childs = $comments['comment_childs'] ?? [];
@endphp
<link rel="stylesheet" href="{{ asset('platforms/comments/web/css/comments.min.css') }}">
<div class="comments" data-type="{{ $type ?? '' }}" data-type_id="{{ $type_id ?? '' }}">
	@include('Comment::add')
	<div class="comments-info">
		<div class="comments-info__total">
			<p class="comments-info__total-text"><span class="total-comment">{{$comment_totals ?? 0}}</span> @lang('Bình luận')</p>
		</div>
		<div class="comments-info__filter">
			<input type="text" name="keyword" placeholder="@lang('Tìm theo nội dung, người gửi...')">
			<button type="submit" data-comments_search><i class="fa fa-search"></i></button>
		</div>
	</div>
	<div class="comments-content">
		<div class="comments-list">
			@if (count($comment_parents) > 0)
				@include('Comment::item')
			@else
				<p class="no-comments">@lang($no_comment_text ?? 'Hãy để lại bình luận của bạn tại đây!')</p>
			@endif
		</div>
		<div class="comments-loadmore" @if ($comment_parents->total() <= config('SudoComment.page_number')) style="display: none;" @endif>
			<button type="button" data-comments_loadmore data-page_cmt="{{$comment_parents->currentPage()}}">@lang('Xem thêm')</button>
		</div>
	</div>
	@php
		$variable = base64_encode(json_encode([
			'page_number' => config('SudoComment.page_number'),
			'upload_image' => config('SudoComment.upload_image'),
			'allowed_size' => config('SudoMedia.allowed_size'),
			'valid_size' => __('Các File phải có kích thước nhỏ hơn'),
			'valid_extention' => __('Định dạng cho phép là'),
			'valid_empty' => __('Bạn cần nhập đấy đủ thông tin.'),
			'valid_format_phone' => __('Định dạng số điện thoại không chính xác.'),
			'valid_format_email' => __('Định dạng Email không chính xác.'),
			'ajax_load_error_text' => __('Có lỗi xảy ra. Vui lòng thử lại!'),
			'ajax_load_url' => route('app.ajax.comments.load_comments'),
			'ajax_add_url' => route('app.ajax.comments.add_comments'),
			'ajax_search_url' => route('app.ajax.comments.search_comments'),
		]));
	@endphp
	<div  class="variable" data-value="{{$variable ?? ''}}" ></div>
	<div  class="comments-loading"><div class="comments-loading__box"></div></div>
	<div  class="comments-popup">
		<div class="comments-popup__close"><i class="fa fa-remove"></i></div>
		<div class="comments-popup__body">
				<img src="{{getImage()}}" alt="">
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{{ asset('platforms/comments/web/js/comments.min.js') }}"></script>