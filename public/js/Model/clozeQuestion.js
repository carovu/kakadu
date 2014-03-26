/**
 * Model for a simple question
 */
clozeQuestion = Backbone.Model.extend({
	
	 initialize: function(data) {
		 this.id = data.id;
		 this.type = data.type;
		 this.question = data.question;
		 this.choices = data.choices;
		 this.texts = data.texts;
		 this.rightAnswer = data.answer;
		 //this.printData();
	 },
	 
	 printData: function(){
		 console.log("ID:" + this.id);
		 console.log("Type:" + this.type);
		 console.log("Question:" + this.question);
		 console.log("Texts:" + this.texts);
		 console.log("Choices:" + this.choices);
		 console.log("Right Answer:" + this.rightAnswer);
	 },

	getRightAnswer: function(){
		 return this.rightAnswer;		 
	 }
	 
	
});