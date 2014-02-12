
{{-- Sidebar --}}
@section('sidebar')
	<li class="nav-header">{{trans('group.sidebar_title')}}</li>
	<li id="create">{{HTML::linkRoute('group/create', trans('profile.create_group'))}}</li>

	<li class="nav-header">{{trans('course.sidebar_title')}}</li>
	<li id="create">{{HTML::linkRoute('course/create', trans('profile.create_course'))}}</li>
@stop

{{--Content--}}
@section('content')


<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{trans('group.edit')}}</legend>
			{{ Form::open(array('url' => 'group/edit', 'method' => 'post')) }} 
			{{ Form::hidden('id', $group['id']) }}

			{{ Form::label('name', trans('group.name')) }}
			@if(Input::old('name') != '')
				{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $group['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('description', trans('group.description')) }}
			@if(Input::old('description') != '')
				{{ Form::textarea('description', Input::old('description'), array('class' => 'row-fluid', 'rows' => '6')) }}
			@else
				{{ Form::textarea('description', $group['description'], array('class' => 'row-fluid', 'rows' => '6')) }}
			@endif
			
			{{ Form::token() }}
			<br>
			<button class="btn btn-small btn-primary" type="submit" name="edit_group" id="edit_group" onclick="$(group/edit).submit()">{{trans('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>



@stop
