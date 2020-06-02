@foreach ($comment_parents as $parent)
	<div class="item" data-comment_id="{{$parent->id}}">
		<div class="item-author">
			<div class="item-author__avatar">{{ getNameKey($parent->getName()) }}</div>
			<div class="item-author__name">{{ $parent->getName() }}</div>
			@if ($parent->admin_id != 0)
				<div class="item-author__role">@lang('Quản trị viên')</div>
			@endif
		</div>
		<div class="item-content">{{ $parent->content }}</div>
		@if (!empty($parent->image))
			@php
				$parent_image = explode(',', $parent->image);
			@endphp
			<div class="item-gallery">
				@foreach ($parent_image as $image)
					<div class="item-image popup-image" data-image="{{getImage($image)}}">
						<img src="{{getImage($image, 'tiny')}}" alt="">
					</div>
				@endforeach
			</div>
		@endif
		<div class="item-action">
			<div class="item-action__reply" data-reply="{{ '@'.$parent->getName().': ' }}"><span>@lang('Trả lời')</span></div>
			<div class="item-action__time"><span>{{$parent->getTime()}}</span></div>
		</div>
		@php
			$childs = $comment_childs->where('parent_id', $parent->id);
		@endphp
		@if (isset($childs) && count($childs) > 0)
			<div class="item-child">
				@foreach ($childs as $child)
					<div class="item">
						<div class="item-author">
							<div class="item-author__avatar">{{ getNameKey($child->getName()) }}</div>
							<div class="item-author__name">{{ $child->getName() }}</div>
							@if ($child->admin_id != 0)
								<div class="item-author__role">@lang('Quản trị viên')</div>
							@endif
						</div>
						<div class="item-content">{{ $child->content }}</div>
						@if (!empty($child->image))
							@php
								$parent_image = explode(',', $child->image);
							@endphp
							<div class="item-gallery">
								@foreach ($parent_image as $image)
									<div class="item-image popup-image" data-image="{{getImage($image)}}">
										<img src="{{getImage($image, 'tiny')}}" alt="">
									</div>
								@endforeach
							</div>
						@endif
						<div class="item-action">
							<div class="item-action__reply" data-reply="{{ '@'.$child->getName().': ' }}"><span>@lang('Trả lời')</span></div>
							<div class="item-action__time"><span>{{$child->getTime()}}</span></div>
						</div>
					</div>
				@endforeach
			</div>
		@endif
		<div class="item-reply">
			@include('Comment::add', [
				'type' => 'reply',
				'parent_id' => $parent->id,
			])
		</div>
	</div>
@endforeach