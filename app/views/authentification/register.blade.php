

@section('content')

<div class="row-fluid">
	<div class="offset1">
		<legend><h1>{{trans('authentification.registration_label')}}</h1></legend>
		{{ Form::open(array('url' => 'api/v1/auth/register', 'method' => 'post')) }}
					{{Form::label('displayname', trans('authentification.displayname_label')) }}
					{{Form::text('displayname', Input::old('displayname')) }}
					{{Form::label('email', 'Email') }}
					{{Form::email('email', Input::old('email')) }}
					{{Form::label('password', trans('authentification.password_label')) }}
					{{Form::password('password') }}
				 	{{Form::label('password_confirmation',trans('authentification.confirm_password_label')) }} 
				 	{{Form::password('password_confirmation') }}
					{{Form::token() }}<br>
					<button class="btn btn-primary" type="submit" name="register" id="register" onclick="$(auth/register).submit()">{{trans('authentification.register_button')}}</button> 
		{{Form::close()}}
	</div>
</div>


@stop
