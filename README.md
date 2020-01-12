ZOHO Inventory API SDK
==================
Unofficial ZOHO Inventory API SDK for PHP

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist shqear/zoho-inventory-api "*"
```

or add

```
"shqear/zoho-inventory-api": "*"
```

to the require section of your `composer.json` file.


Usage
-----

```
require_once 'vendor/autoload.php';
use shqear\lib\ZohoClient;

$inventory = new ZohoInventory(array('accessToken' => 'your auth token'), 'organizationId' => 'your org id'));

$inventory->listContacts(); // Get all contacts
```