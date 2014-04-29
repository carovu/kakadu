
<!-- View for create and edit a cloze question -->
	<label>{{trans('question.type_cloze')}}</label>
	<label>{{trans('question.questionCloze')}}</label>
	<textarea id="clozequestion" name="clozequestion" class="row-fluid" rows="20" style="resize:none">{{Form::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
	<br>
	<button class="btn-small btn-primary" onclick="addGap();return false;">{{trans('question.clozeGap')}}</button><br>
	<label>{{trans('question.preview')}}</label>
	<!-- If we edit a question the question field is set -->
	@if(isset($question))
		<div id="preview"></div>
	<!-- For creating a question -->			
	@else
		<div id="preview"></div>
	@endif
	

