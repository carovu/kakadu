
@section('content')

<div class="row-fluid">
	<div class="offset1 span11">
		<h5>{{trans('question.deleted')}}</h5>
		<h5>{{trans('question.link')}} {{ HTML::linkRoute('courses', trans('home.courses_link')) }}</h5>
	</div>
</div>

@stop