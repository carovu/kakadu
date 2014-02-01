<?php

class UserMetadata extends Eloquent {

    public $table = 'users_metadata';


    public function user() {
        return $this->belongsTo('User', 'user_id');
    }

}