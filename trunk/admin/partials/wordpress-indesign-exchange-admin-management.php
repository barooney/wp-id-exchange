<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://barooney.de/meine-projekte/wordpress-indesign-exchange/
 * @since      1.0.0
 *
 * @package    Wordpress_Indesign_Exchange
 * @subpackage Wordpress_Indesign_Exchange/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="wordpress-indesign-exchange-management" class="wrap">
	<h2>WordPress InDesign Exchange</h2>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Filename</th>
			<td><input type="text" id="download-indesign-exchange-filename" value="export.xml" placeholder="export.xml"></td>
		</tr>
		<tr valign="top">
			<th scope="row">Root-Element</th>
			<td><input type="text" id="download-indesign-exchange-root-element" value="indesign-export" placeholder="indesign-export"></td>
		</tr>
	</table>
	<a id="download-indesign-exchange-xml" class="button button-primary button-large" href="<?php echo admin_url('tools.php?indesign_download=1'); ?>">Download XML</a>
</div>
<script type="text/javascript">
	var exporturl = '<?php echo admin_url('tools.php'); ?>';
</script>