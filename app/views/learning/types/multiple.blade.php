
<div id="multiple">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{trans('test.question_label')}}
				<div class="percent pull-right"></div>
			</h4>
			<p id="questionMultiple"></p>
		</div>
		<br>
		<h4>{{trans('test.answer_label')}}</h4>
		<div class="row-fluid" id="allAnswers">
			<div class="span5">
				<div id="choicesLeft"></div>
			</div>
			<div class="span5 offset1">
				<div id="choicesRight"></div>
			</div>
		</div>
		<div>
			<br>
			<button class="btn-primary" id="checkAnswer">{{trans('test.check_answer')}}</button>
			<button class="btn-primary" id="nextMultipleQuestion">{{trans('test.next_question_button')}}</button>
		</div>
	</div>
	<br>
	<div id="keyboard control" class="alert alert-block fade in">
		<a class="close" href="#">&times;</a>
		<h5>{{trans('descriptions.keys')}}</h5>
		<p>{{trans('descriptions.keys_multiple')}}</p>
		<p>{{trans('descriptions.keys_multiple2')}}</p>
		<p>{{trans('descriptions.keys_multiple3')}}</p>
	</div>
</div>
