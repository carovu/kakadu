
<!-- View for create and edit a cloze question -->
	<label>{{trans('question.type_cloze')}}</label>
	<label>{{trans('question.questionCloze')}}</label>
	<textarea id="clozequestion" name="clozequestion" class="row-fluid" rows="20" style="resize:none">{{Form::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
	<br>
	<button class="btn-small btn-primary" onclick="addGap();return false;">{{trans('question.clozeGap')}}</button><br>
	<label>{{trans('question.preview')}}</label>
	<div id="preview">{{Form::old('preview')}}</div>

