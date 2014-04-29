
<!-- View for create and edit a Drag & Drop question -->
<div>
	<label>{{trans('question.type_dragdrop')}}</label>
	<br>
	<label>{{trans('question.question')}}</label>
	<textarea name="question" class="row-fluid" rows="4" style="resize:none">{{Input::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
				
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
		<div id="choicesDragDrop">
			@foreach($question['choices'] as $choice)
				<div id=<?php echo $count?>>
					<textarea name="choicesDragDrop[]" class="span8 choicesDragDrop" rows="1" style="resize:none">{{$choice}}</textarea>
					<?php $right = false?>
					@if($choice === $question['answer'])
						<?php $right = true?>
						<input id="check<?php echo $count?>" name="right" class="offset1" checked="checked" type="radio" value="<?php echo $count?>" name="radio">
					@endif									
					@if(!$right)
						<input id="check<?php echo $count?>" name="right" class="offset1" type="radio" value="<?php echo $count?>" name="radio">
					@endif
					@if($count >= 2)
						<button class="btn-danger offset1" onclick="removeDragDropChoice(<?php echo $count?>);return false;"><i class="icon-remove"></i></button>
					@endif
				</div>
				<?php $count++?>
			@endforeach
		</div>
	<!-- For creating a question -->			
	@else
		<div id="choicesDragDrop">
			<textarea name="choicesDragDrop[]" class="span8 choicesDragDrop" rows="1" style="resize:none">{{Form::old('choices.0')}}</textarea> 
			<input id="radio0" name="right" class="offset1" type="radio" value="0" name="radio">
					
			<textarea name="choicesDragDrop[]" class="span8 choicesDragDrop" rows="1" style="resize:none">{{Form::old('choices.1')}}</textarea>
			<input id="radio1" name="right" class="offset1" type="radio" value="1" name="radio">
			
		</div>	
	@endif
	<br>
	<button class="btn-small btn-primary" onclick="addDragDropChoice();return false;">{{trans('question.add_choice')}}</button><br>

</div>