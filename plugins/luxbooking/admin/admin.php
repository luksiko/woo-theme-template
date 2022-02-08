<h1 class="luxbooking_title"><?php esc_html_e('Booking Setting', 'luxbooking') ?></h1>
<?php settings_errors(); ?>
<div class="luxbooking_content">

	<form method="post" action="options.php">
		<?php settings_fields('booking_settings'); ?>
		<?php do_settings_sections('luxbooking_settings'); ?>
		<?php submit_button(); ?>

	</form>
</div>
