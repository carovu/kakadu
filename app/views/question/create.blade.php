
{{-- Scripts --}}
@section('scripts')
	{{ HTML::script('js/questionType.js')}}
	
	<script>
		$(document).ready(function(){
			initialiseQuestionType("{{$course['id']}}", "{{URL::to('/api/v1')}}", "{{Form::old('type')}}");
		});
	</script>
@stop

@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend><h3>{{trans('question.create')}}</h3></legend>
			<label>{{trans('question.choose_type')}}</label>
			<div class="btn-group">
				<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{trans('question.type')}} <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a onclick="changeType('simple')" style="cursor: pointer;">{{trans('question.simple')}}</a></li>
					<li><a onclick="changeType('multiple')" style="cursor: pointer;">{{trans('question.multiple')}}</a></li>
					<li><a onclick="changeType('cloze')" style="cursor: pointer;">{{trans('question.cloze')}}</a></li>
					<li><a onclick="changeType('match')" style="cursor: pointer;">{{trans('question.match')}}</a></li>
					<li><a onclick="changeType('image')" style="cursor: pointer;">{{trans('question.image')}}</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Simple question -->
<div class="row-fluid" id="simple">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formSimple')) }}
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'simple') }}
		
			<!-- The simple question type -->
			@include('question.types.simple')
	
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!-- Multiple choice question -->
<div class="row-fluid" id="multiple">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formMultiple')) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'multiple') }}
			
			<!-- The multiplechoice question type -->
			@include('question.types.multiple')
					
			<br>
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!-- Cloze question -->
<div class="row-fluid" id="cloze">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formCloze')) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'cloze') }}
			
			<!-- The multiplechoice question type -->
			@include('question.types.cloze')
					
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!-- Match  question -->
<div class="row-fluid" id="match">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formMatch')) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'match') }}
			
			<!-- The multiplechoice question type -->
			@include('question.types.match')
					
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}			
	</div>
</div>
<!-- Image  question -->
<div class="row-fluid" id="image">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formImage')) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'image') }}
			
			<!-- The multiplechoice question type -->
			@include('question.types.image')
					
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}			
	</div>
</div>

@stop