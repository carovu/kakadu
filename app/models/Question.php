<?php

class Question extends Eloquent {

    public $timestamps = TRUE;

    public function catalogs() {
        return $this->belongsToMany('Catalog', 'catalog_questions');
    }

    public function flashcards() {
        return $this->hasMany('Flashcard');
    }

}