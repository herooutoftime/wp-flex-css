<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form action="options.php" method="post" class="clear">
<!--		<div id="col-container">-->
<!--			<div id="col-right">-->
				<?php
				$wp_list_table = new Flex_Css_Admin_Table();
				$wp_list_table->prepare_items();
				$wp_list_table->display();
				submit_button();
				?>
<!--			</div>-->
<!--			<div id="col-left">-->
				<?php
				settings_fields( $this->plugin_name );
				do_settings_sections( $this->plugin_name );
				submit_button();
				?>
<!--			</div>-->
<!--		</div>-->
	</form>
	<textarea name="preview" id="preview" cols="30" rows="10">
body,
button,
input,
select,
textarea {
	/*! @edit: text_color*/
	color: #000000;
	/*! @edit: text_family*/
	font-family: "Courier New", Courier, monospace;
	/*! @edit: text_size*/
	font-size: 18px;
	font-size: 1rem;
	/*! @edit: line_height*/
	line-height:                    1.75;
	font-family: "Courier New", Courier, monospace
}
	</textarea>
	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<input type="hidden" name="action" value="flex_restore">
		<input type="hidden" name="option_page" value="flex-css">
		<?php
		wp_nonce_field( 'restore' );
		submit_button( 'Restore original', 'secondary', 'submit' );
		?>
	</form>
</div>