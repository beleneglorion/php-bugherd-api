<?php

namespace Bugherd\Api;

/**
 * Organization informations
 *
 * @link   @link https://www.bugherd.com/api_v2#api_org_show
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class Organization extends AbstractApi
{
   

    /**
     * Get more detail of your account.
     * 
     * @return array  information about the account
     */
    public function show()
    {
        return $this->get('/organization.json');
    }



}
