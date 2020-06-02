<div class="comments-add" @if (isset($type) && $type == 'reply') style="display: none;" @endif>
	<div class="comments-add__form">
		<textarea class="comments-add__form-field" name="content" placeholder="@lang('Bất cứ điều gì bạn quan tâm, hãy viết ở đây...')"></textarea>
	</div>
	<div class="comments-add__action">
		<div class="comments-add__action-left">
			<ul>
				@if (config('SudoComment.upload_image') == true)
					<li>
						@php
							if (isset($type) && $type == 'reply') {
								$image_id = 'comments_image_'.$parent_id??'';
							} else {
								$image_id = randString(20);
							}
						@endphp
						<input type="file" name="comment_file" id="{{$image_id ?? ''}}" accept="image/x-png, image/jpeg" multiple>
						<label for="{{$image_id ?? ''}}"><i class="fa fa-camera"></i> @lang('Gửi ảnh')</label>
					</li>
				@endif
				<li><a href="{{ $regulation_link ?? '#' }}">@lang('Qui định đăng bình luận')</a></li>
				@if (\Auth::guard('admin')->check() && checkRole('comments_index'))
					<li><a href="{{ route('admin.comments.index', [
						'search' => 1,
						'type' => $type ?? '',
						'type_id' => $type_id ?? '',
					]) }}" target="_blank">@lang('Quản trị bình luận')</a></li>
				@endif
			</ul>
		</div>
		<div class="comments-add__action-right">
			<button type="button" data-comments_moreinfo>@lang('Gửi')</button>
		</div>
	</div>
	{{-- Xem trước ảnh up lên --}}
	@if (config('SudoComment.upload_image') == true)
		<div class="comments-add__preview"></div>
	@endif
	<div class="comments-add__moreinfo">
		<div class="comments-add__moreinfo-group">
			<input type="text" name="name" placeholder="@lang('Họ và tên')">
		</div>
		<div class="comments-add__moreinfo-group">
			<input type="text" name="phone" placeholder="@lang('Điện thoại')">
		</div>
		<div class="comments-add__moreinfo-group">
			<input type="text" name="email" placeholder="@lang('Email')">
		</div>
		<div class="comments-add__moreinfo-group hidden">
			<input type="hidden" name="parent_id" value="{{ $parent_id ?? 0 }}">
		</div>
		<div class="comments-add__moreinfo-group">
			<button type="button" data-comments_submit><i class="fa fa-paper-plane"></i>@lang('Gửi bình luận')</button>
			<button type="button" data-comments_close><i class="fa fa-remove"></i>@lang('Đóng')</button>
		</div>
	</div>
</div>