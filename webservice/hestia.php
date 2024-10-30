<?php
/*
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use GraphQL\RawObject;
*/
class Wpcpapi_Hestia extends Wpcpapi_ApiFactory
{
	private $url;
	private $user;
	private $pass;
	// private $company_code;
	private $request;
	private $order_status;

	private $post_type = 'property';

	public $pintPickUp;
	public $statusWaiting;
	public $statusFulFillment;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	function __construct($options)
	{
		$this->plugin_name = WPCP_API_NAME;
		$this->setOptions($options);
	}

	function setOptions($options)
	{
		$optionsHestia = get_option($this->getPluginName());
		$this->dev = $optionsHestia['dev'];
		if ($this->dev == true) {
			$this->url = WPCP_API_DEV_URL;
		} else {
			$this->url = WPCP_API_PROD_URL;
		}
		$this->user = $optionsHestia['user'];
		$this->pass = $optionsHestia['pass'];
		$this->pintPickUp = $optionsHestia['point_pickup'];
		$this->statusWaiting = $optionsHestia['order_status_waiting'];
		$this->statusFulFillment = $optionsHestia['order_status_fulfillment'];
		$this->samedayzones = $optionsHestia['samedayzone'];
		$this->standardzones = $optionsHestia['standardzones'];
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function getPluginName()
	{
		return $this->plugin_name;
	}

	function connect()
	{
		$request_data = array(
			'query' => 'mutation { backofficeUserSignIn(email: "' . $this->user . '", password: "' . $this->pass . '") { id }}',
			'variables' => array(
				'email' => $this->user,
				'password' => $this->pass
			)
		);
		$request = json_encode($request_data);

		$url = $this->url;

		$response = wp_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.1',
				'headers'     => array(
					'Content-Type' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($this->user . ":" . $this->pass)
				),
				'body'        => $request
			)
		);
		$response = $response['cookies'];
		$hestiaCookie = '';
		foreach ($response as $cookie) {
			if ($cookie->name === '_hestia_key') {
				$hestiaCookie = $cookie->value;
			}
		}

		return $hestiaCookie;
	}

	function curlRequest($request)
	{
		$url = $this->url;

		$cookies = [];
		$cookies[] = new WP_Http_Cookie(array(
			'name'  => '_hestia_key',
			'value' => $this->connect(),
		));

		$response = wp_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.1',
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'cookies' => $cookies,
				'body' => $request
			)
		);

		// JSON DECODE
		$response = json_decode($response['body']);
		return $response;
	}

	/* ORDERS */
	function onOrderChange($order_id, $status_from, $status_to)
	{
		// Lets grab the order
		$order = wc_get_order($order_id);

		$status = 'wc-' . $status_to;

		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);

		if ($this->statusWaiting == $status) {
			$this->addRequest($order_id);
			$this->getRequests();
		}

		if ($this->statusFulFillment == $status) {
			// Update the GraphQL mutation
			$ws_order_id = $order->get_meta('ws_order_id');

			$raw = '{"query":"mutation {\\r\\n\\t requestPartnerEdit(id: \"' . $ws_order_id . '\", input: {\\r\\n  type: ORDER, status:FULFILED }){\\r\\n  \\t id\\r\\n \\t\\r\\n \\r\\n\\t}\\r\\n}","variables":{}}';
			$result = $this->curlRequest($raw);
		}
		return $result;
	}

	/**
	 * Get all order statuses.
	 *
	 * @since 2.2
	 * @used-by WC_Order::set_status
	 * @return array
	 */
	function getPickPointsBko()
	{

		// RAW REQUEST
		$raw = '{"query":"query {\\r\\n\\tpickupPointsPartner(pagination: {\\r\\n    page: 1, pageSize:100\\r\\n  }){\\r\\n  \\tentries {\\r\\n  \\t  id, name,\\r\\n      address {\\r\\n        street,\\tpostalCode\\r\\n      }\\r\\n      description, isEnabled\\r\\n  \\t}\\r\\n    pageSize, pageNumber, totalPages\\r\\n\\t}\\r\\n}","variables":{}}';
		$result = $this->curlRequest($raw);
		if (isset($result->data->pickupPointsPartner->entries)) {
			foreach ($result->data->pickupPointsPartner->entries as $points) {
				$pointsPickup[] = ['id' => $points->id, 'name'   => $points->name];
			}
		} else {
			if (empty($this->user) && empty($this->pass)) {
				return ['error' => '&nbsp;'];
			} elseif (empty($this->user)) {
				return ['error' => 'Username não definido.'];
			} elseif (empty($this->pass)) {
				return ['error' => 'Password não definido.'];
			} else {
				return ['error' => 'Não existem pontos pickup ou a conexão não foi bem sucedida.'];
			}
		}
		return $pointsPickup;
	}

	function getPickupPoints()
	{
		// RAW REQUEST
		$raw = '{"query":"query {\\r\\n\\tpickupPointsPartner(pagination: {\\r\\n    page: 1, pageSize:2\\r\\n  }){\\r\\n  \\tentries {\\r\\n  \\t  id, name,\\r\\n      address {\\r\\n        street,\\tpostalCode\\r\\n      }\\r\\n      description, isEnabled\\r\\n  \\t}\\r\\n    pageSize, pageNumber, totalPages\\r\\n\\t}\\r\\n}","variables":{}}';
		$result = $this->curlRequest($raw);

		return $result;
	}

	function getRequests()
	{
		// RAW REQUEST
		$raw = '{"query":"query {\\r\\n\\t request(id: \"bfef8e6b-5948-485f-8c1a-4842ff959c22\" ){\\r\\n  id, status \\r\\n\\t}\\r\\n}","variables":{}}';
		$result = $this->curlRequest($raw);
		if ($this->dev == true) {
			echo "<pre>";
			echo "<br>ALL REQUESTS: ";
			print_R($result);
			echo "</pre>";
		}
		return $result;
	}

	function addRequest($order_id)
	{

		// Lets grab the order
		$order = wc_get_order($order_id);

		$date = date('m/d/Y h:i:s a', time());
		$order->add_order_note("({$date}) ADD REQUEST" );
		
		// Verify methods in config
		$shipping_methods = $order->get_shipping_methods();
		$shipping_method = @array_shift($shipping_methods);
		$shipping_method_id = $shipping_method['method_id'];
		$shipping_method_instance_id = $shipping_method['instance_id'];

		$rate_id = $shipping_method_id . ':' . $shipping_method_instance_id;
		$zone_id = $this->get_shipping_zone_from_method_rate_id($rate_id);

		// VERIFY SHIPPING INTEGRATION
		if (isset($this->samedayzones[$zone_id . '_' . $shipping_method_instance_id])) {
			$priority = 'SAME_DAY';
		} elseif (isset($this->standardzones[$zone_id . '_' . $shipping_method_instance_id])) {
			$priority = 'STANDARD';
		} else {
			$order->add_order_note("({$date}) NO HESTIA METHOD SELECTED");
			return;
		}

		$ws_order_id = $order->get_meta('ws_order_id');

		// case ws_order_id in Order
		if (empty($ws_order_id)) {

			$order->add_order_note("({$date}) ADD ORDER REQUEST");

			// $customer = new WC_Customer($order->get_customer_id());
			// $raw_customer = 'customer: {name: \"' . $customer->get_first_name() . '\", surname:\"' . $customer->get_last_name() . '\", phoneNumber: \"' . $customer->get_billing_phone() . '\", email: \"' . $customer->get_email() . '\", address: {street: \"' . $this->removeAcento($customer->get_shipping_address_1()) . '\",  number: \"0\",  postalCode: \"' . $customer->get_shipping_postcode() . '\", country: \"' . $this->removeAcento($customer->get_shipping_country()) . '\",  district: \"null\", city: \"' . $this->removeAcento($customer->get_shipping_city()) . '\"}}';
			
			$first_name = $order->get_shipping_first_name();
			$last_name = $order->get_shipping_last_name();
			$company = $order->get_shipping_company();

			$address_1 = $order->get_shipping_address_1();
			$address_2 = $order->get_shipping_address_2();
			$address = $address_1 . " " . $address_2;

			$city = $order->get_shipping_city();
			$state = $order->get_shipping_state();
			$postcode = $order->get_shipping_postcode();
			$country = $order->get_shipping_country();

			$email = $order->get_billing_email();

			$phone = $order->get_billing_phone();

			$raw_customer = 'customer: {name: \"' . $first_name . '\", surname:\"' . $last_name . '\", phoneNumber: \"' . $phone . '\", email: \"' . $email . '\", address: {street: \"' . $this->removeAcento($address) . '\",  number: \"0\",  postalCode: \"' . $postcode . '\", country: \"' . $this->removeAcento($country) . '\",  district: \"null\", city: \"' . $this->removeAcento($city) . '\"}}';

			// This is how to grab line items from the order
			$line_items = $order->get_items();
			$itens_raw = '';
			// This loops over line items
			foreach ($line_items as $item) {
				// This will be a product
				$product = wc_get_product($item['product_id']);

				// Line item total cost including taxes and rounded
				$weight =  intval($product->get_weight());
				$volume = intval($product->get_width()) * intval($product->get_height()) * intval($product->get_length());

				$itens_raw .= '\\r\\n{\\r\\n type: ENVELOPE, volume: ' . $volume * 1 . ',weight: ' . $weight * 1 . '\\r\\n}\\r\\n,\\r\\n';
			}

			// Create the GraphQL mutation
			//$pickupPointId = $this->getPickupPoints()->data->pickupPointsPartner->entries[0]->id;

			$raw = '{"query":"mutation {\\r\\n\\t requestPartnerCreate(input: {\\r\\n  type: ORDER, status:WAITING_FULFILMENT, pickupPointId: \"' . $this->pintPickUp . '\", partner_internal_id:  \"' . $order_id . '\", priority:  ' . $priority . '\\r\\n, items: [' . $itens_raw . ']\\r\\n, , ' . $raw_customer . ' }){\\r\\n  \\t id\\r\\n trackingCode \\t\\r\\n \\r\\n\\t}\\r\\n}","variables":{}}';
			$result = $this->curlRequest($raw);

			if ($this->dev == true) {
				echo "<pre>";
				echo "<br>request Create RESULT: ";
				print_R($raw);

				print_R($result);
				echo "</pre>";
			}

			$result_print = print_R($result, true);

			// LOG RESULT
			$this->log('Set Order', $result_print);

			// ADD ORDER NOTE
			$order->add_order_note("({$date}) RESULT: {$result_print}");

			// Add id api in order
			$this->add_api_order_id($order_id, $result->data->requestPartnerCreate->id);

			$this->add_api_tracking_code($order_id, $result->data->requestPartnerCreate->trackingCode);
		} else {

			if ($this->dev == true) {
				echo "<pre>";
				echo "<br>request Create RESULT: ";
				print_R($ws_order_id);
				echo "</pre>";
			}

			// ADD ORDER NOTE
			$order->add_order_note("({$date}) ALREADY EXIST #" . $ws_order_id);

			// LOG RESULT
			// $this->log('ALREADY EXIST', $ws_order_id);
		}
	}

	/**
	 * Alternate wp_set_auth_cookie function
	 *
	 * @since 1.1.1
	 * @package wp-graphql-cors
	 */

	function removeAcento($string)
	{
		return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
	}

	function add_api_order_id($order_id, $apiOrderId)
	{
		if (!empty($apiOrderId)) {
			update_post_meta($order_id, 'ws_order_id', sanitize_text_field($apiOrderId));
		}
	}
	function add_api_tracking_code($order_id, $trackingCode)
	{
		update_post_meta($order_id, 'ws_tracking_code', sanitize_text_field($trackingCode));
	}

	function get_shipping_zone_from_method_rate_id($method_rate_id)
	{
		global $wpdb;

		$data = explode(':', $method_rate_id);
		$method_id = $data[0];
		$instance_id = $data[1];

		// The first SQL query
		$zone_id = $wpdb->get_col("
        SELECT wszm.zone_id
        FROM {$wpdb->prefix}woocommerce_shipping_zone_methods as wszm
        WHERE wszm.instance_id = '$instance_id'
        AND wszm.method_id LIKE '$method_id'
    ");
		$zone_id = reset($zone_id); // converting to string

		// 1. Wrong Shipping method rate id
		if (empty($zone_id)) {
			return __("Error! doesn't exist…");
		}
		// 2. Default WC Zone name
		elseif ($zone_id == 0) {
			return __("All Other countries");
		}
		// 3. Created Zone name
		else {
			// The 2nd SQL query
			$zone_name = $wpdb->get_col("
            SELECT wsz.zone_id
            FROM {$wpdb->prefix}woocommerce_shipping_zones as wsz
            WHERE wsz.zone_id = '$zone_id'
        ");
			return reset($zone_name); // converting to string and returning the value
		}
	}

	function log($type, $output)
	{

		$plugin_dir_path = plugin_dir_path(dirname(__FILE__));

		// GET CURRENT DAY AND TIME
		$current_daytime = date("d-m-Y H:i:s");

		$message = '\n\n<br /><br />-----------------------------------<br />\r\n';
		$message .= 'This is a test mail sent by WordPress automatically as per your schedule.<br />\r\n';
		$message .= "{$type}: {$current_daytime}\r\n";

		// PREPARE DATA
		$breaks = array("<br />", "<br>", "<br/>");
		$message .= str_ireplace($breaks, "\r\n", $output);

		// SEND EMAIL
		$recepients = 'joelrocha@escolhadigital.com';
		$subject = "Cron Job {$current_daytime} - {$type}";
		// wp_mail($recepients, $subject, $message);

		// CREATE LOG
		$file = fopen($plugin_dir_path . "log.txt", "a") or die("Unable to open file!");
		fwrite($file, $message);
		fclose($file);
	}
}

function wc_get_pickup_points()
{
	// Lets grab the order
	$hestia = new Wpcpapi_Hestia([]);

	return $hestia->getPickPointsBko();
}

// add_action('woocommerce_order_status_changed', 'wcpcapi_on_order_status_change', 10, 3);
