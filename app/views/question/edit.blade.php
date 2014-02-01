
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
		<legend><h3>{{trans('question.edit')}}</h3></legend>
		{{ Form::open(array('url' => 'api/v1/question/edit', 'method' => 'post')) }} 
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('id', $question['id']) }}
			
			@if(Form::old('type') != '')
				{{ Form::hidden('type', Form::old('type'), array('class' => 'row-fluid')) }}
			@else
				{{ Form::hidden('type', $question['type'], array('class' => 'row-fluid')) }}
			@endif
			
			{{ Form::label('question', trans('question.question')) }}
			@if(Form::old('question') != '')
				{{ Form::textarea('question', Form::old('question'), array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@else
				{{ Form::textarea('question', $question['question'], array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@endif
			
			{{ Form::label('answer', trans('question.answer')) }}
			@if(Form::old('answer') != '')
				{{ Form::textarea('answer', Form::old('answer'), array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@else
				{{ Form::textarea('answer', $question['answer'], array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@endif
			
			{{ Form::label('catalogs[]', trans('question.catalogs')) }}
			@if(Form::old('catalogs') != '')
				{{ Form::select('catalogs[]', $catalogs, Form::old('catalogs'), array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
			@else
				{{ Form::select('catalogs[]', $catalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
			@endif
			
			{{ Form::token() }}
			{{ Form::submit(trans('question.save_changes'), array('class' => 'btn btn-primary')) }}
			
		{{ Form::close() }}
		</div>
	</div>
</div>

@stop