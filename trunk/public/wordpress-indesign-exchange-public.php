<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wordpress_Indesign_Exchange
 * @subpackage Wordpress_Indesign_Exchange/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Indesign_Exchange
 * @subpackage Wordpress_Indesign_Exchange/public
 * @author     Your Name <email@example.com>
 */
class Wordpress_Indesign_Exchange_Public {

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
	 * @var      string    $Wordpress_Indesign_Exchange       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $Wordpress_Indesign_Exchange, $version ) {

		$this->Wordpress_Indesign_Exchange = $Wordpress_Indesign_Exchange;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Indesign_Exchange_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Indesign_Exchange_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Indesign_Exchange_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Indesign_Exchange_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

}
