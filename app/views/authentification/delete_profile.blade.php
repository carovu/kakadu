

{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		    <div class="alert alert-block alert-error fade in">
    			<strong>{{trans('profile.warning')}}</strong>
    			<p>{{trans('profile.delete_warning')}}<p>
    			{{ Form::open(array('url' => 'profile/delete', 'method' => 'delete')) }}
				<br><button class="btn btn-success" onclick="$('profile/delete').submit()">{{trans('general.yes')}}</button>
				<button class="btn btn-danger" onclick="parent.location='{{URL::route('profile/edit')}}';return false;">{{trans('general.no')}}</button>
				{{ Form::token() }}
				{{ Form::close() }}
				
    		</div>
	</div>
</div>

@stop
          