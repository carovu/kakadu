

{{-- Sidebar --}}
@section('sidebar')	
	
	@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
		<legend><font size="3">{{trans('general.catalog')}}</font></legend>
		<li id="editCatalog"><i class="icon-pencil"></i> <a href='#' onclick=edit() >{{trans('catalog.edit')}}</a></li>
		<li id="deleteCatalog"><i class="icon-trash"></i> <a href='#' onclick=deletecatalog() >{{trans('catalog.delete')}}</a></li>
		<li id="createCatalog"><i class="icon-plus"></i> {{HTML::linkRoute('catalog/create', trans('catalog.create'), array($course['id']))}}</li>	
		<li id="createQuestion"><i class="icon-plus"></i> <a href="{{URL::route('question/create', array($course['id']))}}?catalog={{$catalog['id']}}">{{trans('question.create')}}</a></li>		
		<br>
	@endif
@stop

{{-- Scripts --}}
@section('scripts')

	{{ HTML::script('js/favorites.js')}}
	{{ HTML::script('js/questionType.js')}}
	
	<script>

		$(document).ready(function() {	
			initialiseFavorites("{{URL::to('/api/v1')}}", "{{trans('favorites.learn')}}");
			initialiseQuestionType("{{$course['id']}}", "{{URL::to('/api/v1')}}");
			$("#added").hide();
		});
		
		//Counts the Questions
		$count = 0;

		function linkShow(id, catalog, name){
			console.log("Katalog:" + catalog);
			var url = "{{URL::to('question')}}";
			setTimeout(function(){
				window.location=url+"/"+id+"?navcatalog="+catalog;
			}, 300);
		}

		function linkEdit(id){
			var url = "{{URL::to('api/v1/question')}}";
			setTimeout(function(){
				window.location=url+"/"+id+"/edit";
			}, 300);
		}
		
		//function which is called on delete
		function deletecatalog(){
			bootbox.dialog("{{trans('catalog.check')}}", [{
		
				"label" : "{{trans('general.no')}}",
				"class" : "btn-danger",
				"callback": function() {
					console.log("No delete");
				}
		
				}, {
				"label" : "{{trans('general.yes')}}",
				"class" : "btn-success",
				"callback": function() {
					var urldelete = "{{ URL::to('catalog/delete', array($catalog['id'])) }}";
					window.location=urldelete;
				}
		
				}]);
		}
		
		//function which is called on delete
		function deletequestion(id){
			bootbox.dialog("{{trans('question.check')}}", [{
		
				"label" : "{{trans('general.no')}}",
				"class" : "btn-danger",
				"callback": function() {
					console.log("No delete");
				}
		
				}, {
				"label" : "{{trans('general.yes')}}",
				"class" : "btn-success",
				"callback": function() {
					var urldelete = "{{ URL::to('question')}}";
					window.location=urldelete+"/"+id+"/delete";
				}
		
				}]);
		}
		
		//Hides the updated label if the catalog was never updated
		function hide(){
			$(document).ready(function(){
				$('.updated').hide();
			}); 	
		}
		
		//Displays the number of questions of this catalog
		function counts(){
			$(document).ready(function(){
				$('.number').text($count);
			}); 	
		}
		
		//Displays the inline edit fields
		function switchView(id){	
			var show = "#view"+id;
			var edit = "#edit"+id;
			$(show).toggle();
			$(edit).toggle();
			$("#"+id).collapse('show')
		}
	</script>
	
@stop

{{--Content--}}
@section('content')

<!-- Check if the Catalog was ever updated -->
@if($catalog['created_at'] == $catalog['updated_at'])
	<script>hide();</script>
@endif

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<font size="2">{{ HTML::linkRoute('course', $course['name'], array($course['id'])) }} > {{trans('catalog.sidebar_title')}}</font>
			<div id="view">
				<div id="added" class="alert alert-block alert-success fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{trans('catalog.added_to_favorites')}}</h5>
				</div>
				<legend>
					<font style='color: #ADA5B5'>{{trans('general.catalog')}}</font> {{$catalog['name']}}
					@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
						@if($catalog['parent'] != "")
							<a style="cursor: pointer;" onclick=edit(); class="pull-right" title="{{trans('catalog.edit_name')}}"><i class="icon-edit"></i></a>
						@endif
					@endif
					@if($roleSystem !== ConstRole::GUEST)
							@if($catalog['learning'] !== true)
								<button  id="favorite{{$catalog['id']}}" onclick="addFavoriteCatalog({{$catalog['id']}})" class="btn btn-mini" title="{{trans('favorites.favorite')}}"><i class="icon-star"></i></button>
							@else
								<a class="btn btn-mini" href="{{URL::route('catalog/learning', array($catalog['id']))}}">{{trans('favorites.learn')}}</a>
							@endif
					@endif
				</legend>
				<div class="row-fluid">
					<div class="span12">
						<h5>Info</h5>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span6">
						<label>{{trans('catalog.course')}} {{ HTML::linkRoute('course', $course['name'], array($course['id'])) }}</label>
						<label>{{trans('catalog.number')}} <text class="number"></text></label>
						<label>{{trans('catalog.created')}} {{ $catalog['created_at'] }}<label>
						<label class="updated">{{trans('catalog.updated')}} {{ $catalog['updated_at'] }}</label>
					</div>
					<div class="span6">
						@if(count($subcatalogs) > 0)
						<div class="btn-group">
							<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{trans('catalog.subcatalogs')}} <span class="caret"></span></button>
							<ul class="dropdown-menu">
								@foreach($subcatalogs as $subcatalog)
									<li>{{ HTML::linkRoute('catalog', $subcatalog['name'], array($subcatalog['id'])) }}</li>
								@endforeach	
							</ul>
						</div>
						@endif
					</div>
				</div>
			</div>
			@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
				<div id="edit">
					{{ Form::open(array('url' => 'catalog/edit', 'method' => 'post')) }}
					{{ Form::hidden('course', $course['id']) }}
					{{ Form::hidden('id', $catalog['id']) }}
						
					<legend>
						{{ Form::text('name', $catalog['name']) }}
						<div class="btn-group pull-right">
							<button class="btn" type="submit" name="changeCatalog" onclick="$(catalog/edit).submit()">{{trans('course.save')}}</button>
							<button class="btn" onclick="edit();return false;">{{trans('general.abort')}}</button>
						</div>
					</legend>
					
					<div class="row-fluid">
						<div class="span6">
							<h5>Info</h5>
							<label>{{trans('catalog.course')}} {{ HTML::linkRoute('course', $course['name'], array($course['id'])) }}</label>
							<label>{{trans('catalog.number')}} <text class="number"></text></label>
							<label>{{trans('catalog.created')}} {{ $catalog['created_at'] }}<label>
							<label class="updated">{{trans('catalog.updated')}} {{ $catalog['updated_at'] }}</label>
						</div>
						<div class="span6">
							<h5>{{trans('catalog.subcatalogs')}}</h5>
							<ul class="nav nav-pills">
								<li class="dropdown">
							    	<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#">{{trans('catalog.subcatalogs')}}<b class="caret"></b></a>
							        <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
								    	@foreach($subcatalogs as $subcatalog)
											<li>{{ HTML::linkRoute('catalog', $subcatalog['name'], array($subcatalog['id'])) }}</li>
										@endforeach		          
							        </ul>
								</li>
							</ul>
						</div>
					</div>
					
					{{ Form::hidden('number', $catalog['number'])}}
					{{ Form::hidden('parent', $catalog['parent'])}}
						
					{{ Form::token() }}
						
					{{ Form::close() }}
				</div>
			@endif
			<div class="accordion" id="accordion"></div>
			@foreach($questions as $question)
				<script>$count++;</script>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#{{$question['id'] }}">
							<script>document.write(cutString("{{$question['question']}}", 40))</script> <i class="icon-chevron-down"></i>
							<div class="btn-group pull-right">
									<button href="#" title="{{trans('catalog.show')}}" class="btn btn-small" onclick="linkShow({{$question['id']}}, {{$catalog['id']}}, '{{$catalog['name']}}')">{{trans('course.show')}}</button>
								@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
									<button href="#" title="{{trans('question.edit')}}" class="btn btn-small" onclick="switchView({{$question['id']}});"><i class="icon-pencil"></i></button>
									<button href="#" title="{{trans('question.delete')}}" class="btn btn-small" onclick="deletequestion({{$question['id'] }});"><i class="icon-trash"></i></button>
								@endif
							</div>
						</a>
					</div>
					<div id="{{$question['id'] }}" class="accordion-body collapse">
						<div class="accordion-inner">
							@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
								<div ondblclick="editQuestion({{$question['id']}});" id="view{{$question['id']}}" title="{{trans('general.dbclick')}}">
							@else
								<div id="view{{$question['id']}}">
							@endif
								@if($question['type'] == 'simple' || $question['type'] === 'UndefType')	
									<h5>{{trans('question.question')}}:</h5>	
									<p>{{$question['question']}}</p>
									<h5>{{trans('question.answer')}}:</h5>
									<p>{{ $question['answer'] }}</p>
								@elseif($question['type'] == 'cloze')
									<h5>{{trans('question.question')}}:</h5>	
									<p>{{$question['question']}}</p>
									<h5>{{trans('question.choices')}}</h5>	
									@foreach($question['choices'] as $choice)
										<p>{{ $choice}}</p>
									@endforeach
								@else
									<h5>{{trans('question.question')}}:</h5>	
									<p>{{$question['question']}}</p>
									<h5>{{trans('question.correct_answer')}}:</h5>
									@foreach($question['answer'] as $answer)	
										<p>{{$question['choices'][$answer]}}</p>
									@endforeach
									<h5>{{trans('question.choices')}}</h5>	
									@foreach($question['choices'] as $choice)
										<p>{{ $choice}}</p>
									@endforeach
								@endif
							</div>
							@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
								<div id="edit{{$question['id']}}" style="display: none">
									<div id="editQuestion{{$question['id']}}">
										@if($question['type'] === 'simple' || $question['type'] === 'UndefType')
											{{ Form::open(array('url' => 'api/v1/question/edit', 'method' => 'post', 'id' => 'formSimple')) }}
			
												{{ Form::hidden('course', $course['id']) }}
												{{ Form::hidden('type', 'simple') }}
												{{ Form::hidden('id', $question['id']) }}
											
												<!-- The simple question type -->
												@include('question.types.simple')
										
												@include('question.catalogs')
												
												{{ Form::token() }}
												{{ Form::submit(trans('course.save'), array('class' => 'btn')) }}
												<button class="btn" onclick="switchView({{$question['id']}});return false;">{{trans('general.abort')}}</button>
											{{ Form::close() }}	
										@elseif($question['type'] == 'multiple')
											{{ Form::open(array('url' => 'api/v1/question/edit', 'method' => 'post', 'id' => 'formMultiple')) }}
		
												{{ Form::hidden('course', $course['id']) }}
												{{ Form::hidden('type', 'multiple') }}
												{{ Form::hidden('id', $question['id']) }}
												
												<!-- The multiplechoice question type -->
												@include('question.types.multiple')
														
												<br>
												@include('question.catalogs')
												
												{{ Form::token() }}
												{{ Form::submit(trans('course.save'), array('class' => 'btn')) }}
												<button class="btn" onclick="switchView({{$question['id']}});return false;">{{trans('general.abort')}}</button>
											{{ Form::close() }}	
										@else
											{{ Form::open(array('url' => 'api/v1/question/edit', 'method' => 'post', 'id' => 'formCloze')) }}
		
												{{ Form::hidden('course', $course['id']) }}
												{{ Form::hidden('type', 'cloze') }}
												{{ Form::hidden('id', $question['id']) }}
												
												<!-- The multiplechoice question type -->
												@include('question.types.cloze')
														
												<br>
												@include('question.catalogs')
												
												{{ Form::token() }}
												{{ Form::submit(trans('course.save'), array('class' => 'btn')) }}
												<button class="btn" onclick="switchView({{$question['id']}});return false;">{{trans('general.abort')}}</button>
											{{ Form::close() }}	
										@endif
									</div>
								</div>
							@endif						
						</div>
					</div>
				</div>
			@endforeach
			<script>counts();</script>
		</div>
	</div>
</div>

@stop