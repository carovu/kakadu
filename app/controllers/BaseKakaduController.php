<?php

class BaseKakaduController extends BaseController {

    protected $group;
    protected $course;


    /**
     * Check if the user has the permission to perform this action
     * 
     * @return [boolean] True if user has the permission
     */
    protected function checkPermissions($action) {
        //Check system admin permissions
        if($this->role === ConstRole::ADMIN) {
            return ConstPermission::ALLOWED;
        }


        //Check user permissions
        else if($this->role === ConstRole::USER) {
            switch($action) {
                case ConstAction::ALL:
                case ConstAction::SEARCH:
                    return ConstPermission::ALLOWED;

                case ConstAction::SHOW:
                    return $this->checkDisplayPermissions();

                case ConstAction::FAVORITE:
                case ConstAction::LEARN:
                    return $this->checkLearnPermissions();

                case ConstAction::CREATE:
                case ConstAction::EDIT:
                case ConstAction::DELETE:
                    return $this->checkManagePermissions($action); 

                default:
                    return ConstPermission::DENIED;
            }
        }


        //Default actions
        switch($action) {
            case ConstAction::SHOW:
            case ConstAction::ALL:
            case ConstAction::SEARCH:
                return ConstPermission::LIMITED;

            case ConstAction::FAVORITE:
            case ConstAction::LEARN:
            case ConstAction::CREATE:
            case ConstAction::EDIT:
            case ConstAction::DELETE:
                return ConstPermission::DENIED;

            default:
                return ConstPermission::DENIED;
        }

    }


    /**
     * Checks the permissions to display elements
     * 
     * @return Permission
     */
    private function checkDisplayPermissions() {

        if($this->getRole() === false) {
            return ConstPermission::DENIED;
        }

        //Return right permission
        switch($this->role) {
            case ConstRole::GROUPADMIN:
            case ConstRole::GROUPMEMBER:
                return ConstPermission::ALLOWED;
            
            case ConstRole::USER:
                return ConstPermission::LIMITED;

            default:
                return ConstPermission::DENIED;
        }
    }


    /**
     * Checks the permissions to learn elements
     * 
     * @return Permission
     */
    private function checkLearnPermissions() {

        //Learn the favorites
        if(is_null($this->course)) {
            return ConstPermission::ALLOWED;
        }

        //Learn a course or a catalog
        if($this->getRole() === false) {
            return ConstPermission::DENIED;
        }
        
        //Return right permission
        switch($this->role) {
            case ConstRole::GROUPADMIN:
            case ConstRole::GROUPMEMBER:
                return ConstPermission::ALLOWED;
            
            case ConstRole::USER:
                return ConstPermission::DENIED;

            default:
                return ConstPermission::DENIED;
        }
    }


    /**
     * Checks the permissions to manage elements
     * 
     * @return Permission 
     */
    private function checkManagePermissions($action) {
        //Filter special case - create group or course
        if($action === ConstAction::CREATE && is_null($this->group) && is_null($this->course)) {
        //if($action === ConstAction::CREATE && is_null($this->group)) {
            return ConstPermission::ALLOWED;
        }

        //Normal cases
        if($this->getRole() === false) {
            return ConstPermission::DENIED;
        }

        //Return right permission
        switch($this->role) {
            case ConstRole::GROUPADMIN:
                return ConstPermission::ALLOWED;

            case ConstRole::GROUPMEMBER:
            case ConstRole::USER:
                return ConstPermission::DENIED;

            default:
                return ConstPermission::DENIED;
        }
    }


    /**
     * Get the role of a group or a course element
     */
    private function getRole() {
        
        if(!is_null($this->course)) {
            $this->getCourseRole();
        } else if(!is_null($this->group)) {
            $this->getGroupRole();
        } else {
            return false;
        }

        return true;
    }


    /**
     * Get the role of the user in the group
     */
    private function getGroupRole() {

        $userSentry = Sentry::getUser();
        $userID = $userSentry->getId();

        $pivot = $this->group->users();
        $allocation = $pivot->where('user_id', '=', $userID)
                            ->first();


        //User is not a memeber of the group
        if($allocation === null) {
            return;
        }


        //Get the role
        $role = Role::find($allocation->role_id);

        if($role === null) {
            return;
        }

        if($role->name === 'admin') {
            $this->role = ConstRole::GROUPADMIN;
        } else if($role->name === 'member') {
            $this->role = ConstRole::GROUPMEMBER;
        }

        View::share('roleLearngroup', $this->role);

    }


    /**
     * Get the role of the user in the course
     */
    private function getCourseRole() {

        $userSentry = Sentry::getUser();
        $userID = $userSentry->getId();

        //Get learngroups and the highest role
        $learngroups = $this->course->learngroups()->get();

        if(count($learngroups) === 0) {
            $this->role = ConstRole::GROUPADMIN;
            View::share('roleLearngroup', $this->role);
        }

        foreach($learngroups as $group) {
            $pivot = $group->users();
            $allocation = $pivot->where('user_id', '=', $userID)
                                ->first();

            //User is a memeber of the group
            if($allocation !== null) {
                $role = Role::find($allocation->role_id);

                if($role === null) {
                    continue;
                }

                if($role->name === 'admin') {
                    $this->role = ConstRole::GROUPADMIN;
                    break;
                } else if($role->name === 'member') {
                    $this->role = ConstRole::GROUPMEMBER;
                }
            }
        }

        View::share('roleLearngroup', $this->role);
    }


    /**
     * Get the group informations in an array
     * 
     * @param  Group  $group  The group
     * @return array          An array with all informations of the group
     */
    protected function getGroupArray($group) {
        return array(
            'id'            => $group->id,
            'name'          => $group->name,
            'description'   => $group->description,
            'created_at'    => $group->created_at,
            'updated_at'    => $group->updated_at
        );
    }

    /**
     * Get the course informations in an array
     * 
     * @param  Course  $course   The course
     * @param  boolean $favorite Course is a favorite course of the user
     * @return array             An array with all informations of the course
     */
    protected function getCourseArray($course, $favorite = false) {
        return array(
            'id'            => $course->id,
            'name'          => $course->name,
            'description'   => $course->description,
            'created_at'    => $course->created_at,
            'updated_at'    => $course->updated_at,
            'catalog'       => $course->catalog,
            'favorite'      => $favorite
        );
    }

    /**
     * Get the catalog informations in an array
     * 
     * @param  Catalog $catalog  The catalog
     * @param  boolean $favorite Catalog is a favorite catalog of the user
     * @param  boolean $learning The user is allowed to learn this catalog
     * @return array             An array with all informations of the catalog
     */
    protected function getCatalogArray($catalog, $favorite = false, $learning = false) {
        return array(
            'id'            => $catalog->id,
            'name'          => $catalog->name,
            'number'        => $catalog->number,
            'created_at'    => $catalog->created_at,
            'updated_at'    => $catalog->updated_at,
            'parent'        => $catalog->parent,
            'favorite'      => $favorite,
            'learning'      => $learning
        );
    }
}