

{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{trans('catalog.edit')}}</legend>
			{{ Form::open(array('url' => 'catalog/edit', 'method' => 'post')) }}			
	
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('id', $catalog['id']) }}
			
			{{ Form::label('name', trans('catalog.name')) }}
			@if(Input::old('name') != '')
				{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $catalog['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('number', trans('catalog.number')) }}
			@if(Input::old('name') != '')
				{{ Input::old('number', Input::old('number'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Input::old('number', $catalog['number'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('parent', trans('catalog.parent')) }}
			@if(Input::old('parent') != '')
				{{ Form::select('parent', $catalogs, Input::old('parent'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::select('parent', $catalogs, $catalog['parent'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::token() }}
			<button class="btn btn-small btn-primary" type="submit" name="change_course" id="change_course" onclick="$(catalog/edit).submit()">{{trans('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>

@stop