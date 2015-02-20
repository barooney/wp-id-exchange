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
		Wordpress_Indesign_Exchange_Admin::$files = array();
		Wordpress_Indesign_Exchange_Admin::$gallery_found = false;
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

			Wordpress_Indesign_Exchange_Admin::$dom = new DOMDocument( "1.0", "UTF-8" );
			Wordpress_Indesign_Exchange_Admin::$dom->preserveWhitespace = false;
			Wordpress_Indesign_Exchange_Admin::$dom->formatOutput = false;

			$stylesheet = Wordpress_Indesign_Exchange_Admin::$dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $requirement['filename'] . '.xslt"');
			Wordpress_Indesign_Exchange_Admin::$dom->appendChild($stylesheet);

			$root = Wordpress_Indesign_Exchange_Admin::$dom->createElement($requirement['root_element']);

			$posts = get_posts(array(
				'posts_per_page'   => 0,
				'post_type'        => 'any',
				'orderby'          => 'post_date',
				'order'            => 'DESC',
				'include'          => $requirement['include'],
				'suppress_filters' => true,
			));

			$zip = new ZipArchive();
			$filename = tempnam('/tmp/', 'wp-id-exchange-');
			if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
				exit("cannot open <$filename>\n");
			}

			// iterate through the posts
			foreach($posts as $p) {
				$xml_post = Wordpress_Indesign_Exchange_Admin::$dom->createElement($p->post_type);
				$xml_post->setAttribute('id', $p->ID);
				
				$post_title = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_title', $p->post_title);
				$xml_post->appendChild($post_title);
				$post_date = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_date', date_create($p->post_date)->format($requirement['date_format']));
				$xml_post->appendChild($post_date);

				$post_author = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_author', get_the_author_meta('display_name', $p->post_author));
				$xml_post->appendChild($post_author);

				// remove the WordPress filter to automatically apply <p> tags
				remove_filter('the_content', 'wpautop');

				// the gallery is a bit tricky, because we need the full size images instead of the small ones, minus all the markup
				remove_shortcode('gallery', 'gallery_shortcode');
				add_shortcode('gallery', array('Wordpress_Indesign_Exchange_Admin', 'export_gallery_shortcode'));

				$post_content = $p->post_content;
				$post_content_paragraphs = explode("\n", $post_content);
				$post_content = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_content');

				// iterate through the post's paragraphs
				$paragraph_ctr = 0;
				for ( $paragraph_ctr = 0; $paragraph_ctr < count($post_content_paragraphs); $paragraph_ctr++ ) {
					$para = $post_content_paragraphs[$paragraph_ctr];

					// skip "more" tags
					// TODO #42: create checkbox to keep them in an article to generate a page break
					if ('<!--more-->' === $para) {
						continue;
					}

					// skip empty paragraphs
					if ('' === $para || ' ' === $para || '&nbsp;' == $para) {
						continue;
					}

					// handle headlines
					if (strpos($para, '<h1') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h1', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}
					if (strpos($para, '<h2') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h2', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}
					if (strpos($para, '<h3') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h3', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}
					if (strpos($para, '<h4') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h4', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}
					if (strpos($para, '<h5') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h5', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}
					if (strpos($para, '<h6') !== FALSE) {
						$head_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('h6', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($head_element);
						continue;
					}

					// blockquotes and cites
					if (strpos($para, '<blockquote') !== FALSE) {
						$blockquote_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('blockquote', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($blockquote_element);
						continue;
					}

					if (strpos($para, '<cite') !== FALSE) {
						$blockquote_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('cite', strip_tags($para, '<em><strong><i><b>'));
						$post_content->appendChild($blockquote_element);
						continue;
					}

					// handle tables
					if (strpos($para, '<table') !== FALSE) {
						$table_html = $para;
						do {
							$paragraph_ctr++;
							$para = $post_content_paragraphs[$paragraph_ctr];
							$table_html .= $para;
						} while (strpos($para, '</table') === FALSE);

						$cols = 0;
						$table_doc = new DOMDocument;
						$table_doc->loadHTML($table_html);
						foreach ($table_doc->childNodes as $node) {
							if ($node->hasChildNodes() && $node->childNodes->length == 1 && $node->firstChild->nodeName === 'body') { 
								$table_node = $node->firstChild->firstChild; // <table>
								foreach ($table_node->childNodes as $table_part) { // <thead>, <tfoot> and <tbody>
									echo $table_part->nodeName . ": ";
									foreach ($table_part->childNodes as $table_row) { // <tr>
										echo $table_row->childNodes->length . "<br>";
										foreach ($table_row->childNodes as $table_data) {
											echo $table_data->nodeValue; // <td>, <th>
											echo '(' . $table_data->getAttribute('colspan') . '), ';
											$cols += ($table_data->getAttribute('colspan') ? intval($table_data->getAttribute('colspan')) : 1);
										}
										echo '<br>';
										echo 'columns: ' . $cols . '<br>';
										$cols = 0;
									}
								}
							}
						}
						die();
					}

					// handle lists
					if (strpos($para, '<ul') !== FALSE) {
						$list_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('ul');
						do {
							$paragraph_ctr++;
							$para = $post_content_paragraphs[$paragraph_ctr];

							if (strpos($para, '</ul') === FALSE) {
								$list_item_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('li', strip_tags($para, '<em><strong><i><b>'));
								$list_element->appendChild($list_item_element);
							}

						} while (strpos($para, '</ul') === FALSE);
						$post_content->appendChild($list_element);
						continue;
					}

					$last_paragraph = Wordpress_Indesign_Exchange_Admin::$dom->createElement('p', apply_filters('the_content', $para, $p->ID));

					// handle galleries
					if (Wordpress_Indesign_Exchange_Admin::$gallery_found === true) {
						$gallery_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('gallery');
						foreach (Wordpress_Indesign_Exchange_Admin::$files as $f) {
							$image_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('image');
							$image_href_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('href');
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

			Wordpress_Indesign_Exchange_Admin::$dom->appendChild($root);

			// echo Wordpress_Indesign_Exchange_Admin::$dom->saveXML();
			// exit();

			$post_content_replaced = Wordpress_Indesign_Exchange_Admin::$dom->saveXML();
			$post_content_replaced = str_replace('&lt;', '<', $post_content_replaced);
			$post_content_replaced = str_replace('&gt;', '>', $post_content_replaced);

			$zip->addFromString($requirement['filename'] . '.xml', $post_content_replaced);
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
