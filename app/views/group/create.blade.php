
{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{trans('group.create')}}</legend>
			{{ Form::open(array('url' => 'group/create', 'method' => 'post')) }} 
			{{ Form::label('name', trans('group.name')) }}
			{{ Form::text('name', Form::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			
			{{ Form::label('description', trans('group.description')) }}
			{{ Form::textarea('description', Form::old('description'), array('class' => 'row-fluid', 'rows' => '6'))}}
			
			{{ Form::token() }}
			<br>
			<button class="btn btn-small btn-primary" type="submit" name="create_group" id="create_group" onclick="$(api/v1/group/create).submit()">{{trans('group.create')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>

@stop
