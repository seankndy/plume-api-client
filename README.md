## Installation

```bash
  composer require seankndy/plume-api-client
```

## Plume Setup
Login to the Plume portal (https://portal.plume.com), navigate to Configuration, then locate API Token Generation on the page.  From there, you can generate a new API app token and acquire the authorization URL and base64 encoded basic auth header.   You will need to decode that Basic authorization string and use that for the appBasicAuth parameter on the `\SeanKndy\PlumeApi\AppConfiguration` object.  This is then passed to the `\SeanKndy\PlumeApi\Client`. 

## Basic Usage
```php
<?php

require('vendor/autoload.php');

$client = new \SeanKndy\PlumeApi\Client(
    new \SeanKndy\PlumeApi\AppConfiguration(
        'partnerIdHere', // provided by Plume
        'authorization_url_here', // provided by Plume
        'user:pass', // base64 decoded Basic auth header value provided to you by Plume
        'https://piranha-gamma.prod.us-west-2.aws.plumenet.io/api/',
        __DIR__.'/../private-storage/.plume-api-token.cache' // where to store temporal access token; WARNING: be sure this is not publicly accessible
    )
);

// make any authenticated request to Plume, returns a \Psr\Http\Message\ResponseInterface
$response = $client->authenticatedRequest(
    'GET', // HTTP method
    '/any/endpoint/here',
    'body', // Can also be an array which will be json-encoded automatically
    [] // HTTP headers
);

// Customers API provides the most commonly needed Customers API functionality so you don't have to build the requests yourself.  They throw \RuntimeExceptions for non-OK HTTP status codes. 
$customersApi = $client->customersApi();
$customer = $customersApi->register(
    'foo@bar.com',
    'Foo Bar',
    '12345'
));
$matchingCustomers = $customersApi->search('email', 'foo@bar.com'); // search by email
$matchingCustomers = $customersApi->search('accountId', '12345'); // search by accountId

// claim or unclaim nodes to a customer location
$node = $customersApi->claimNode('customerIdHere', 'locationIdHere', 'deviceSerialNumberHere');
$customersApi->unclaimNode('customerIdHere', 'locationIdHere', 'nodeId');

// get the nodes for a customer location
$nodes = $customersApi->getNodes('customerIdHere', 'locationIdHere');

// set pppoe wan configuration
$customersApi->updateWanConfiguration('customerIdHere', 'locationIdHere', [
    'pppoe' => [
       'enabled' => true,
       'username' => 'myUsername',
       'password' => 'myPassword'
    ]
]);

// create a wifi network
$network = $customersApi->createWifiNetwork('customerIdHere', 'locationIdHere', 'ssid_here', 'passphrase_here');
```