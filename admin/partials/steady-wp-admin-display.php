<?php

// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

// show error/update messages
settings_errors( $attributes['settings_slug'] );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post" class="steady-form">
		<?php
		// $option_group: A settings group name. This should match the group name used in register_setting().
		settings_fields( $attributes['steady_wp'] );

		if ( Steady_WP_Settings::is_connected() ) {
			// is connected

			$campaign_url      = get_option( $attributes['settings_slug'] . '_connect_campaign_url' );
			$publication_title = get_option( $attributes['settings_slug'] . '_connect_publication_title' );
			?>
		<div class="steady-form__msg steady-form__msg--success">
			<h2>
				<span class="steady-form__icon dashicons dashicons-yes"></span>
				<?php
				// translators: contains the campaign url and the application title
				echo wp_kses_post( sprintf( __( '<a href="%s" href="_blank">%s</a> is connected to Steady.', 'steady-wp' ), $campaign_url, $publication_title ) );
				?>
			</h2>
		</div>
		<input type="hidden" name="disconnect_from_steady" value="1">
			<?php
			submit_button( __( 'Disconnect from Steady', 'steady-wp' ) );

		} else {
			// is not connected

			// $page: The slug name of the page whose settings sections you want to output.
			// This should match the page name used in add_settings_section().
			do_settings_sections( $attributes['settings_page_slug'] );
			submit_button( __( 'Connect', 'steady-wp' ) );
		}
		?>
	</form>
</div>
