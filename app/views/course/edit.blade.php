
{{-- Scripts --}}
@section('scripts')

	{{ HTML::script('js/addGroup.js')}}
	
	<script>
	$(document).ready(function() {
		//initialises the js-file addGroup (can be found in public/js/addGroup).
		initialiseAddGroup("{{URL::to('/api/v1')}}");
	});
	
	</script>

@stop

@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{trans('course.edit_course')}}</legend>
			{{ Form::open(array('url' => 'course/edit', 'method' => 'post')) }} 	
	
			{{ Form::hidden('id', $course['id']) }}
			
			{{ Form::label('name', trans('course.name')) }}
			@if(Form::old('name') != '')
				{{ Form::text('name', Form::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $course['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('description', trans('course.description')) }}
			@if(Form::old('description') != '')
				{{ Form::textarea('description', Form::old('description'), array('class' => 'row-fluid', 'rows' => '6')) }}
			@else
				{{ Form::textarea('description', $course['description'], array('class' => 'row-fluid', 'rows' => '6')) }}
			@endif
			
			{{Form::label('group', trans('course.group'))}}

			@if(!isset($group))
				<div class="input-append">
				  {{Form::input('text', 'group', '', array('id' => 'searchGroup', 'placeholder' => trans('course.search_group')))}}
				  <button class="btn" type="button" onclick="removeReference({{trans('course.search_group')}});"><i class="icon-remove"></i></button>
				</div>
			@else
				<div class="input-append">
				  {{Form::input('text', 'group', $group['name'], array('id' => 'searchGroup', 'placeholder' => trans('course.search_group')))}}
				  <button class="btn" type="button" onclick="removeReference('{{trans('course.search_group')}}');"><i class="icon-remove"></i></button>
				</div>
			@endif
			
			<div class="row-fluid" id="showGroups"></div>
			<div id="nothingFound" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{trans('course.not_found')}}</h5>
			</div>
			<table id="groupsTable" class="table table-hover table-condensed">
				<thead>
					<tr>
						<th>{{trans('group.name')}}</th>
						<th>{{trans('group.description')}}</th>
						<th>{{trans('course.add')}}</th>
					</tr>
				</thead>
				<tbody id="groups_search">
					
				</tbody>
			</table>
				
			@if(!isset($group))
				{{ Form::hidden('group', null, array('id' => 'groupId')) }}
			@else
				{{ Form::hidden('group', $group['id'], array('id' => 'groupId')) }}
			@endif
			
			
			{{ Form::token() }}
			<button class="btn btn-small btn-primary" type="submit" name="change_course" id="change_course" onclick="$(course/edit).submit()">{{trans('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>





@stop