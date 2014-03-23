<?php

namespace Bugherd\Api;

/**
 * Listing projects, creating, editing
 *
 * @link   https://www.bugherd.com/api_v2#api_proj_list
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class Project extends AbstractApi
{
    
    protected $projects = array();
    protected $actives = array();

    /**
     * List projects
     * @link   https://www.bugherd.com/api_v2#api_proj_list
     *
     * @param  array $params optional parameters to be passed to the api (offset, limit, ...)
     * @return array list of projects found
     */
    public function all()
    {
        $this->projects = $this->retrieveAll('/projects.json');

        return $this->projects;
    }
    
    /**
     * List projects
     * @link   https://www.bugherd.com/api_v2#api_proj_list
     *
     * @param  array $params optional parameters to be passed to the api (offset, limit, ...)
     * @return array list of projects found
     */
    public function allActive()
    {
        $this->actives = $this->retrieveAll('/projects/active.json');

        return $this->actives;
    }

    /**
     * Returns an array of projects with name/id pairs (or id/name if $reserse is false)
     *
     * @param  boolean $forceUpdate to force the update of the projects var
     * @param  boolean $reverse     to return an array indexed by name rather than id
     * @return array   list of projects (id => project name)
     */
    public function listing($forceUpdate = false, $reverse = true)
    {
        if (true === $forceUpdate || empty($this->projects)) {
            $this->all();
        }
        $ret = array();
        foreach ($this->projects['projects'] as $e) {
            $ret[(int) $e['id']] =  $e['name'];
        }

        return $reverse ? array_flip($ret) : $ret;
    }
    
    /**
     * Returns an array of projects with name/id pairs (or id/name if $reserse is false)
     *
     * @param  boolean $forceUpdate to force the update of the projects var
     * @param  boolean $reverse     to return an array indexed by name rather than id
     * @return array   list of projects (id => project name)
     */
    public function listingActive($forceUpdate = false, $reverse = true)
    {
        if (true === $forceUpdate || empty($this->projects)) {
            $this->allActive();
        }
        $ret = array();
        foreach ($this->actives['projects'] as $e) {
            $ret[(int) $e['id']] =  $e['name'];
        }

        return $reverse ? array_flip($ret) : $ret;
    }

    /**
     * Get a project id given its name
     * @param  string $name
     * @return int
     */
    public function getIdByName($name)
    {
        $arr = $this->listing();
        if (!isset($arr[$name])) {
            return false;
        }

        return $arr[(string) $name];
    }

    /**
     * Get information about a project 
     * @link https://www.bugherd.com/api_v2#api_proj_show
     *
     * @param  string $id the project id
     * @return array  information about the project
     */
    public function show($id)
    {
        return $this->get('/projects/'.urlencode($id).'.json');
    }

    /**
     * Create a new project given an array of $params
     * @link https://www.bugherd.com/api_v2#api_proj_create
     *
     * @param  array             $params the new project data
     * @throws \Exception
     * @return \SimpleXMLElement
     */
    public function create(array $params = array())
    {
        $defaults = array(
            'name'      => null,
            'devurl'    => null,
            'is_active' => true,
            'is_public' => false,
        );

        $params = array_filter(
            array_merge($defaults, $params),
            array($this, 'isNotNull')
        );

        if(
            !isset($params['name'])
         || !isset($params['devurl'])
        ) {
            throw new \Exception('Missing mandatory parameters');
        }
        $data = array('project'=>$params);

        return $this->post('/projects.json', $data);
    }

    /**
     * Update project's information
     * @link https://www.bugherd.com/api_v2#api_proj_update
     *
     * @param  string            $id     the project id
     * @param  array             $params
     * @return \SimpleXMLElement
     */
    public function update($id, array $params)
    {
        $data = array('project'=>$params);

        return $this->put('/projects/'.$id.'.json', $data);
    }

    /**
     * Delete a project
     * @link https://www.bugherd.com/api_v2#api_proj_delete
     *
     * @param  int  $id id of the project
     * @return void
     */
    public function remove($id)
    {
        return $this->delete('/projects/'.$id.'.json');
    }
    
    /**
     * Add a member to a project
     * 
     * @link https://www.bugherd.com/api_v2#api_proj_add_member
     * @param int $id
     * @param int $memberId
     * @return boolean
     */
    
    public function addMember($id,$memberId) {
        
        $params = array('user_id'=>$memberId);
         
        return $this->post('/projects/'.$id.'/add_member.json', $params);

        
    }
    
    /**
     * Add a member to a project
     * 
     * @link https://www.bugherd.com/api_v2#api_proj_add_guest
     * @param int $id
     * @param mixed $guest
     * @return boolean
     */
    
    public function addGuest($id,$guest) {
        if(is_string($guest)) {
            $params = array('email'=>$guest);
        } elseif(is_numeric($guest)) {
            $params = array('user_id'=>$guest);
        }else  {
            throw new \Exception('Invalid parameter');
        }
         
        return $this->post('/projects/'.$id.'/add_guest.json', $params);
        
    }
    
    
    
    
    
}
