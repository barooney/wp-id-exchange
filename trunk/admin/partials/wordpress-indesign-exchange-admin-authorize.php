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

$dropbox = new Wordpress_Indesign_Exchange_Dropbox();
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="wordpress-indesign-exchange-options" class="wrap">
	<h2>WordPress InDesign Exchange Options</h2>

	<h3>Dropbox Connect Finish</h3>

	<?php
	$credentials = $dropbox->get_access_token( $_GET );
	if ( is_array( $credentials ) ) :
	?>
		<p>You successfully set up your Dropbox account to work with WordPress InDesign Exchange on <a href="<?php echo esc_attr( home_url() ); ?>"><?php echo bloginfo( 'title' ); ?></a></p>

	<?php else: ?>
		<p>Something went awfully wrong. Please try again.</p>
	<?php endif; ?>
	<p><a href="<?php echo esc_attr( admin_url() . 'options-general.php?page=wp-id-exchange/admin/partials/wordpress-indesign-exchange-admin-options.php' ); ?>">Go back</a></p>

	
</div>