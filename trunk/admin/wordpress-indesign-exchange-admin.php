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
	 * The ZIP archive, which is downloaded later on.
	 *
	 * @since	 1.0.1
	 * @access 	 public
	 * @var 	 ZIPArchive	$zip 	The ZIP Archive, which is downloaded later on.
	 */
	public static $zip;

	/**
	 * The configuration settings needed for the export
	 *
	 * @since	 1.0.1
	 * @access 	 public
	 * @var 	 array	$requirement 	The configuration settings needed for the export
	 */
	public static $requirement;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $Wordpress_Indesign_Exchange       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $Wordpress_Indesign_Exchange, $version ) {

		do_action( 'wpidex_preinit' );
		$this->Wordpress_Indesign_Exchange = $Wordpress_Indesign_Exchange;
		$this->version = $version;
		$this->zip = new ZIPArchive();
		Wordpress_Indesign_Exchange_Admin::$files = array();
		Wordpress_Indesign_Exchange_Admin::$gallery_found = false;

		do_action( 'wpidex_postinit' );
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
		require_once( ABSPATH . '/wp-settings.php' );
		require_once( ABSPATH . '/wp-load.php' );
		require_once( ABSPATH . WPINC . '/load.php' );
		wp_load_translations_early();
		global $pagenow, $wp_locale, $wp_rewrite;
		$wp_rewrite = new WP_Rewrite();

		if ( $pagenow === 'tools.php' && isset( $_GET['indesign_download'] ) && $_GET['indesign_download'] === '1' ) {
			// user wants to download xml export file
			$this->requirement = array(
				'filename' => isset( $_GET['filename'] ) ? $_GET['filename'] : 'export.xml',
				'root_element' => isset( $_GET['rootElement'] ) ? $_GET['rootElement'] : 'indesign-import',
				'date_format' => isset( $_GET['dateFormat'] ) ? $_GET['dateFormat'] : 'd.m.Y',
				'include' => isset( $_GET['include'] ) ? $_GET['include'] : '',
				'category' => isset( $_GET['category'] ) ? $_GET['category'] : '',
			);

			Wordpress_Indesign_Exchange_Admin::$dom = new DOMDocument( "1.0", "UTF-8" );
			Wordpress_Indesign_Exchange_Admin::$dom->preserveWhitespace = false;
			Wordpress_Indesign_Exchange_Admin::$dom->formatOutput = false;

			$stylesheet = Wordpress_Indesign_Exchange_Admin::$dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $this->requirement['filename'] . '.xslt"');
			Wordpress_Indesign_Exchange_Admin::$dom->appendChild($stylesheet);

			$root = Wordpress_Indesign_Exchange_Admin::$dom->createElement($this->requirement['root_element']);

			$args = array(
				'posts_per_page'   => 0,
				'post_type'        => array( 'post', 'page' ),
				'orderby'          => 'post_date',
				'order'            => 'DESC',
				'category'         => $this->requirement['category'],
				'include'          => $this->requirement['include'],
				'suppress_filters' => true,
			);
			$args = apply_filters( 'wpidex_change_query_args', $args );
			$posts = get_posts( $args );
			//var_dump($posts);

			$filename = tempnam( '/tmp/', 'wp-id-exchange-' );
			if ( $this->zip->open( $filename, ZipArchive::CREATE ) !== TRUE ) {
				exit( "cannot open <$filename>\n" );
			}


			// var_dump($args);

			// iterate through the posts
			foreach($posts as $p) {
				$xml_post = apply_filters( 'add_id_ex_post', $p, Wordpress_Indesign_Exchange_Admin::$dom );
				// var_dump($xml_post);
				if ( null !== $xml_post ) {
					$root->appendChild( $xml_post );
				}
			}
			// die();

			Wordpress_Indesign_Exchange_Admin::$dom->appendChild($root);

			$post_content_replaced = Wordpress_Indesign_Exchange_Admin::$dom->saveXML();
			$post_content_replaced = str_replace( '&lt;', '<', $post_content_replaced );
			$post_content_replaced = str_replace( '&gt;', '>', $post_content_replaced );
			$post_content_replaced = str_replace( '&#13;', '', $post_content_replaced );
			$post_content_replaced = str_replace( '<p></p>', '', $post_content_replaced );

			$this->zip->addFromString( $this->requirement['filename'] . '.xml', $post_content_replaced );
			$this->zip->addFile( plugin_dir_path( __FILE__ ) . 'partials/export.xslt', $this->requirement['filename'] . '.xslt' );
			$this->zip->close();

			header( 'Content-type: application/zip' );
			header( 'Content-Disposition: attachment; filename=' . $this->requirement['filename'] . '.zip' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			echo file_get_contents( $filename );
			exit();
		}
	}

	public function add_post_to_xml( $p ) {
		if ( 'WP_Post' !== get_class( $p ) ) {
			return $p;
		}
		$xml_post = Wordpress_Indesign_Exchange_Admin::$dom->createElement($p->post_type);
		$xml_post->setAttribute('id', $p->ID);
		$xml_post->setAttribute('name', $p->post_name);

		$thumbnail = get_post_thumbnail_id($p->ID);
		if ($thumbnail) {
			$f = get_attached_file(get_post_thumbnail_id($p->ID));
			$post_thumbnail_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_thumbnail');
			$post_thumbnail_href_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('href');
			$post_thumbnail_href_attribute->value = 'file://attachments/' . basename($f);
			$post_thumbnail_element->appendChild($post_thumbnail_href_attribute);
			$xml_post->appendChild($post_thumbnail_element);

			$this->zip->addFile($f, '/attachments/' . basename($f));
		}

		$post_title = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_title', $p->post_title);
		$xml_post->appendChild($post_title);
		$post_date = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_date', date_create($p->post_date)->format($this->requirement['date_format']));
		$xml_post->appendChild($post_date);

		$post_author = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_author', get_the_author_meta('display_name', $p->post_author));
		$xml_post->appendChild($post_author);

		if ('' !== $p->post_excerpt) {
			$post_excerpt = Wordpress_Indesign_Exchange_Admin::$dom->createElement('post_excerpt', trim($p->post_excerpt));
			$xml_post->appendChild($post_excerpt);
		}

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
			$para = trim( $post_content_paragraphs[$paragraph_ctr] );

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

				$table_struct = array();

				$max_cols = 0;
				$table_doc = new DOMDocument;
				$table_doc->loadHTML($table_html);
				foreach ($table_doc->childNodes as $node) {
					if ($node->hasChildNodes() && $node->childNodes->length == 1 && $node->firstChild->nodeName === 'body') {
						$table_node = $node->firstChild->firstChild; // <table>
						foreach ($table_node->childNodes as $table_part) { // <thead>, <tfoot> and <tbody>
							$table_struct[$table_part->nodeName] = array();
							foreach ($table_part->childNodes as $table_row) { // <tr>
								$row = array();
								foreach ($table_row->childNodes as $table_data) {
									$cell = array();
									switch (get_class($table_data)) {
										case 'DOMElement':
											if ($table_data->nodeName === 'td' || $table_data->nodeName === 'th') {
												$cell['_value'] = $table_data->nodeValue;
												$cell['colspan'] = intval($table_data->getAttribute('colspan') ? $table_data->getAttribute('colspan') : '1');
												$cell['rowspan'] = intval($table_data->getAttribute('rowspan') ? $table_data->getAttribute('rowspan') : '1');
												$row[] = $cell;
											}
											break;
									}
									$cols = 0;
									foreach ($row as $c) {
										$cols += $c['colspan'];
									}
									$max_cols = max($max_cols, $cols);
								}
								$table_struct[$table_part->nodeName][] = $row;
							}
						}
					}
				}

				$table_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('table');
				$table_frame_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('frame');
				$table_frame_attribute->value = 'all';
				$table_element->appendChild($table_frame_attribute);

				$tgroup_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('tgroup');
				$tgroup_cols_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('cols');
				$tgroup_cols_attribute->value = $max_cols;
				$tgroup_element->appendChild($tgroup_cols_attribute);

				for ($i = 0; $i < $max_cols; $i++) {
					$colspec_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('colspec');
					$colspec_colname_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('colname');
					$colspec_colname_attribute->value = 'c' . ($i + 1);
					$colspec_element->appendChild($colspec_colname_attribute);

					$colspec_colwidth_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('colwidth');
					$colspec_colwidth_attribute->value = '261.1377952755pt';
					$colspec_element->appendChild($colspec_colwidth_attribute);

					$tgroup_element->appendChild($colspec_element);
				}

				foreach ($table_struct as $t_key => $t_val) {
					$t_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement($t_key);

					foreach ($t_val as $row) {
						$t_row = Wordpress_Indesign_Exchange_Admin::$dom->createElement('row');

						$c = 1;
						foreach ($row as $cell) {
							$entry_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('entry');

							$entry_align_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('align');
							$entry_align_attribute->value = 'left';
							$entry_element->appendChild($entry_align_attribute);

							$entry_valign_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('valign');
							$entry_valign_attribute->value = 'top';
							$entry_element->appendChild($entry_valign_attribute);

							if ($cell['colspan'] > 1) {
								$entry_namest_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('namest');
								$entry_namest_attribute->value = 'c' . $c;
								$entry_element->appendChild($entry_namest_attribute);

								$entry_nameend_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('nameend');
								$entry_nameend_attribute->value = 'c' . ($cell['colspan'] - $c + 1);
								$entry_element->appendChild($entry_nameend_attribute);
							}

							if ($cell['rowspan'] > 1) {
								$entry_morerows_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('morerows');
								$entry_morerows_attribute->value = ($cell['rowspan'] - 1);
								$entry_element->appendChild($entry_morerows_attribute);
							}

							$entry_element->nodeValue = $cell['_value'];

							$t_row->appendChild($entry_element);

							$c++;
						}
						$t_element->appendChild($t_row);
					}
					$tgroup_element->appendChild($t_element);
				}

				$table_element->appendChild($tgroup_element);
				$post_content->appendChild($table_element);

				continue;
			}

			// handle lists
			// handles <ol> lists correctly since 1.0.1
			$ol = strpos($para, '<ol') !== FALSE;
			$ul = strpos($para, '<ul') !== FALSE;

			if ($ol || $ul) {
				$list_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement( $ol ? 'ol' : 'ul' );
				do {
					$paragraph_ctr++;
					$para = $post_content_paragraphs[$paragraph_ctr];

					$ol_end = strpos($para, '</ol') === FALSE;
					$ul_end = strpos($para, '</ul') === FALSE;
					if ($ol_end && $ul_end) {
						$list_item_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('li', strip_tags($para, '<em><strong><i><b>'));
						$list_element->appendChild($list_item_element);
					}

				} while ($ol_end && $ul_end);
				$post_content->appendChild($list_element);
				continue;
			}

			$last_paragraph = Wordpress_Indesign_Exchange_Admin::$dom->createElement('p', apply_filters('the_content', $para, $p->ID));

			// handle images
			if (strpos($para, '<img') !== FALSE) {
				$img_doc = new DOMDocument;
				$img_doc->loadHTML($para);

				$src = $img_doc->lastChild->lastChild->lastChild->getAttribute('src');

				$f = get_attached_file($this->_get_image_id_from_url($src));

				$image_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('image');
				$image_href_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('href');
				$image_href_attribute->value = 'file://attachments/' . basename($f);
				$image_element->appendChild($image_href_attribute);

				$this->zip->addFile($f, '/attachments/' . basename($f));
			}

			// handle galleries
			if (Wordpress_Indesign_Exchange_Admin::$gallery_found === true) {
				$gallery_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('gallery');
				foreach (Wordpress_Indesign_Exchange_Admin::$files as $f) {
					$image_element = Wordpress_Indesign_Exchange_Admin::$dom->createElement('image');
					$image_href_attribute = Wordpress_Indesign_Exchange_Admin::$dom->createAttribute('href');
					$image_href_attribute->value = 'file://attachments/' . basename($f);
					$image_element->appendChild($image_href_attribute);

					$this->zip->addFile($f, '/attachments/' . basename($f));

					$gallery_element->appendChild($image_element);
				}
				$last_paragraph->appendChild($gallery_element);
				Wordpress_Indesign_Exchange_Admin::$files = array();
				Wordpress_Indesign_Exchange_Admin::$gallery_found = false;
			}
			$post_content->appendChild($last_paragraph);
		}
		$xml_post->appendChild($post_content);

		return $xml_post;
	}

	public static function export_gallery_shortcode($atts = array(), $content) {
		if (!isset($atts['ids'])) return;
		$ids = explode(',', $atts['ids']);
		foreach ($ids as $attachment) {
			Wordpress_Indesign_Exchange_Admin::$files[] = get_attached_file($attachment);
		}
		Wordpress_Indesign_Exchange_Admin::$gallery_found = true;
	}

	private function _get_image_id_from_url( $attachment_url = '' ) {

		global $wpdb;
		$attachment_id = false;

		// If there is no url, return.
		if ( '' == $attachment_url ) {
			return;
		}

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}
}
