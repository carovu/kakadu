@extends('layouts.install')

@section('content')
<div class="row-fluid">
	<div class="offset1">
		{{ Form::open(array('url' => 'install', 'method' => 'post')) }} 
		<div class="row-fluid">
			<legend>{{trans('install.installation')}}</legend>
		</div>
		<div class="row-fluid">
			<div class="span6">
				{{Form::label('user_displayname', trans('install.display')) }}
			    {{Form::text('user_displayname', Form::old('user_displayname')) }}
			
			    {{Form::label('user_email', 'Email') }}
			    {{Form::email('user_email', Form::old('user_email')) }}
			
			    {{Form::label('user_password', trans('install.password')) }}
			    {{Form::password('user_password') }}
			
			    {{Form::label('user_password_confirmation', trans('install.password_confirm')) }} 
	    		{{Form::password('user_password_confirmation') }}		
	    		
			</div>	
			<div class="span6">				
				{{Form::label('db_host', 'Host') }}
			    {{Form::text('db_host', Form::old('db_host')) }}
			    	
			    {{Form::label('db_database', trans('install.database')) }}
			    {{Form::text('db_database', Form::old('db_database')) }}
			
			    {{Form::label('db_username', trans('install.username')) }}
			    {{Form::text('db_username', Form::old('db_username')) }}
			
			    {{Form::label('db_password', trans('install.password')) }}
			    {{Form::password('db_password') }}
			
			    {{Form::label('db_password_confirmation', trans('install.password_confirm')) }} 
			    {{Form::password('db_password_confirmation') }}
			</div>
		</div>
		<div class="row-fluid">
			{{ Form::submit(trans('install.install'), array('class' => 'btn btn-primary')) }}
	
			{{ Form::token() }}
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop