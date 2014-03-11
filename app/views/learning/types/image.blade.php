
<div id="imagequestion">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{trans('test.question_label')}}
				<div class="percent pull-right" class="pull-right"></div>
			</h4>
			<p id="questionImage"></p>
		</div>
		<br>
		<h4>{{trans('test.answer_label')}}</h4>

		<div>
			<br>
			<button class="btn-primary" id="checkAnswer">{{trans('test.check_answer')}}</button>
			<button class="btn-primary" id="nextQuestion">{{trans('test.next_question_button')}}</button>
		</div>
	</div>
	<br>
	<div id="keyboard control" class="alert alert-block fade in">
		<a class="close" href="#">&times;</a>
		<h5>{{trans('descriptions.keys')}}</h5>
		<p>{{trans('descriptions.key_show')}}</p>
		<p>{{trans('descriptions.use')}} 1 {{trans('descriptions.key_correct')}}</p>
		<p>{{trans('descriptions.use')}} 2 {{trans('descriptions.key_incorrect')}}</p>
	</div>
		
</div>