
<div class="accordion" id="accordion1">
	<!-- For loop over all courses  -->
	@foreach ($courses as $course)
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#{{$course['id']}}">
				{{$course['name'] }} <i class="icon-chevron-down"></i>
				<div class="btn-group pull-right">					
					<button onclick="linkcourse({{$course['id']}})"; class="btn btn-small">{{trans('course.show')}}</button>
					@if(Sentry::check())
						@if($course['favorite'] == '')		
							<button id="favorite{{$course['id']}}" onclick="addFavoriteCourse({{$course['id']}})"; class="btn btn-small" title="{{trans('favorites.favorite')}}"><i class="icon-star"></i></button>
						@endif
					@endif
					<button href="#" class="btn btn-small" onclick="linktest({{$course['id']}})">{{trans('favorites.learn')}}</button>				
				</div>
			</a>
		</div>
		<div id="{{$course['id']}}" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">{{ $course['description'] }}</div>
		</div>
	</div>
	@endforeach
</div>
{{ $links }}