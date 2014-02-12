

{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<legend><h3>{{trans('profile.profile_edit_link')}}</h3></legend>
		<div class="span12">
			<div class="accordion" id="accordion1">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">{{trans('profile.change_info')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseOne" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ Form::open(array('url' => 'profile/edit', 'method' => 'post')) }}
								<fieldset>
									<ol>
										{{ Form::label('displayname', 'Displayname') }}
										@if(Input::old('displayname') != '')
											{{ Form::text('displayname', Input::old('displayname')) }}
										@else
											{{ Form::text('displayname',  DB::table('users_metadata')->where('user_id', Sentry::findUserByLogin($user['email'])->getId())->first()->displayname)}}
										@endif
					
										{{ Form::label('email', 'Email') }}
										@if(Input::old('email') != '')
											{{ Form::email('email', Input::old('email')) }}
										@else
											{{ Form::email('email', $user['email']) }}
										@endif
										{{ Form::label('language', trans('profile.language')) }}

										{{ Form::select('language', $languages, $language) }}
										
										{{ Form::token() }}
										<br>
										<button class="btn btn-small btn-primary" type="submit" name="change_email" id="change_email" onclick="$(profile/edit).submit()">{{trans('profile.change_info')}}</button>
									</ol>
								</fieldset>
							{{ Form::close() }}
						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">{{trans('profile.change_password')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseTwo" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ Form::open(array('url' => 'profile/changepassword', 'method' => 'post')) }}
								<fieldset>
									<ol>
										{{ Form::label('password_old', trans('profile.old_password')) }}
										{{ Form::password('password_old') }}
					
										{{ Form::label('password', trans('profile.new_password')) }}
										{{ Form::password('password') }}
					
										{{ Form::label('password_confirmation', trans('profile.confirm_password')) }}
										{{ Form::password('password_confirmation') }}
					
										{{ Form::token() }}
										<br>
										<button class="btn btn-small btn-primary" type="submit" name="change_pw" id="change_pw" onclick="$(profile/changepassword).submit()">{{trans('profile.change_password')}}</button>
									</ol>
								</fieldset>
							{{ Form::close() }}
						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">{{trans('profile.delete_profile')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseThree" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ HTML::linkRoute('profile/delete', trans('profile.delete_profile_link')) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
