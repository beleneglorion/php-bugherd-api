<?php

namespace Bugherd;

use SimpleXMLElement;

/**
 * Simple PHP Bugherd client
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 * Website: http://github.com/kbsali/php-bugherd-api
 */
class Client
{
    /**
     * @var array
     */
    private static $defaultPorts = array(
        'http'  => 80,
        'https' => 443,
    );

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $apikey;

    /**
     * @var boolean
     */
    private $checkSslCertificate = true;

    /**
     * @var boolean
     */
    private $checkSslHost = 2;


    /**
     * @var array APIs
     */
    private $apis = array();

    /**
     * @var int|null Bugherd response code, null if request is not still completed
     */
    private $responseCode = null;

    /**
     * Error strings if json is invalid
     */
    private static $json_errors = array(
        JSON_ERROR_NONE      => 'No error has occurred',
        JSON_ERROR_DEPTH     => 'The maximum stack depth has been exceeded',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX    => 'Syntax error',
    );

    /**
     * Usage: apikeyOrUsername can be auth key or username.
     * Password needs to be set if username is given.
     *
     * @param string $apikey
     */
    public function __construct($apikey)
    {
        $this->url = 'https://www.bugherd.com';
        $this->apiKey = $apikey;
        $this->port = 443;
    }

    /**
     * @param  string                    $name
     * @return Api\AbstractApi
     * @throws \InvalidArgumentException
     */
    public function api($name)
    {
        $classes = array(
            'organization'  => 'Organization',
            'user'          => 'User',
            'project'       => 'Project',
            'task'          => 'Task',
            'comment'       => 'Comment',
            'webhook'       => 'Webhook'
        );
        if (!isset($classes[$name])) {
            throw new \InvalidArgumentException();
        }
        if (isset($this->apis[$name])) {
            return $this->apis[$name];
        }
        $c = 'Bugherd\Api\\'.$classes[$name];
        $this->apis[$name] = new $c($this);

        return $this->apis[$name];
    }

    /**
     * Returns Url
     * @return string
     */
    public function getUrl()
    {
       return $this->url;
    }
    
     /**
     * Set Url ( only usefull if bugherd change is api url.. )
     * @return string
     */
    public function setUrl($url)
    {
       $this->url = $url;
       
       return $this;
    }

    /**
     * HTTP GETs a json $path and tries to decode it
     * @param  string $path
     * @return array
     */
    public function get($path)
    {
        if (false === $json = $this->runRequest($path, 'GET')) {
            return false;
        }

        return $this->decode($json);
    }

    /**
     * Decodes json response
     * @param  string $json
     * @return array
     */
    public function decode($json)
    {
        $decoded = json_decode($json, true);
        if (null !== $decoded) {
            return $decoded;
        }
        if (JSON_ERROR_NONE === json_last_error()) {
            return $json;
        }

        return self::$json_errors[json_last_error()];
    }

    /**
     * HTTP POSTs $params to $path
     * @param  string $path
     * @param  string $data
     * @return mixed
     */
    public function post($path, $data)
    {
        if (false === $json = $this->runRequest($path, 'POST', $data)) {
            return false;
        }

        return $this->decode($json);

    }

    /**
     * HTTP PUTs $params to $path
     * @param  string $path
     * @param  string $data
     * @return array
     */
    public function put($path, $data)
    {
        if (false === $json = $this->runRequest($path, 'PUT', $data)) {
            return false;
        }

        return $this->decode($json);
    }

    /**
     * HTTP PUTs $params to $path
     * @param  string $path
     * @return array
     */
    public function delete($path)
    {
        if (false === $json = $this->runRequest($path, 'DELETE')) {
            return false;
        }

        return $this->decode($json);
    }

    /**
     * Turns on/off ssl certificate check
     * @param  boolean $check
     * @return Client
     */
    public function setCheckSslCertificate($check = tue)
    {
        $this->checkSslCertificate = $check;

        return $this;
    }

    /**
     * Turns on/off ssl host certificate check
     * @param  boolean $check
     * @return Client
     */
    public function setCheckSslHost($check = true)
    {
        $this->checkSslHost = $check;

        return $this;
    }


    /**
     * Set the port of the connection
     * @param  int    $port
     * @return Client
     */
    public function setPort($port = null)
    {
        if (null !== $port) {
            $this->port = (int) $port;
        }

        return $this;
    }

    /**
     * Returns Bugherd response code
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Returns the port of the current connection,
     * if not set, it will try to guess the port
     * from the given $urlPath
     * @param  string $urlPath the url called
     * @return int    the port number
     */
    public function getPort($urlPath = null)
    {
        if (null === $urlPath) {
            return $this->port;
        }
        if (null !== $this->port) {
            return $this->port;
        }
        $tmp = parse_url($urlPath);

        if (isset($tmp['port'])) {
            $this->setPort($tmp['port']);

            return $this->port;
        }
        $this->setPort(self::$defaultPorts[$tmp['scheme']]);

        return $this->port;
    }

    /**
     * @param  string                        $path
     * @param  string                        $method
     * @param  array                        $data
     * @return false|array|string
     * @throws \Exception                    If anything goes wrong on curl request
     */
    private function runRequest($path, $method = 'GET',array $data =null)
    {
        $this->responseCode = null;
        $this->getPort($this->url.$path);
        $data = json_encode($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey.':x');
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $this->url.'/api_v2'.$path);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER,0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_PORT , $this->port);
        if (80 !== $this->port) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->checkSslCertificate);
            curl_setopt($curl, CURLOPT_SSLVERSION,3);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->checkSslHost);
        }

        $httpHeader = array();
        $httpHeader[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if (isset($data)) {curl_setopt($curl, CURLOPT_POSTFIELDS, $data);}
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (isset($data)) {curl_setopt($curl, CURLOPT_POSTFIELDS, $data);}
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default: // GET
                break;
        }
        $response = curl_exec($curl);
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $e = new \Exception(curl_error($curl), curl_errno($curl));
            curl_close($curl);
            throw $e;
        }
        curl_close($curl);

        if ($response) {
            return $response;
        }

        return true;
    }
}
