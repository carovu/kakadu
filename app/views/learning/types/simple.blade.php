
<div id="simple">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{trans('test.question_label')}}
				<div class="percent pull-right" class="pull-right"></div>
			</h4>
			<p id="questionSimple"></p>
		</div>
		<br>
		<div style="height: 14ex;" id="answerLabel">
			<h4>{{trans('test.answer_label')}}</h4>
			<p id="answerSimple"></p>
		</div>
		<button class="btn btn-primary" id="showAnswer">{{trans('test.show_answer_button')}}</button>
		<div id="correct">
			<button class="btn btn-success" id="yes">
				<i class="icon-ok"></i> {{trans('test.yes')}}
			</button>
			<button class="btn btn-danger" id="no">
				<i class="icon-remove"></i> {{trans('test.no')}}
			</button>
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
