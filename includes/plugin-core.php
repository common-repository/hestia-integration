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

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wpcpapi
 * @subpackage Wpcpapi/includes
 * @author     Escolha Digital <geral@escolhadigital.com>
 */
class Wpcpapi
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpcpapi_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{

		if (defined('WPCP_API_VER')) {
			$this->version = WPCP_API_VER;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = WPCP_API_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wpcpapi_Loader. Orchestrates the hooks of the plugin.
	 * - Wpcpapi_i18n. Defines internationalization functionality.
	 * - Wpcpapi_Admin. Defines all hooks for the admin area.
	 * - Wpcpapi_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		$plugin_dir_path = plugin_dir_path(dirname(__FILE__));

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $plugin_dir_path . 'includes/plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $plugin_dir_path . 'includes/plugin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $plugin_dir_path . 'admin/plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $plugin_dir_path . 'public/plugin-public.php';

		/**
		 * The class for the connection to the database
		 */

		/* Create Order Admin */
		require_once $plugin_dir_path . 'admin/includes/woocommerce/wc-order-admin.php';

		/* OnOrder Status Change */
		require_once $plugin_dir_path . 'admin/includes/woocommerce/wc-order-changed.php';

		/* Create Field Product */
		require_once $plugin_dir_path . 'includes/woocommerce/wc-product.php';

		/* Create Field Checkout */
		require_once $plugin_dir_path . 'includes/woocommerce/wc-checkout.php';

		/* Functions for file management */
		require_once $plugin_dir_path . 'functions/files.php';

		/* The classes for woocommerce support */
		// require_once $plugin_dir_path . 'functions/woocommerce/wc-orders.php';
		require_once $plugin_dir_path . 'functions/woocommerce/wc-products.php';


		/* The class for the functions webservice */
		require_once $plugin_dir_path . 'includes/api.php';
		require_once $plugin_dir_path . 'webservice/hestia.php';
		// includeFilesInDir($plugin_dir_path . 'webservice');

		require_once $plugin_dir_path . 'includes/plugin-functions.php';

		// CRON
		// require_once $plugin_dir_path . 'includes/wp-cron.php';

		// INSTANTIATE LOADER
		$this->loader = new Wpcpapi_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpcpapi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Wpcpapi_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Wpcpapi_Admin($this->getPluginName(), $this->getVersion());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// Add menu item
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

		// Add Settings link to the plugin
		$plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
		$this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

		// Save/Update our plugin options
		$this->loader->add_action('admin_init', $plugin_admin, 'options_update');

		// Init api post types and others
		$this->loader->add_action('init', $plugin_admin, 'add_plugin_admin_init');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_plugin_admin_meta_boxes');
		$this->loader->add_action('save_post', $plugin_admin, 'add_plugin_admin_save_post', 10, 1);
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Wpcpapi_Public($this->getPluginName(), $this->getVersion());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
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

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wpcpapi_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Retrieve the plugin options
	 *
	 * @since     1.0.0
	 * @return    string    Plugin options
	 */
	function getPluginOptions()
	{
		$options = get_option($this->plugin_name);

		return $options;
	}
}
