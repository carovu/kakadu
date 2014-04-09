
<!-- View for create and edit a cloze question -->
	<label>Highlight text below and click "Add gaps"</label>
	<textarea id="clozequestion" name="clozequestion" class="row-fluid" rows="20" style="resize:none">{{Form::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
	<br>
	<button class="btn-small btn-primary" onclick="addGap();return false;">Add gaps</button><br>

	<label>Your Gaps</label>
	<div id="gaps"></div>	
	<div id="preview"></div>

