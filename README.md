ZOHO Inventory API SDK
==================
Unofficial ZOHO Inventory API SDK for PHP

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require shohag/zoho-inventory-sdk
```

or add

```
"shohag/zoho-inventory-sdk": "*"
```

to the require section of your `composer.json` file.


Usage
-----

```
require_once 'vendor/autoload.php';
use shohag\ZohoInventorySDK\ZohoClient;

$inventory = new ZohoInventory(array('accessToken' => 'your access token'), 'organizationId' => 'your org id'));

$inventory->listContacts(); //get all contacts

die(var_dump($inventory));
```