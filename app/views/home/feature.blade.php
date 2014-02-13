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
<div class="row">
	<div class="offset1">
		<div class="span4">
			<i class="offset2 icon-question-sign icon-4x"></i>
			<h3>{{trans('features.questiontypes')}}</h3>
			<p>{{trans('features.questiontypes_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset2 icon-star icon-4x"></i>
			<h3>{{trans('features.favorites')}}</h3>
			<p>{{trans('features.favorites_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset3 icon-user icon-4x"></i>
			<h3>{{trans('features.list')}}</h3>
			<p>{{trans('features.list_description')}}</p>
		</div>
	</div>
</div>
<div class="row">
	<div class="offset1">
		<div class="span4">
			<i class="offset2 icon-th-list icon-4x"></i>
			<h3>{{trans('features.sidebar')}}</h3>
			<p>{{trans('features.sidebar_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset2 icon-file icon-4x"></i>
			<h3>{{trans('features.import')}}</h3>
			<p>{{trans('features.import_description')}}</p>
		</div>
		<div class="span4">
			<i class="offset3 icon-fullscreen icon-4x"></i>
			<h3>{{trans('features.expandable')}}</h3>
			<p>{{trans('features.expandable_description')}}</p>
		</div>
	</div>
</div>
@stop