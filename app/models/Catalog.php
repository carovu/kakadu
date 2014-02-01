<?php

class Catalog extends Eloquent {

    public $timestamps = TRUE;


    public function parent() {
        return $this->belongsTo('Catalog', 'parent');
    }

    public function children() {
        return $this->hasMany('Catalog', 'parent');
    }

    public function course() {
        return $this->hasOne('Course', 'catalog');
    }

    public function questions() {
        return $this->belongsToMany('Question', 'catalog_questions');
    }

    public function favorite() {
        return $this->belongsToMany('User', 'favorites');
    }

}