<div id="cloze">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{trans('test.cloze_label')}}
				<div class="percent pull-right" class="pull-right"></div>
			</h4>
			<div>
				<p id="questionCloze"></p>
			</div>
			<div>	
				<h4>{{trans('test.answer_label')}}</h4>
				<p id="answerCloze"></p>
			</div>
			<div>
				<button class="btn-primary" id="checkCloze">{{trans('test.cloze_check_button')}}</button>
				<button class="btn-primary" id="nextClozeQuestion">{{trans('test.next_question_button')}}</button>
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