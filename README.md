Jsonrpc-client
==============

JSON-Rpc 2.0 protocol implementation

http://www.jsonrpc.org/specification

Installing
==========
Add to yours composer.json require section:

    "moaction/jsonrpc-client": "1.*"

Usage
=====

Basic usage
-----------
```php
$client = new \Moaction\Jsonrpc\Client\ClientBasic('http://mys-server.org/jsonrpc/url');

$request = new \Moaction\Jsonrpc\Common\Request();
$request->setMethod('getUser');
$request->setParams(array('id' => 1));
// You have to set request id whenever you want to recieve response data. See `Notification request`
$request->setId(1);

$response = $client->call($request);
// $response now contains a \Moaction\Jsonrpc\Common\Response object
```

Notification request
--------------------
Notification is a request without id. According to specification server must not reply on a request without id:
> When a rpc call is made, the Server MUST reply with a Response, except for in the case of Notifications.

```php
$request = new \Moaction\Jsonrpc\Common\Request();
$request->setMethod('commentAdded');
$request->setParams(array(
    'userId' => 45,
    'commentId' => 471,
));

$client->call($request);
```


Batch request
-------------
```php
 // first request
 $request1 = new \Moaction\Jsonrpc\Common\Request();
 $request1->setMethod('getUserInfo');
 $request1->setParams(array('id' => 1));
 $request1->setId(3);

 // second request
 $request2 = new \Moaction\Jsonrpc\Common\Request();
 $request2->setMethod('getUserComments');
 $request2->setParams(array('id' => 1));
 $request2->setId(7);

 // notification
 $request3 = new \Moaction\Jsonrpc\Common\Request();
 $request3->setMethod('loginSucceeded');
 $request3->setParams(array('id' => 1));

 $responses = $client->batch(array($request2, $request2, $request2));

 // now $responses contains array of request objects. Array keys are requests' ids.
 // array(
 //     3 => Request object,
 //     7 => Request object,
 // )
```
