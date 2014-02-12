

{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">	
		<legend>{{trans('catalog.create')}}</legend>	
			{{ Form::open(array('url' => 'catalog/create', 'method' => 'post')) }}
			{{ Form::hidden('course', $course['id']) }}
			
			{{ Form::label('name', trans('catalog.name')) }}
			{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid')) }}
			
			{{ Form::label('number', trans('catalog.number_create')) }}
			{{ Form::input('number', 'number', Input::old('number'), array('class' => 'row-fluid')) }}
			
			{{ Form::label('parent', trans('catalog.parent')) }}
			{{ Form::select('parent', $catalogs, Input::old('parent'), array('class' => 'row-fluid')) }}
			
			{{ Form::token() }}<br>
			{{ Form::submit(trans('catalog.create'), array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
		</div>
	</div>
</div>

@stop