@extends('layouts.master')

{{-- Sidebar --}}
@section('sidebar')
    <li class="nav-header">{{trans('group.sidebar_title')}}</li>
    <li id="create">{{HTML::linkRoute('group/create', trans('profile.create_group'))}}</li>

    <li class="nav-header">{{trans('course.sidebar_title')}}</li>
    <li id="create">{{HTML::linkRoute('course/create', trans('profile.create_course'))}}</li>
@stop

{{-- Content --}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span4">
			<i class="offset2 icon-folder-open-alt icon-4x"></i>
			<h3>{{trans('descriptions.course')}}</h3>
			<p>{{trans('descriptions.course_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset2 icon-group icon-4x"></i>
			<h3>{{trans('descriptions.groups2')}}</h3>
			<p>{{trans('descriptions.group_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset3 icon-lightbulb icon-4x"></i>
			<h3>{{trans('descriptions.algorythm2')}}</h3>
			<p>{{trans('descriptions.algorythm_description')}}</p>
		</div>
	</div>
</div>

@stop
