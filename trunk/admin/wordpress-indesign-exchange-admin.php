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

		wp_enqueue_style( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'css/wordpress-indesign-exchange-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->Wordpress_Indesign_Exchange, plugin_dir_url( __FILE__ ) . 'js/wordpress-indesign-exchange-admin.js', array( 'jquery' ), $this->version, false );

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

	public function get_indesign_xml() {
		global $pagenow;
		if ($pagenow=='tools.php' && isset($_GET['indesign_download']) && $_GET['indesign_download']=='1') {
			// user wants to download xml export file
			$requirement = array(
				'filename' => isset($_GET['filename']) ? $_GET['filename'] : 'export.xml',
				'root_element' => isset($_GET['rootElement']) ? $_GET['rootElement'] : 'indesign-export',
				'date_format' => isset($_GET['dateFormat']) ? $_GET['dateFormat'] : 'd.m.Y',
				'include' => isset($_GET['include']) ? $_GET['include'] : '',

			);
			header("Content-type: application/xml; charset=UTF-8");
			header("Content-Disposition: attachment; filename=" . $requirement['filename']);
			header("Pragma: no-cache");
			header("Expires: 0");
			$dom = new DOMDocument( "1.0", "UTF-8" );
			$dom->preserveWhitespace = false;
			$dom->formatOutput = false;

			$root = $dom->createElement($requirement['root_element']);

			$posts = get_posts(array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'post_date',
				'order'            => 'DESC',
				'include'          => $requirement['include'],
				'exclude'          => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'post',
				'post_mime_type'   => '',
				'post_parent'      => '',
				'post_status'      => 'publish',
				'suppress_filters' => true
			));

			foreach($posts as $p) {
				$xml_post = $dom->createElement($p->post_type);
				$xml_post->setAttribute('id', $p->ID);
				$post_title = $dom->createElement('post_title', $p->post_title);
				$xml_post->appendChild($post_title);
				$post_date = $dom->createElement('post_date', date_create($p->post_date)->format($requirement['date_format']));
				$xml_post->appendChild($post_date);

				$post_content = $p->post_content;
				$post_content_paragraphs = explode("\n", $post_content);
				$post_content = $dom->createElement('post_content');

				foreach($post_content_paragraphs as $para) {
					if ($para !== '' && $para !== '<!--more-->') $post_content->appendChild($dom->createElement('p', $para));
				}
				$xml_post->appendChild($post_content);

				$root->appendChild($xml_post);
			}

			$dom->appendChild($root);

			echo $dom->saveXML();
			exit();
		}
	}
}
