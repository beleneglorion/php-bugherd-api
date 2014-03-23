<?php

namespace Bugherd\Api;

/**
 * Listing users (Read Only Api)
 *
 * @link   https://www.bugherd.com/api_v2#api_user_list
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class User extends AbstractApi
{
    protected $users = array();
    protected $members = array();
    protected $guests = array();

    /**
     * List all users
     *
     * @return array list of users found
     */
    public function all()
    {
        $this->users = $this->retrieveAll('/users.json');

        return $this->users;
    }
    
     /**
     * List all guests
     *
     * @return array list of guests found
     */
    public function getGuests()
    {
        $this->guests = $this->retrieveAll('/users/guests.json');

        return $this->guests;
    }

    
    
     /**
     * List all members
     *
     * @return array list of guests found
     */
    public function getMembers()
    {
        $this->members = $this->retrieveAll('/users/members.json');

        return $this->members;
    }




}
