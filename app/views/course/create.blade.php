@extends('layouts.master')

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
			<legend>{{trans('profile.create_course')}}</legend>
			{{ Form::open(array('url' => 'course/create', 'method' => 'post', 'id'=>'courseCreate')) }}
			{{ Form::label('name', trans('course.name')) }}
			{{ Form::text('name', Form::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			
			{{ Form::label('description', trans('course.description')) }}
			{{ Form::textarea('description', Form::old('description'), array('class' => 'row-fluid', 'rows' => '6'))}}
			
			{{Form::label('group', trans('course.group_search'))}}			
			{{Form::input('text', 'group', '', array('id' => 'searchGroup', 'placeholder' => trans('course.search_group')))}}
			
			<div class="row-fluid" id="showGroups"></div>
			<div id="nothingFound" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{trans('course.not_found')}}</h5>
			</div>
			<div id="inList" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{trans('course.in_list')}}</h5>
			</div>
			
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="offset1">
		<div class="span8">			
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
			
			<button class="btn btn-small btn-primary" type="submit" name="create_course" id="create_course" onclick="$(course/create).submit()">{{trans('profile.create_course')}}</button>
		</div>
		<div class="span4">
			<table id="tabelReferences" class="table table-hover table-condensed">
				<thead>
					<th>{{trans('course.group_referenced')}}</th>
					<th>{{trans('course.remove_reference')}}</th>
				</thead>
				<tbody id="referencedCourses"></tbody>
			</table>
		</div>
		{{ Form::token() }}
		{{ Form::close() }}
	</div>
</div>

			
			



@stop