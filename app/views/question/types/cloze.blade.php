
<!-- View for create and edit a cloze question -->
	<label>{{trans('question.question')}}</label>
	<textarea id="question" name="question" class="row-fluid" rows="1" style="resize:none">{{Form::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
	<label>{{trans('question.clozecreate')}}</label>
	@if(isset($question))
		@foreach($question['texts'] as $text)
			<div id="texts">
				<textarea name="texts[]" class="span8 texts" rows="2" style="resize:none">{{$text}}</textarea> 
			<div>
		@endforeach
	@else
		<div id="texts">
			<textarea name="texts[]" class="span8 texts" rows="2" style="resize:none">{{Form::old('texts.0')}}</textarea> 

			<textarea name="texts[]" class="span8 texts" rows="2" style="resize:none">{{Form::old('texts.1')}}</textarea>
		<div>		
	@endif

	<div class="row-fluid">
		<div class="span10">
			<label>{{trans('question.answer')}}
				<p class="pull-right">{{trans('question.choose_answer')}}</p>
			</label>
		</div>
	</div>

	<!-- If we edit a question the question field is set -->
	@if(isset($question))
		<?php $count = 0?>
		<div id="choices">
			@foreach($question['choices'] as $choice)
				<div id=<?php echo $count?>>
					<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{$choice}}</textarea>
					<?php $right = false?>				
					@if(!$right)
						<input id="check<?php echo $count?>" name="right" class="offset1" type="checkbox" value="<?php echo $count?>" name="checkbox">
					@endif
					@if($count >= 2)
						<button class="btn-danger offset1" onclick="removeChoice(<?php echo $count?>);return false;"><i class="icon-remove"></i></button>
					@endif
				</div>
				<?php $count++?>
			@endforeach
		</div>
	<!-- For creating a question -->			
	@else
		<div id="choices">
			<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{Form::old('choices.0')}}</textarea> 
			<input id="check0" name="right" class="offset1" type="checkbox" value="0" name="checkbox">

			<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{Form::old('choices.1')}}</textarea> 
			<input id="check0" name="right" class="offset1" type="checkbox" value="0" name="checkbox">
		</div>	
	@endif
	<br>
	<button class="btn-small btn-primary" onclick="addChoice();return false;">{{trans('question.add_choice')}}</button><br>
