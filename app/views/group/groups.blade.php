@extends('layouts.master')

{{-- Scripts --}}
@section('scripts')

{{ HTML::script('js/sorting_list.js')}}
{{ HTML::script('js/addUser.js')}}

<script>
$(document).ready(function() {	
	$("#groups").addClass('active');
	$(".pagination").addClass('pagination-centered');

	//Links that are needed for Sorting.
	//Function in sorting_list.js
	initialise_links("{{URL::to('groups')}}");

	//Links that are needed for adding-requests.
	//Function in add_user.js
	initialiseAddUser("{{Request::root()}}");
});


function link(id){
	var url = "{{URL::to('group')}}";
	setTimeout(function(){
		window.location=url+"/"+id;
	}, 300);
}
</script>

@stop

{{-- Content --}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<form class="form-inline">
				<ul class="nav nav-pills">
		            <li class="dropdown">
		            	<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#">{{trans('pagination.sort')}}<b class="caret"></b></a>
		                <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
		                	<li><a href="#" onclick="sorting('sort', 'name', '{{URL::route('groups')}}');">Name</a></li>
							<li><a href="#" onclick="sorting('sort', 'id', '{{URL::route('groups')}}');">Id</a></li>
							<li><a href="#" onclick="sorting('sort', 'created_at', '{{URL::route('groups')}}');">{{trans('pagination.create_date')}}</a></li>
		                </ul>
					</li>
					<li class="dropdown">
		            	<a class="dropdown-toggle" id="drop4" role="button" data-toggle="dropdown" href="#">{{trans('pagination.number')}}<b class="caret"></b></a>
		                <ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
							<li><a href="#" onclick="sorting('number', '10', '{{URL::route('groups')}}');">10</a></li>
							<li><a href="#" onclick="sorting('number', '20', '{{URL::route('groups')}}');">20</a></li>
							<li><a href="#" onclick="sorting('number', '30', '{{URL::route('groups')}}');">30</a></li>
							<li><a href="#" onclick="sorting('number', '40', '{{URL::route('groups')}}');">40</a></li>
							<li><a href="#" onclick="sorting('number', '50', '{{URL::route('groups')}}');">50</a></li>
		                </ul>
					</li>
					<li class="dropdown">
		            	<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#">{{trans('pagination.order')}}<b class="caret"></b></a>
		                <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
		                	<li><a href="#" onclick="sorting('order', 'asc', '{{URL::route('groups')}}');">{{trans('pagination.up')}}</a></li>
							<li><a href="#" onclick="sorting('order', 'desc', '{{URL::route('groups')}}');">{{trans('pagination.down')}}</a></li>
		                </ul>
					</li>
				</ul>
			</form>
			<div id="list">
				@include('group.list')
			</div>
		</div>
	</div>
</div>



@stop