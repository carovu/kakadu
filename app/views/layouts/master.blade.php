<!DOCTYPE html>
<html lang='de'>
<head>

	<meta charset="UTF-8" />        
	<title>Kakadu</title>
	{{Asset::scripts()}}
	{{HTML::style('css/bootstrap.css')}}
	{{HTML::style('css/font-awesome.css')}} 
	{{HTML::style('css/footer.css')}} 
	<style>

    	/* A fixed navbar needs a padding-top of at least 40px (see: http://twitter.github.io/bootstrap/components.html#navbar)
    	*  The padding is added to the div where the content section is printed
    	*/
    	#content{
    		padding-top: 60px;
    	}

    	/* This css-code is needed for the fixed right sidebar	*/
    	.sidebar-nav-fixed {
    		position:fixed;
    		top:60px;
    		width:21.97%;
    	}

    	</style>

    	<script>

	   //Shows the error messages
	   function error(message){                
	   	bootbox.alert(message);
	   }

	    //Hides/show the inline edit fields
	    function edit(){
	    	$('#view').toggle();
	    	$('#edit').toggle();
	    }

	    $(document).ready(function() {
	    	$('.dropdown-toggle').dropdown();

	        //Hides all inline edit-fields
	        $('#edit').hide();

	        //Additional Label for IE -> cause placeholders are not working in IE
	        if (navigator.appName == "Netscape"){
	        	$("#emailLabel").hide();
	        	$("#passwordLabel").hide();
	        }
	    });
	    </script>
	    @yield('scripts')


	</head>

	<body>
		<div id="wrap">
			<div class="row-fluid">
				<div class="navbar navbar-fixed-top">
					<div class="navbar-inner">
						<a href="{{ URL::route('home')}}" class="brand">Kakadu</a>
						<ul class="nav">            
							<li id="courses">{{ HTML::linkRoute('courses', trans('home.courses_link'))}}</li>
							<li id="groups">{{ HTML::linkRoute('groups', trans('home.groups_link'))}}</li>
							@if($roleSystem != ConstRole::GUEST)
							<li id="favorites">{{ HTML::linkRoute('favorites', trans('home.favorites'))}}</li>
							@endif
							<li class="divider-vertical"></li>              
						</ul>

						{{ Form::open(array('url' => 'courses/search', 'method' => 'get', 'class' => 'navbar-form pull-left')) }}
						<input type="text" name="search" value="{{trans('home.search_defaultvalue')}}" onfocus="if (this.value=='{{trans('home.search_defaultvalue')}}') this.value='';"/>
						{{Form::token()}}
						<button class="btn" type="submit">{{trans('home.search_placeholder')}}</button>
					</form>
					{{ Form::close() }}                 

					<ul class="nav pull-right" id="dropdown">
						<li id="help">{{ HTML::linkRoute('feature', trans('home.feature_link'), array('class'=>'pull-right'))}}</li>
						@if($roleSystem != ConstRole::GUEST)
						<li class="dropdown" id="accountmenu">  
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" ><u id="userinfo">{{  DB::table('users_metadata')->where('user_id', Sentry::findUserByLogin($user['email'])->getId())->first()->displayname}}</u> <i class="icon-cog"></i><b class="caret"></b></a>
							<ul class="dropdown-menu">  
								<li>{{ HTML::linkRoute('profile/edit', trans('profile.profile_edit_link')) }}</li>  
								<li class="divider"></li>  
								<li>{{ HTML::linkRoute('auth/logout', trans('profile.logout_link')) }}</li>  
							</ul>  
						</li> 
						@endif
					</ul> 
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div id="content" class="span9">@yield('content')</div>
			<div class="span3">
				<div id="sidebar" class="well sidebar-nav-fixed">
					<ul class="nav nav-list">
						<div id= "hide-sidebar" class="span1" style="position: relative;">
							<a style="cursor: pointer;"><i cursor: pointer style="position: absolute; bottom: 2em; right: 2em;" class="icon-chevron-right"></i>
								<i style="position: absolute; bottom: 2em; right: 3em;" class="icon-chevron-right"></i>
								<i style="position: absolute; bottom: 2em; right: 1em;" class="icon-chevron-right"></i></a>
							</div>

							@if($roleSystem == ConstRole::GUEST)
							<div id="guest">
								<div class="span11">                    
									{{ Form::open(array('url' => 'auth/login', 'method' => 'post')) }}
									<h3 class="form-signin-heading">{{trans('authentification.sign_in_label')}}</h3>                                   
									{{ Form::label('email', 'Email', array('id'=>'emailLabel')) }}
									{{ Form::email('email', Form::old('email'), array('placeholder'=>'Email')) }}
									{{ Form::label('password', trans('authentification.password_label'), array('id'=>'passwordLabel')) }}
									{{ Form::password('password', array('placeholder'=>trans('authentification.password_label'))) }}                                    
									<label class="checkbox">
										<input id="remember" checked type="checkbox">{{trans('authentification.remember_label')}}
									</label>                           
									{{ Form::token() }}
									<button class="btn btn-primary" type="submit" name="login" id="login" onclick="$('auth/login').submit()">Login</button>
									{{Form::close()}}
									<li>{{ HTML::linkRoute('auth/register', trans('authentification.register_link')) }}<li>
										<li>{{ HTML::linkRoute('auth/forgotpassword', trans('authentification.forgot_password_link')) }}</li>
									</div>      
								</div>
								@else
								<div id="user">
									<div class="span11">
										@yield('sidebar')

										<legend><font size="3">{{trans('general.main_menu')}}</font></legend>
										<li class="nav-header">{{trans('group.sidebar_title')}}</li>
										<li id="show"><i class="icon-info-sign"></i> {{HTML::linkRoute('groups', trans('general.show_groups'))}}</li>
										<li id="create"><i class="icon-plus"></i> {{HTML::linkRoute('group/create', trans('profile.create_group'))}}</li>

										<li class="nav-header">{{trans('course.sidebar_title')}}</li>
										<li id="show"><i class="icon-info-sign"></i> {{HTML::linkRoute('courses', trans('general.show_courses'))}}</li>
										<li id="create"><i class="icon-plus"></i> {{HTML::linkRoute('course/create', trans('profile.create_course'))}}</li>

										<li class="nav-header">{{trans('profile.profile_header')}}</li>
										<li><i class="icon-user"></i>{{ HTML::linkRoute('profile/edit', trans('profile.profile_edit_link')) }}</li>
										<li><i class="icon-signout"></i>{{ HTML::linkRoute('auth/logout', trans('profile.logout_link')) }}</li>
									</div>      
								</div> 

								@endif                     
							</ul>
						</div>
					</div>
					<div id="show-sidebar" class="span1">
						<a style="cursor: pointer;"><i style="position: absolute; bottom: 58%; right: 1%;" class="icon-chevron-left"></i>
							<i style="position: absolute; bottom: 56%; right: 1%;" class="icon-chevron-left"></i>
							<i style="position: absolute; bottom: 54%; right: 1%;" class="icon-chevron-left"></i>
							<i style="position: absolute; bottom: 52%; right: 1%;" class="icon-chevron-left"></i></a>
						</div>
					</div>
					<div id="push"></div>
				</div>
			</div>
			<div id="footer">
				<div class="container">
					@if($roleSystem == ConstRole::GUEST)
					<div class="container">
						<div class="row">
							<div class="span5 offset5">
								{{ Form::open(array('url' => 'language/edit', 'method' => 'post', 'class' => 'navbar-search')) }}
								{{ Form::hidden('language', 'en') }}
								{{ Form::token() }}
								{{ Form::submit(trans('home.english'), array('id' => 'language', 'class' => 'btn btn-link')) }}
								{{ Form::close() }}
								{{ Form::open(array('url' => 'language/edit', 'method' => 'post', 'class' => 'navbar-search')) }}
								{{ Form::hidden('language', 'de') }}
								{{ Form::token() }}
								{{ Form::submit(trans('home.german'), array('id' => 'language', 'class' => 'btn btn-link')) }}
								{{ Form::close() }}
							</div>
						</div>
					</div>
					@endif
					<div class="row-fluid span12">
						<p class="text-center">
							@if($roleSystem !== ConstRole::GUEST)
							<br>
							@endif
							Version 1.0 of Kakadu 
							<a href="http://uibk.ac.at/">Universit&auml;t Innsbruck</a> - <a href="http://informatik.uibk.ac.at/">Institut f&uuml;r Informatik</a> - <a href="http://dbis-informatik.uibk.ac.at/">Databases and Information Systems</a>       		
						</p>
					</div>
				</div>
			</div>


			<!-- Error handling fo all forms -->
			@if(isset($errors))
			<?php
			$message = "";
			foreach ($errors as $error){
				if(!is_array($error) && ($error != ":message")){
					$message .= $error;
				}
			}
			if($message !== ""){
				$test = "error('".$message."')";
				echo "<script>".$test."</script>";
			}
			?>

			@endif
		</body>
		</html>
