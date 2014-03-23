<?php

namespace Bugherd\Api;

use Bugherd\Client;

/**
 * Abstract class for Api classes
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
abstract class AbstractApi
{
    /**
     * The client
     *
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    protected function get($path)
    {
        return $this->client->get($path);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $data)
    {
        return $this->client->post($path, $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function put($path, $data)
    {
        return $this->client->put($path, $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function delete($path)
    {
        return $this->client->delete($path);
    }

    /**
     * Checks if the variable passed is not null
     *
     * @param mixed $var Variable to be checked
     *
     * @return bool
     */
    protected function isNotNull($var)
    {
        return !is_null($var);
    }

    /**
     * Retrieves all the elements of a given endpoint (even if the
     * total number of elements is greater than 100)
     *
     * @param  string $endpoint API end point
     * @param  array  $params   optional parameters to be passed to the api (page , ...)
     * @return array  elements found
     */
    protected function retrieveAll($endpoint, array $params = array())
    {
        if (empty($params)) {
            return $this->get($endpoint);
        }


        unset($params['page']);
        $ret= $this->get($endpoint . '?' . http_build_query($params));
        $nbResult = $ret['meta']['count'];
        if( $nbResult > 100) {
            $nbPage = ceil($nbResult/100);
            for($p = 2;$p <= $nbPage; $p++) {
                 $params['page'] = $p;
                 sleep(3); //  this is evil but bugherd enforce this...
                 $ret = array_merge_recursive($ret, $this->get($endpoint . '?' . http_build_query($params))); 
            }
        }

        return $ret;
    }

   
}
