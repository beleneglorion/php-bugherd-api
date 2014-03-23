<?php

namespace Bugherd\Api;

/**
 * Listing Webhook categories, creating, editing
 *
 * @link   https://www.bugherd.com/api_v2#api_webhook_list
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class Webhook extends AbstractApi
{
    
    const EVENT_TASK_CREATE     = 'task_create';
    const EVENT_TASK_UPDATE     = 'task_update';
    const EVENT_TASK_DESTROY    = 'task_destroy';
    const EVENT_COMMENT         = 'comment';

    /**
     * List WebHook
     * @link https://www.bugherd.com/api_v2#api_task_list
     *
     * @return array list of webhooks found
     */
    public function all()
    {
        return $this->retrieveAll('/webhooks.json');
    }
    
    
    
    /**
     * Create a new webhook given an array of $params
     * The issue is assigned to the authenticated user.
     * @link https://www.bugherd.com/api_v2#api_webhook_create
     * @param  string $projectId  the project id
     * @param  array             $params the new issue data
     * @return mixed
     */
    public function create(array $params = array())
    {
        $defaults = array(
            'target_url'     => null,
            'event'  => null
        );
        $params = array_filter(array_merge($defaults, $params));


        return $this->post('/webhooks.json',$params);
    }
    
     /**
     * Delete a webhook
     * @link https://www.bugherd.com/api_v2#api_webhook_delete
     *
     * @param  int  $id id of the webhook
     * @return void
     */
    public function remove($id)
    {
        return $this->delete('/webhooks/'.  urlencode($id).'.json');
    }
}
