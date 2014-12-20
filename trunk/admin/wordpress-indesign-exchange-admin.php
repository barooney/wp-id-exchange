<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://barooney.de/meine-projekte/wordpress-indesign-exchange/
 * @since      1.0.0
 *
 * @package    Wordpress_Indesign_Exchange
 * @subpackage Wordpress_Indesign_Exchange/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Indesign_Exchange
 * @subpackage Wordpress_Indesign_Exchange/admin
 * @author     Daniel Baron <daniel@barooney.de>
 */
class Wordpress_Indesign_Exchange_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Wordpress_Indesign_Exchange    The ID of this plugin.
	 */
	private $Wordpress_Indesign_Exchange;

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
	 * @var      string    $Wordpress_Indesign_Exchange       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $Wordpress_Indesign_Exchange, $version ) {

		$this->Wordpress_Indesign_Exchange = $Wordpress_Indesign_Exchange;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Indesign_Exchange_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Indesign_Exchange_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Indesign_Exchange_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Indesign_Exchange_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 *
	 * Register the options page
	 *
	 * @since 	1.0.0
	 *
	 */
	public function add_options_page() {
		add_options_page('InDesign Exchange Settings', 'InDesign Exchange', 'manage_options', plugin_dir_path( __FILE__ ) . 'partials/wordpress-indesign-exchange-admin-options.php');
	}

	/**
	 *
	 * Register the management page
	 *
	 * @since 	1.0.0
	 *
	 */
	public function add_management_page() {
		add_management_page('InDesign Exchange', 'InDesign Exchange', 'publish_posts', plugin_dir_path( __FILE__ ) . 'partials/wordpress-indesign-exchange-admin-management.php');
	}
}
