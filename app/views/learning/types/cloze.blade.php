
<div id="cloze">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{trans('test.question_label')}}
				<div class="percent pull-right" class="pull-right"></div>
			</h4>
			<p id="questionCloze"></p>
			<h4>{{trans('test.answer_label')}}</h4>
			<br>
			<h5>{{trans('test.cloze_label')}}</h5>
			<p id="choicesCloze"></p>
			<div>
				<button class="btn-primary" id="checkCloze">{{trans('test.cloze_check_button')}}</button>
				<button class="btn-primary" id="nextQuestion">{{trans('test.next_question_button')}}</button>
			</div>
		</div>
	</div>	
	<br>
	<div id="keyboard control" class="alert alert-block fade in">
		<a class="close" href="#">&times;</a>
		<h5>{{trans('descriptions.keys')}}</h5>
		<p>{{trans('descriptions.key_show')}}</p>
	</div>
		
</div> 