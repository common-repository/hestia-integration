<?php

class Wpcpapi_ApiFactory
{

	private $url;
	private $username;
	private $password;

	private $key;
	private $secret;
	private $client_id;

	private $document;

	private $debug = FALSE;
	private $dev = FALSE;

    public $pintPickUp;
    public $statusWaiting;
    public $statusFulFillment;

    private $api;
	private $debug_to = "joelrocha@escolhadigital.com";
    public $options;

	function __construct($software, $options)
	{

		$this->url = (!empty($plugin_options['server'])) ? $options['server'] : '';
		$this->username = (!empty($plugin_options['user'])) ? $options['user'] : '';
		$this->password = (!empty($plugin_options['pass'])) ? $options['pass'] : '';

		$this->key = (!empty($plugin_options['key'])) ? $options['key'] : '';
		$this->secret = (!empty($plugin_options['secret'])) ? $options['secret'] : '';
		$this->client_id = (!empty($plugin_options['client_id'])) ? $options['client_id'] : '';

		$this->debug = (!empty($options['debug'])) ? $options['debug'] : false;
		$this->dev = (!empty($options['dev'])) ? $options['dev'] : false;

		$this->document = (!empty($plugin_options['document'])) ? $options['document'] : '';
        $this->pintPickUp = (!empty($plugin_options['point_pickup'])) ? $options['point_pickup'] : '';
        $this->statusWaiting = (!empty($plugin_options['order_status_waiting'])) ? $options['order_status_waiting'] : '';
        $this->statusFulFillment = (!empty($plugin_options['order_status_fulfillment'])) ? $options['order_status_fulfillment'] : '';

		if (!empty($software)) {
			$class_api = "Wpcpapi_$software";
			$this->api = new $class_api($options);
		}
	}


	function init()
	{
		if (method_exists($this->api, 'init')) {
			return $this->api->init();
		}
	}

	function saveMetaBox($post_id)
	{
		if (method_exists($this->api, 'saveMetaBox')) {
			return $this->api->saveMetaBox($post_id);
		}
	}

	function addMetaBox()
	{
		if (method_exists($this->api, 'addMetaBox')) {
			return $this->api->addMetaBox();
		}
	}

	function connect()
	{
		if (method_exists($this->api, 'connect')) {
			return $this->api->connect();
		}
	}

	function getFields()
	{
		if (method_exists($this->api, 'getFields')) {
			return $this->api->getFields();
		}
	}

	function auth()
	{
		if (method_exists($this->api, 'auth')) {
			return $this->api->auth();
		}
	}

	/**
	 * Get products
	 * 
	 * Get Products List
	 * 
	 * @ options
	 *
	 * @since	1.0.0
	 * @access	public
	 * @return	array
	 */

	function getProducts($options = array())
	{
		if (method_exists($this->api, 'getProducts')) {
			return $this->api->getProducts($options);
		}
	}

	function getProduct($id)
	{
		if (method_exists($this->api, 'getProduct')) {
			return $this->api->getProduct($id);
		}
	}

	function insertProducts($data)
	{
		if (method_exists($this->api, 'insertProducts')) {
			return $this->api->insertProducts($data);
		}
	}

	function deleteProducts($data)
	{
		if (method_exists($this->api, 'deleteProducts')) {
			return $this->api->deleteProducts($data);
		}
	}

	function insertImages($data)
	{
		if (method_exists($this->api, 'insertImages')) {
			return $this->api->insertImages($data);
		}
	}

	function getCategories($data)
	{
		if (method_exists($this->api, 'getCategories')) {
			return $this->api->getCategories($data);
		}
	}

	function insertCategories($data)
	{
		if (method_exists($this->api, 'insertCategories')) {
			return $this->api->insertCategories($data);
		}
	}

	function updateProductsStocks($data)
	{
		if (method_exists($this->api, 'updateProductsStocks')) {
			return $this->api->updateProductsStocks($data);
		}
	}

	function onOrderChange($order_id, $status_from, $status_to)
	{
		if (method_exists($this->api, 'onOrderChange')) {
			$this->api->onOrderChange($order_id, $status_from, $status_to);
		}
	}

	function emailBeforeOrderTable($order_id)
	{
		if (method_exists($this->api, 'emailBeforeOrderTable')) {
			$this->api->emailBeforeOrderTable($order_id);
		}
	}

	// function updateProductStock();

	// ORDERS
	// function insertOrder();

	// PDF
	function createPDF($order_id)
	{
		if (method_exists($this->api, 'createPDF')) {
			$this->api->createPDF($order_id);
		}
	}

	// INVOICES
	// function generateInvoice();

	function createInvoice($order_id)
	{
		if (method_exists($this->api, 'createInvoice')) {
			$this->api->createInvoice($order_id);
		}
	}

	// DATA
	function convertData($products)
	{
		return $this->api->convertData($products);
	}

	// DEBUG
	function sendDebugEmail($subject = "Debug Email", $result)
	{

		$to = $this->debug_to;

		if ($this->debug == TRUE) {
			$to = $this->debug_to;
			$message = print_r($result, true);
			wp_mail($to, $subject, $message);
		}
	}

	/* REQUESTS */
	function checkStock()
	{
		if (method_exists($this->api, 'checkStock')) {
			$this->api->checkStock();
		}
	}

	function createReference()
	{
		if (method_exists($this->api, 'checkStock')) {
			$this->api->createReference();
		}
	}

	function createDeliveryRequest()
	{
		if (method_exists($this->api, 'createDeliveryRequest')) {
			$this->api->createDeliveryRequest();
		}
	}

	function createOrderRequest()
	{
		if (method_exists($this->api, 'createOrderRequest')) {
			$this->api->createOrderRequest();
		}
	}

	/* CRON */
	public function setCron()
	{
		if (method_exists($this->api, 'setCron')) {
			$this->api->setCron();
		}
	}

	public function deactivateCron()
	{
		if (method_exists($this->api, 'deactivateCron')) {
			$this->api->deactivateCron();
		}
	}

	public function setCronCustomInterval($schedules)
	{
		if (method_exists($this->api, 'setCronCustomInterval')) {
			$this->api->setCronCustomInterval($schedules);
		}
	}

	/* PAGES */
	public function getTotalProductPages()
	{
		if (method_exists($this->api, 'getTotalProductPages')) {
			return $this->api->getTotalProductPages();
		}
	}
}
