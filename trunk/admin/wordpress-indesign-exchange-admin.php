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
	 * @var      string 		$Wordpress_Indesign_Exchange    The ID of this plugin.
	 */
	private $Wordpress_Indesign_Exchange;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string   	 	$version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The root dom element
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		DOMDocument		$dom		The root dom element.
	 */
	private static $dom;

	/**
	 * The list of files, which need to be exported.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @var		array			$files		The list of files, which need to be exported.
	 */
	public static $files;

	/**
	 * The status, if a gallery was found.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @var		boolean			$gallery_found		The status, if a gallery was found.
	 */
	public static $gallery_found;

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
		$this->files = array();
		$this->gallery_found = false;
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
		// initialize for shortcodes
		require_once(ABSPATH . '/wp-settings.php');
		require_once(ABSPATH . '/wp-load.php');
		require_once(ABSPATH . WPINC . '/load.php');
		wp_load_translations_early();
		global $pagenow, $wp_locale, $wp_rewrite;
		$wp_rewrite = new WP_Rewrite();

		if ($pagenow=='tools.php' && isset($_GET['indesign_download']) && $_GET['indesign_download']=='1') {
			// user wants to download xml export file
			$requirement = array(
				'filename' => isset($_GET['filename']) ? $_GET['filename'] : 'export.xml',
				'root_element' => isset($_GET['rootElement']) ? $_GET['rootElement'] : 'indesign-import',
				'date_format' => isset($_GET['dateFormat']) ? $_GET['dateFormat'] : 'd.m.Y',
				'include' => isset($_GET['include']) ? $_GET['include'] : '',
			);

			$this->dom = new DOMDocument( "1.0", "UTF-8" );
			$this->dom->preserveWhitespace = false;
			$this->dom->formatOutput = false;

			$stylesheet = $this->dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $requirement['filename'] . '.xslt"');
			$this->dom->appendChild($stylesheet);

			$root = $this->dom->createElement($requirement['root_element']);

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

			$zip = new ZipArchive();
			$filename = tempnam('/tmp/', 'wp-id-exchange-');
			if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
				exit("cannot open <$filename>\n");
			}

			foreach($posts as $p) {
				$xml_post = $this->dom->createElement($p->post_type);
				$xml_post->setAttribute('id', $p->ID);
				
				$post_title = $this->dom->createElement('post_title', $p->post_title);
				$xml_post->appendChild($post_title);
				$post_date = $this->dom->createElement('post_date', date_create($p->post_date)->format($requirement['date_format']));
				$xml_post->appendChild($post_date);

				$post_author = $this->dom->createElement('post_author', get_the_author_meta('display_name', $p->post_author));
				$xml_post->appendChild($post_author);

				// the gallery is a bit tricky, because we need the full size images instead of the small ones, minus all the markup
				remove_shortcode('gallery', 'gallery_shortcode');
				add_shortcode('gallery', array('Wordpress_Indesign_Exchange_Admin', 'export_gallery_shortcode'));

				$post_content = $p->post_content;
				$post_content_paragraphs = explode("\n", $post_content);
				$post_content = $this->dom->createElement('post_content');

				foreach($post_content_paragraphs as $para) {
					$last_paragraph = $this->dom->createElement('p', apply_filters('the_content', $para, $p->ID));
					if (Wordpress_Indesign_Exchange_Admin::$gallery_found === true) {
						$gallery_element = $this->dom->createElement('gallery');
						foreach (Wordpress_Indesign_Exchange_Admin::$files as $f) {
							$image_element = $this->dom->createElement('image');
							$image_href_attribute = $this->dom->createAttribute('href');
							$image_href_attribute->value = 'file://attachments/' . basename($f);
							$image_element->appendChild($image_href_attribute);

							$zip->addFile($f, '/attachments/' . basename($f));

							$gallery_element->appendChild($image_element);
						}
						$last_paragraph->appendChild($gallery_element);
						Wordpress_Indesign_Exchange_Admin::$files = array();
						Wordpress_Indesign_Exchange_Admin::$gallery_found = false;
					}
					$post_content->appendChild($last_paragraph);
				}
				$xml_post->appendChild($post_content);

				$root->appendChild($xml_post);
			}

			$this->dom->appendChild($root);

			// echo $this->dom->saveXML();
			// exit();

			$zip->addFromString($requirement['filename'] . '.xml', $this->dom->saveXML());
			$zip->addFile(plugin_dir_path(__FILE__) . 'partials/export.xslt', $requirement['filename'] . '.xslt');
			$zip->close();

			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=" . $requirement['filename'] . '.zip');
			header("Pragma: no-cache");
			header("Expires: 0");

			echo file_get_contents($filename);
			exit();
		}
	}

	public static function export_gallery_shortcode($atts = array(), $content) {
		if (!isset($atts['ids'])) return;
		$ids = explode(',', $atts['ids']);
		foreach ($ids as $attachment) {
			Wordpress_Indesign_Exchange_Admin::$files[] = get_attached_file($attachment);
		}
		Wordpress_Indesign_Exchange_Admin::$gallery_found = true;
	}
}
