<?php 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    public $hidden = array(
                                'password',
                                'ip_address',
                                'status',
                                'permissions',
                                'activated',
                                'activation_code',
                                'activated_at',
                            );

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function metadata() {
        return $this->hasOne('Users_metadata', 'id');
    }

    public function learngroups() {
        return $this->belongsToMany('Learngroup', 'user_learngroups')->withPivot('role_id');
    }

    public function favorites() {
        return $this->belongsToMany('Catalog', 'favorites');
    }

    public function flashcards() {
        return $this->hasMany('Flashcard');
    }
}