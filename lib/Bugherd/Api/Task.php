<?php

namespace Bugherd\Api;

/**
 * Listing Task, creating, editing
 *
 * @link   https://www.bugherd.com/api_v2#api_task_list
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class Task extends AbstractApi
{
    const PRIORITY_NOT_SET     = 'not set';
    const PRIORITY_CRITICAL    = 'critical';
    const PRIORITY_IMPORTANT   = 'important';
    const PRIORITY_NORMAL      = 'normal';
    const PRIORITY_MINOR       = 'minor';

    const PRIORITY_NOT_SET_ID     = 0;
    const PRIORITY_CRITICAL_ID    = 1;
    const PRIORITY_IMPORTANT_ID   = 2;
    const PRIORITY_NORMAL_ID      = 3;
    const PRIORITY_MINOR_ID       = 4;

    
    const STATUS_BACKLOG_ID = 0;
    const STATUS_TODO_ID    = 1;
    const STATUS_DOING_ID   = 2;
    const STATUS_DONE_ID    = 4;
    const STATUS_CLOSED_ID  = 5;
    
    const STATUS_BACKLOG = 'backlog';
    const STATUS_TODO    = 'todo';
    const STATUS_DOING   = 'doing';
    const STATUS_DONE    = 'done';
    const STATUS_CLOSED  = 'closed';

    /**
     * List Task
     * @link https://www.bugherd.com/api_v2#api_task_list
     * @param  int $projectId the id of the project
     * @param  array $params the additional parameters (cf avaiable $params above)
     *  updated_since, created_since, status, priority, tag and external_id.
     * @return array list of issues found
     */
    public function all($projectId,array $params = array())
    {
        return $this->retrieveAll('/projects/'.urlencode($projectId).'/tasks.json', $params);
    }

    /**
     * Get information about a task given its id
     * @link https://www.bugherd.com/api_v2#api_task_show
     *
     * @param  string $projectId  the project id
     * @param  string $id     the task id
     * @return array  information about the task
     */
    public function show($projectId,$id)
    {
        return $this->get('/projects/'.urlencode($projectId).'/tasks/'.urlencode($id).'.json');
    }

    

    /**
     * Create a new task given an array of $params
     * The issue is assigned to the authenticated user.
     * @link https://www.bugherd.com/api_v2#api_task_create
     * @param  string $projectId  the project id
     * @param  array             $params the new issue data
     * @return mixed
     */
    public function create($projectId,array $params = array())
    {
        $defaults = array(
            'description'      => null,
            'requester_id'     => null,
            'requester_email'  => null
        );
        $params = array_filter(array_merge($defaults, $params));
        
        $data = array('task'=>$params);

        return $this->post('/projects/'.urlencode($projectId).'/tasks.json',$data);
    }

    /**
     * Update task information's
     * @link https://www.bugherd.com/api_v2#api_task_update
     *
     * @param  int $projectId  the project id
     * @param  int            $id     the issue number
     * @param  array         $params
     * @return boolean
     */
    public function update($projectId,$id, array $params)
    {
         $data = array('task'=>$params);
       
        return $this->put('/projects/'.urlencode($projectId).'/tasks/'.urlencode($id).'.json', $data);
    }

   
}
