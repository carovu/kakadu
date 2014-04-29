
{{-- Scripts --}}
@section('scripts')
	{{ HTML::script('js/questionType.js')}}
	{{HTML::style('css/bootstrap-fileupload.css')}} 
	{{HTML::script('js/bootstrap-fileupload.js')}}
	{{HTML::script('js/jquery.selection.js')}}
	
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
					<li><a onclick="changeType('dragdrop')" style="cursor: pointer;">{{trans('question.dragdrop')}}</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Simple question -->
<div class="row-fluid" id="simple">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formSimple', 'files' => true)) }}
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
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formMultiple', 'files' => true)) }} 
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
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formCloze', 'files' => true)) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'cloze') }}
			
			<!-- The cloze question type -->
			@include('question.types.cloze')
			
			<br>		
			@include('question.catalogs')

			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!-- Drag&Drop question -->
<div class="row-fluid" id="dragdrop">
	<div class="offset1">
		<br>
		{{ Form::open(array('url' => 'question/create', 'method' => 'post', 'id' => 'formDragDrop', 'files' => true)) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'dragdrop') }}
			
			<!-- The cloze question type -->
			@include('question.types.dragdrop')
			
			<br>		
			@include('question.catalogs')

			{{ Form::token() }}
			{{ Form::submit(trans('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!--  Testing for image -->
	<!-- {{ HTML::image('uploads/dog.jpg')}} -->
	
@stop