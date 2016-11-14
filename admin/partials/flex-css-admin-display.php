<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form action="options.php" method="post">
		<?php
		settings_fields( $this->plugin_name );
		do_settings_sections( $this->plugin_name );
		$wp_list_table = new Flex_Css_Admin_Table();
		$wp_list_table->prepare_items();
		$wp_list_table->display();

		submit_button();
		?>
	</form>
	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<input type="hidden" name="action" value="flex_restore">
		<input type="hidden" name="option_page" value="flex-css">
		<?php
		wp_nonce_field( 'restore' );
		submit_button( 'Restore original', 'secondary', 'submit' );
		?>
	</form>
</div>