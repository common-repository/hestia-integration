<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       escolhadigital.com
 * @since      1.0.0
 *
 * @package    Wpcpapi
 * @subpackage Wpcpapi/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpcpapi
 * @subpackage Wpcpapi/admin
 * @author     Escolha Digital <geral@escolhadigital.com>
 */
class Wpcpapi_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcpapi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcpapi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// BOOTSTRAP
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/style.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcpapi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcpapi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// BOOTSTRAP
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/webservice-ed-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */

	public function add_plugin_admin_menu()
	{

		/*
     * Add a settings page for this plugin to the Settings menu.
     *
     * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
     *
     *        Administration Menus: http://codex.wordpress.org/Administration_Menus
     *
     */
		if (WPCP_API_SETTINGS == true) {
			add_options_page(
				WPCP_PLUGIN_SETTINGS_NAME,
				WPCP_PLUGIN_SETTINGS_NAME,
				'manage_options',
				$this->plugin_name,
				array($this, 'display_plugin_setup_page')
			);
		}

		if (WPCP_API_FUNCTIONS == true) {
			add_options_page(
				WPCP_PLUGIN_FUNCTIONS_NAME,
				WPCP_PLUGIN_FUNCTIONS_NAME,
				'manage_options',
				$this->plugin_name . '_buttons',
				array($this, 'wpcp_api_display_buttons')
			);
		}
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */

	public function add_action_links($links)
	{

		/*
      *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
      */
		$settings_link = array(
			'<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge($settings_link, $links);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */

	public function display_plugin_setup_page()
	{
		include_once('partials/display-admin-settings.php');
	}

	public function wpcp_api_display_buttons()
	{

		$functions = new Wpcpapi_Functions();

		// echo "TEST ";
		// $functions->importProducts(array());
		// $functions->getProducts();
		// $functions->getCategories(array());
		// $functions->getProduct(439870);
		// $functions->importImages(439870);

		// print_R(get_current_screen());

		// INCLUDE HTML
		if (WPCP_API_PRODUCTS_PAGE_IMPORT == true) {
			global $current_page, $total_pages;
			$current_products_page = $functions->getCurrentImportPage('products');
			$current_images_page = $functions->getCurrentImportPage('images');
			$total_pages = $functions->getTotalProductPages();
		}
		include_once('partials/display-buttons.php');

		// RUN REQUESTS
		if (!empty($_GET['wpcpa_action'])) {
			switch ($_GET['wpcpa_action']) {
				case 'connect':
					$functions->connect();
					break;
				case 'import-products':
					$functions->importProducts(array());
					break;
				case 'import-products-page':
					$functions->importProductsByPage(array());
					break;
				case 'delete-products':
					$functions->deleteProducts(array());
					break;
				case 'update-stock':
					$functions->updateStock(array());
					break;
				case 'import-categories':
					$functions->importCategories(array());
					break;
				case 'import-images':
					$functions->importImages(array());
					break;
				case 'import-images-page':
					$functions->importImagesByPage(array());
					break;
			}
		}
	}

	/**
	 *
	 * On Option update
	 *
	 * @since    1.0.0  
	 **/
	public function options_update()
	{
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}

	/**
	 *
	 * Field Validation
	 *
	 * @since    1.0.0  
	 **/
	public function validate($input)
	{

		// All checkboxes inputs        
		$valid = array();

		// Cleanup
		// $valid['cleanup'] = (isset($input['cleanup']) && !empty($input['cleanup'])) ? 1 : 0;

		/*$valid['sandbox'] = $input['sandbox'];
    $valid['live'] = $input['live'];
    $valid['server'] = $input['server'];
    $valid['port'] = $input['port'];
    $valid['user'] = $input['user'];
    $valid['pass'] = $input['pass'];
    $valid['table'] = $input['table'];
	$valid['software'] = $input['software'];
	$valid['document'] = $input['document'];
    $valid['check_checkout_stock'] = $input['check_checkout_stock'];
    $valid['check_product_by'] = $input['check_product_by'];
    $valid['cron_minutes'] = $input['cron_minutes'];*/
		// $valid['debug'] = $input['debug'];

		foreach ($input as $id => $value) {
			$valid[$id] = $value;
		}

		return $valid;
	}

	public function add_plugin_admin_init()
	{

		$functions = new Wpcpapi_Functions();
		$functions->init();
	}

	public function add_plugin_admin_meta_boxes()
	{

		$functions = new Wpcpapi_Functions();
		$functions->addMetaBox();
	}

	public function add_plugin_admin_save_post($post_id)
	{

		$functions = new Wpcpapi_Functions();
		$functions->saveMetaBox($post_id);
	}
}
