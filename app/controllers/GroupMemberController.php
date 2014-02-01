<?php

class GroupMemberController extends BaseKakaduController {
    
    private $member;

    private $rules = array(
                        'id'            => 'required',
                        'user'          => 'required'
                    );


    /**
     * Add a user to a group
     */
    public function postUserAdd() {
        
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Check if user is allready in the group
        $allocation = $this->group->users()->where('user_id', '=', $this->member->id)->first();

        if($allocation !== null) {
            return $this->getJsonErrorResponse(array(trans('group.user_allready_in_group')));
        }

        //Add user to group
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($this->member, array('role_id' => $role->id));

        return $this->getJsonOkResponse();
    }


    /**
     * Remove a user from a group
     */
    public function postUserRemove() {

        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Check if user is not in the group
        $allocation = $this->group->users()->where('user_id', '=', $this->member->id)->first();

        if($allocation === null) {
            return $this->getJsonInfoResponse(array(trans('group.user_not_in_group')));
        }

        //Check if user is admin of the group
        $role = Role::where('name', 'LIKE', 'admin')->first();

        if($allocation->role_id === $role->id) {
            $admins = $this->group->users()->where('role_id', '=', $role->id)->count();

            if($admins <= 1) {
                return $this->getJsonErrorResponse(array(trans('group.last_admin_of_group')));
            }
        }

        //Delete all favorites
        HelperGroup::deleteFavoritesOfLearngroupMember($this->group, $this->member);

        //Remove user from group
        $this->group->users()->detach($this->member->id);

        return $this->getJsonOkResponse();
    }


    /**
     * Add the user to the admin group
     */
    public function postAdminAdd() {

        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Check if user is allready in the group
        $allocation = $this->group->users()->where('user_id', '=', $this->member->id)->first();

        if($allocation !== null) {
            //Check if user is allready in the admin group
            $role = Role::where('name', 'LIKE', 'admin')->first();

            if($allocation->role_id === $role->id) {
                //User is allready an admin
                return $this->getJsonErrorResponse(array(trans('group.user_allready_an_admin')));
            } else {
                //User is not an admin
                $this->group->users()
                                     ->where('id', '=', $allocation->id)
                                     ->update(array('role_id' => $role->id));
            }

        } else {
            //Add user to group
            $role = Role::where('name', 'LIKE', 'admin')->first();
            $this->group->users()->attach($this->member, array('role_id' => $role->id));
        }

        return $this->getJsonOkResponse();
    }


    /**
     * Remove the user from the admin group
     */
    public function postAdminRemove() {

        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Check if user is not in the group
        $allocation = $this->group->users()->where('user_id', '=', $this->member->id)->first();

        if($allocation === null) {
            return $this->getJsonInfoResponse(array(trans('group.user_not_in_group')));
        }

        //Check if user is admin of the group
        $role = Role::where('name', 'LIKE', 'admin')->first();

        if($allocation->role_id === $role->id) {
            $admins = $this->group->users()->where('role_id', '=', $role->id)->count();

            if($admins <= 1) {
                return $this->getJsonErrorResponse(array(trans('group.last_admin_of_group')));
            }
        }

        //Remove user from group
        $role = Role::where('name', 'LIKE', 'member')->first();
        DB::table('user_learngroups')->where('id', '=', $allocation->id)->update(array('role_id' => $role->id));

        return $this->getJsonOkResponse();
    }


    /**
     * Check the input and the permissions
     * 
     * @return Response|boolean Returns a error response with the given message or true on a valid check
     */
    private function checkInputAndPermissions() {

        //Validate input
        $validation = $this->validateInput($this->rules);

        if($validation !== true) {
            return $validation;
        }

        //Get group
        $id = Input::get('id');
        $this->group = Learngroup::find($id);

        if($this->group === null) {
            return $this->getJsonErrorResponse(array(trans('group.group_not_found')));
        }

        //Get user
        try {
            $user = Sentry::findUserByLogin(Input::get('user'));
            $this->member = User::find($user->getId());
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return $this->getJsonErrorResponse(array($e->getMessage()));
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::EDIT);

        if($permission === ConstPermission::DENIED) {
            return $this->getJsonErrorResponse(array(trans('general.permission_denied')));
        }

        return true;
    }


    /**
     * Validate input with the given rules
     * 
     * @return Response|boolean Returns a error response when there is a validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->messages();
            return $this->getJsonErrorResponse($errors);
        } else {
            return true;
        }
    }

}