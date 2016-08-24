<?php
namespace shqear\lib;

class ZohoClient
{
    public $organizationId;
    public $accessToken;

    const ERROR_TYPE_CURL = 'cUrl';
    const ERROR_TYPE_ZOHO = 'zoho';

    const SCOPE_PURCHASEORDERS = 'purchaseorders';
    const SCOPE_SALESORDERS = 'salesorders';
    const SCOPE_ITEMGROUP = 'itemgroup';
    const SCOPE_INVOICES = 'invoices';
    const SCOPE_CONTACTS = 'contacts';
    const SCOPE_ITEMS = 'items';
    const SCOPE_BILLS = 'bills';

    private $_baseUrl = 'inventory.zoho.com/api/v1';
    private $_curlObject;

    public function __construct(array $config = [])
    {
        foreach ($config as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function setSettings($scope, $params, $returnJson = false)
    {
        $return = $this->curlRequest("/settings/$scope/", 'PUT', ['JSONString' => json_encode($params)]);
        if ($returnJson) {
            return $return;
        } else {
            return $return->code == 0;
        }
    }

    /**
     * @param integer $item_id leave empty to list all items
     * @return mixed
     */
    public function getItem($item_id = null)
    {
        return $this->curlRequest("/items/{$item_id}");
    }

    /**
     * @param $search_text
     * @return mixed
     */
    public function searchItem($search_text)
    {
        return $this->getItems(['search_text' => $search_text]);
    }

    /**
     * List all items
     * @param null $filter
     * @return mixed
     */
    public function getItems($filter = null)
    {
        return $this->curlRequest("/items/", 'GET', $filter);
    }

    public function getOrganizationsInfo()
    {
        return $this->curlRequest('/organizations');
    }

    public function getItemGroup($group_id)
    {
        return $this->curlRequest("/itemgroups/{$group_id}");
    }

    public function createItem($params)
    {
        return $this->curlRequest('/items', 'POST', ['JSONString' => json_encode($params)]);
    }

    public function updateItem($item_id, $updates)
    {
        return $this->curlRequest("/items/{$item_id}", 'PUT', ['JSONString' => json_encode($updates)]);
    }

    public function deletePurchaseOrder($purchaseorder_id)
    {
        return $this->curlRequest("/purchaseorders/{$purchaseorder_id}", 'DELETE');
    }

    public function createPurchaseOrder($params, $ignore = false)
    {
        return $this->curlRequest(
            '/purchaseorders', 'POST',
            ['JSONString' => json_encode($params)],
            ['ignore_auto_number_generation' => $ignore ? 'true' : 'false']
        );
    }

    public function getPurchaseOrder($purchaseorder_id)
    {
        return $this->curlRequest("/purchaseorders/{$purchaseorder_id}");
    }

    private function curlRequest($alias, $method = 'GET', array $params = [], array $urlParams = [])
    {
        $this->_curlObject = $this->initializeCurlObject();
        if ($method == 'POST') {
            curl_setopt($this->_curlObject, CURLOPT_POST, true);
        } else {
            curl_setopt($this->_curlObject, CURLOPT_CUSTOMREQUEST, $method);
        }
        if ($method == 'GET') {
            curl_setopt($this->_curlObject, CURLOPT_URL, $this->getUrlPath($alias, $params));
        } else {
            curl_setopt($this->_curlObject, CURLOPT_URL, $this->getUrlPath($alias, $urlParams));
            curl_setopt($this->_curlObject, CURLOPT_POSTFIELDS, $params);
        }
        return $this->execute();
    }

    private function initializeCurlObject()
    {
        $this->_curlObject = curl_init('');
        curl_setopt($this->_curlObject, CURLOPT_RETURNTRANSFER, 1);
        return $this->_curlObject;
    }

    private function execute()
    {
        $return = curl_exec($this->_curlObject);
        if (curl_errno($this->_curlObject)) {
            $errorNo = curl_errno($this->_curlObject);
            $errorText = curl_error($this->_curlObject);
            throw new \Exception("cUrl Error ({$errorNo}) : {$errorText}.");
        }
        curl_close($this->_curlObject);
        $return = json_decode($return);
        if ($return->code == 0) {
            return $return;
        } else {
            throw new \Exception("Zoho Error ({$return->code}) : {$return->message}.");
        }
    }

    private function getUrlPath($alias, $params = [])
    {
        return 'https://' . preg_replace('/\/+/', '/', "{$this->_baseUrl}/{$alias}") . '?'
        . http_build_query(array_merge($this->getAuthParams(), $params ?: []));
    }

    /**
     * @return array
     */
    public function getAuthParams()
    {
        return array_merge(['authtoken' => $this->accessToken, 'organization_id' => $this->organizationId]);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getParamsQuery(array $params)
    {
        return http_build_query($params);
    }
}
