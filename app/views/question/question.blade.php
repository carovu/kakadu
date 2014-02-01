
{{-- Sidebar --}}
@section('sidebar')

	@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
		<legend><font size="3">{{ trans('question.question')}}</font></legend>

		<li id="editQuestion"><i class='icon-pencil'></i> <a href="#" onclick="edit()">{{trans('question.edit')}}</a></li>
		<li> <i class='icon-trash'></i> <a href='#' onclick=deletequestion()>{{trans('question.delete')}}</a></li>
	@endif
@stop

{{-- Scripts --}}
@section('scripts')

{{ HTML::script('js/questionType.js')}}
<script>

$(document).ready(function(){
	initialiseQuestionType("{{$course['id']}}", "{{URL::to('/api/v1')}}");
});

//function which is called on delete
function deletequestion(){
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
			var urldelete = "{{ URL::route('question/delete', array($question['id'])) }}";
			window.location=urldelete;
		}

		}]);
}
</script>

@stop

@section('content')


<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			@if(!is_null($navCatalog))
				<font size="2">{{ HTML::linkRoute('course', $course['name'], array($course['id'])) }} > {{ HTML::linkRoute('catalog', $navCatalog['name'], array($navCatalog['id'])) }} > {{trans('question.question')}}</font>
			@else
				<font size="2">{{ HTML::linkRoute('course', $course['name'], array($course['id'])) }} > {{trans('question.question')}}</font>
			@endif
			<div id="view">
				<legend>
					{{trans('question.question')}}
					@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
						<a style="cursor: pointer;" onclick=edit(); class="pull-right" title="{{trans('course.edit')}}"><i class="icon-edit"></i></a>
					@endif
				</legend>

				<div class="row-fluid">
					<div class="span12">
							<h5>Info</h5>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<label>{{trans('question.course')}} {{ HTML::linkRoute('course', $course['name'], array($course['id'])) }}</label>
					</div>
					
					<div class="span6">
						<div class="btn-group">
							<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{trans('question.catalogs')}} <span class="caret"></span></button>
							<ul class="dropdown-menu">
								@foreach($catalogs as $catalog)
									<li>{{ HTML::linkRoute('catalog', $catalog['name'], array($catalog['id'])) }}</li>
								@endforeach		
							</ul>
						</div>
					</div>
				</div>
				@if($question['type'] == 'simple' || $question['type'] === 'UndefType')	
					<h5>{{trans('question.question')}}:</h5>	
					<p>{{$question['question']}}</p>
					<h5>{{trans('question.answer')}}:</h5>
					<p>{{ $question['answer'] }}</p>
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
				<div id="edit">		
					{{ Form::open(array('url' => 'api/v1/question/edit', 'method' => 'post', 'id' => 'formMultiple')) }} 							
					<legend>
						{{trans('question.question')}}
						<div class="btn-group pull-right">
							<button class="btn" type="submit" name="change_question" onclick="$(question/edit).submit()">{{trans('course.save')}}</button>
							<button class="btn" onclick="edit();return false;">{{trans('general.abort')}}</button>
						</div>
					</legend>
					<div class="row-fluid">
						<div class="span12">
							<h5>Info</h5>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<label>{{trans('question.course')}} {{ HTML::linkRoute('course', $course['name'], array($course['id'])) }}</label>
						</div>
						<div class="span6">
							<div class="btn-group">
								<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{trans('question.catalogs')}} <span class="caret"></span></button>									<ul class="dropdown-menu">
									@foreach($catalogs as $catalog)
										<li>{{ HTML::linkRoute('catalog', $catalog['name'], array($catalog['id'])) }}</li>
									@endforeach		
								</ul>
							</div>
						</div>
					</div>
					<div id="editQuestion{{$question['id']}}">	
						@if($question['type'] == 'simple' || $question['type'] === 'UndefType')
			
							{{ Form::hidden('course', $course['id']) }}
							{{ Form::hidden('type', 'simple') }}
							{{ Form::hidden('id', $question['id']) }}
											
							<!-- The simple question type -->
							@include('question.types.simple')
							<label>{{trans('catalog.sidebar_title')}}</label>	
							{{ Form::select('catalogs[]', $allCatalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid' , 'id'=>'selectCatalogs')) }}
						@else		
						
							{{ Form::hidden('course', $course['id']) }}
							{{ Form::hidden('type', 'multiple') }}
							{{ Form::hidden('id', $question['id']) }}
												
							<!-- The multiplechoice question type -->
							@include('question.types.multiple')
							<label>{{trans('catalog.sidebar_title')}}</label>						
							{{ Form::select('catalogs[]', $allCatalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid' , 'id'=>'selectCatalogs')) }}
						@endif
					</div>
					{{ Form::token() }}
					{{ Form::close() }}	
				</div>
			@endif			
		</div>
	</div>
</div>

@stop