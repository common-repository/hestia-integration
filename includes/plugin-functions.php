<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       escolhadigital.com
 * @since      1.0.0
 *
 * @package    Wpcpapi
 * @subpackage Wpcpapi/includes
 */


class Wpcpapi_Functions extends Wpcpapi
{

	protected $url;
	protected $username;
	protected $password;
	protected $software;
	protected $document;

	private $key;
	private $secret;
	private $client_id;

	private $debug = FALSE;
	private $dev = FALSE;

	// NEW VARS
	protected $plugin_name;
	// private $plugin_options;
	private $factory;

	// OLD VARS
	protected $soap_client;

	/**
	 * Define the core functionality of the class.
	 *
	 */
	public function __construct()
	{

        $this->plugin_name = WPCP_API_NAME;

		$plugin_options = $this->getPluginOptions();
		$this->url = (!empty($plugin_options['server'])) ? $plugin_options['server'] : '';
		$this->username = (!empty($plugin_options['user'])) ? $plugin_options['user'] : '';
		$this->password = (!empty($plugin_options['pass'])) ? $plugin_options['pass'] : '';
		$this->software = (!empty($plugin_options['software'])) ? $plugin_options['software'] : '';
		$this->document = (!empty($plugin_options['document'])) ? $plugin_options['document'] : '';
		$this->debug = (!empty($plugin_options['debug'])) ? $plugin_options['debug'] : false;
		$this->dev = (!empty($plugin_options['dev'])) ? $plugin_options['dev'] : false;

		// CHECK IF API IS ENBLED
		$this->initiate($plugin_options);

		// SEND URL, USERNAME, PASSWORD, ID, DEBUG AND DEV

	}

	public function initiate($plugin_options)
	{

		$this->factory = new Wpcpapi_ApiFactory($this->software, $plugin_options);
	}

	function getSoftware()
	{
		return $this->software;
	}

	function getFields()
	{
		return $this->factory->getFields();
	}

	/**
	 * Retrieve the plugin options
	 *
	 * @since     1.0.0
	 * @return    string    Plugin options
	 */
	/*function getPluginOptions()
	{
		$options = get_option($this->plugin_name);

		return $options;
	}*/

	function insertInvoice()
	{
		// $this->factory->insertInvoice();
	}

	public function getClientData()
	{
		// $client = Wpcpapi_Clients::getClientDataFromOrder($order_id);

		// return $client;
	}

	public function init()
	{
		$this->factory->init();
	}

	public function saveMetaBox($post_id)
	{
		$this->factory->saveMetaBox($post_id);
	}

	public function addMetaBox()
	{
		$this->factory->addMetaBox();
	}

	public function connect()
	{
		$this->factory->connect();
	}

	public function auth()
	{
		$this->factory->auth();
	}

	// PHP Check if time is between two times regardless of date
	function TimeIsBetweenTwoTimes($from, $till, $input)
	{
		$f = DateTime::createFromFormat('H:i:s', $from);
		$t = DateTime::createFromFormat('H:i:s', $till);
		$i = DateTime::createFromFormat('H:i:s', $input);
		if ($f > $t) $t->modify('+1 day');
		return ($f <= $i && $i <= $t) || ($f <= $i->modify('+1 day') && $i <= $t);
	}

	/**
	 * ORDER FUNCTIONS
	 */
	function onOrderChange($order_id, $status_from, $status_to)
	{
		$this->factory->onOrderChange($order_id, $status_from, $status_to);
	}

	/**
	 * EMAIL FUNCTIONS
	 */
	function emailBeforeOrderTable($order_id)
	{

		// VERIFY IF FUNCTION EXISTS
		// if (method_exists($this->factory, 'emailBeforeOrderTable')) {
		$this->factory->emailBeforeOrderTable($order_id);
		// }
	}

	function createInvoice($order_id)
	{

		$this->factory->createInvoice($order_id);
	}

	function createPdf($order_id)
	{

		$this->factory->createPdf($order_id);
	}

	/* REQUESTS */
	public function checkStock()
	{
		$this->factory->checkStock();
	}

	public function createReference()
	{
		$this->factory->createReference();
	}

	public function createDeliveryRequest()
	{
		$this->factory->createDeliveryRequest();
	}

	public function createOrderRequest()
	{
		$this->factory->createOrderRequest();
	}

	/* CRON */
	public function setCron()
	{
		$this->factory->setCron();
	}

	public function deactivateCron()
	{
		$this->factory->deactivateCron();
	}

	public function setCronCustomInterval($schedules)
	{
		$this->factory->setCronCustomInterval($schedules);
	}

	/* FILES */
	public function getCurrentImportPage($type = 'products')
	{

		$plugin_dir_path = plugin_dir_path(dirname(__FILE__));

		// GET CURRENT PAGE
		$page = file_get_contents($plugin_dir_path . $type . ".txt");

		return $page;
	}

	public function setCurrentImportPage($page, $type = 'products')
	{

		$plugin_dir_path = plugin_dir_path(dirname(__FILE__));

		// GET CURRENT PAGE
		$file = fopen($plugin_dir_path . $type . ".txt", "w") or die("Unable to open file!");

		fwrite($file, $page);

		fclose($file);

		return $page;
	}

	public function getTotalProductPages()
	{
		$total = $this->factory->getTotalProductPages();

		return $total;
	}

	public function changeTotalImportPages($type = 'products')
	{
		// SET TOTAL PAGES AND START FROM LAST
		$page = $current = $this->getCurrentImportPage($type);
		$total = $this->factory->getTotalProductPages($type);

		$current--;
		if ($current <= 0) {
			$this->setCurrentImportPage($total, $type);
			$page = $total;
		} else {
			$this->setCurrentImportPage($current, $type);
		}

		return $page;
	}
}
