<?php

namespace SeanKndy\PlumeApi\Partners;

use SeanKndy\PlumeApi\AbstractBaseClient;
use SeanKndy\PlumeApi\Partners\Api\CustomersApi;

class Client extends AbstractBaseClient
{
    public function customersApi(): CustomersApi
    {
        return new CustomersApi($this);
    }
}