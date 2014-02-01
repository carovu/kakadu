@extends('layouts.master')

{{-- Scripts --}}
@section('scripts')
	
	{{ HTML::script('js/favorites.js')}}
	<script>
	$(document).ready(function() {	
		$("#favorites").addClass('active');
		initialiseFavorites("{{URL::to('/api/v1')}}", "{{trans('favorites.learn')}}");
	});
	</script>
	

@stop



{{--Content--}}
@section('content')

	<div class="row-fluid">
		<div class="offset1">
		    <div class="span5">	
				<h3>{{trans('favorites.courses')}}</h3>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>{{trans('favorites.name')}}</th>
							<th>{{trans('favorites.learn_remove')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($courses as $c)
						<tr id="course{{$c['id']}}">
							<td>{{HTML::linkRoute('course', $c['name'], array($c['id']))}}</td>
							<td>
								<div class="btn-group">
									<button onclick="parent.location='{{URL::route('course/learning', array($c['id']))}}'" class="btn btn-small">{{trans('favorites.learn')}}</button>
									<button class="btn btn-small btn-danger" onclick="removeFavoriteCourse({{$c['id']}})" title="{{trans('favorites.remove')}}"><i class="icon-remove icon-mini"></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="span5">
				<h3>{{trans('favorites.catalogs')}}</h3>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>{{trans('favorites.name')}}</th>
							<th>{{trans('favorites.learn_remove')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($catalogs as $c)
						<tr id="catalog{{$c['id']}}">
							<td>{{HTML::linkRoute('catalog', $c['name'], array($c['id']))}}</td>
							<td>
								<div class="btn-group">
									<button onclick="parent.location='{{URL::route('catalog/learning', array($c['id']))}}'" class="btn btn-small">{{trans('favorites.learn')}}</button>
									<button class="btn btn-small btn-danger" onclick="removeFavoriteCatalog({{$c['id']}})" title="{{trans('favorites.remove')}}"><i class="icon-remove icon-mini"></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>    
    </div>
    <div class="row-fluid">
		<div class="offset1">		
    		<div class="span5">
    			<h3>{{trans('favorites.learn_title')}}</h3>
				{{HTML::linkRoute('favorites/learning', trans('favorites.learn_all'))}}
			</div>
		</div>
	</div>

@stop
