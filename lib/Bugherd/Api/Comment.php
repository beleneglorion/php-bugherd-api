<?php

namespace Bugherd\Api;

/**
 * @link   https://www.bugherd.com/api_v2#api_comment_list
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class Comment extends AbstractApi
{
    protected $comments = array();

    /**
     * List comment of a task 
     * @link https://www.bugherd.com/api_v2#api_comment_list
     *
     * @param  int $project project id 
     * @param  int $taskId project id     
     * @return array      list of comments
     */
    public function all($projectId, $taskId)
    {
        $path = '/projects/'.urlencode($projectId).'/tasks/'.urlencode($taskId).'/comments.json';
        $this->comments = $this->retrieveAll($path);

        return $this->comments;
    }
    
    
     /**
     * Create a new comment on a task
     * @link https://www.bugherd.com/api_v2#api_comment_create
     * @param  string $projectId  the project id
     * @param  string $taskId  the project id
     * @param  array             $params the new issue data
     * @return mixed
     */
    public function create($projectId,$taskId,array $params = array())
    {
        $defaults = array(
            'text'      => null,
            'user_id'     => null,
            'user_email'  => null
        );
        $params = array_filter(array_merge($defaults, $params));
        
        $data = array('comment'=>$params);

        $path = '/projects/'.urlencode($projectId).'/tasks/'.urlencode($taskId).'/comments.json';
        return $this->post($path,$data);
    }

}
