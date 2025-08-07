<?php

namespace SeanKndy\PlumeApi\Uprise;

use SeanKndy\PlumeApi\AbstractBaseClient;
use SeanKndy\PlumeApi\Uprise\Api\PropertiesApi;

class Client extends AbstractBaseClient
{
   public function propertiesApi(string $partnerId): PropertiesApi
   {
       return new PropertiesApi($this, $partnerId);
   }
}