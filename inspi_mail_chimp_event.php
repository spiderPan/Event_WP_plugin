<?php
/*
Plugin Name: Inspiratica MailChimp Event
Plugin URI: http://inspiratica.ca/
Description: Inspiratica MailChimp Event Plugin
Author: Inspiratica
Version: 1.0
Author URI: http://inspiratica.ca/
*/
date_default_timezone_set('America/New_York');


function inspi_mailchimp_event_init() {

	register_post_type(
		'inspi_event',
		array(
			'labels'              => array(
				'name'          => __('MailChimp Events'),
				'singular_name' => __('MailChimp Event')
			),
			'public'              => true,
			'menu_position'       => 7,
			'exclude_from_search' => true,
			'supports'            => array('title', 'editor')
		)
	);

	wp_enqueue_script('jquery-ui', plugins_url('js/jquery-ui-1.10.4.custom.min.js', __FILE__), array('jquery'));
	wp_enqueue_style('jquery-ui-css', plugins_url('style/jquery-ui-1.10.4.custom.min.css', __FILE__));

}

add_action('init', 'inspi_mailchimp_event_init');


/***** Meta Boxes *****/

function inspi_mailchimp_event_get_fields() {
	return array(
		'event_date'     => array(
			'name'     => 'Event Date *',
			'type'     => 'date',
			'required' => true
		),
		'email_schedule' => array(
			'name'     => 'Email Schedule *',
			'type'     => 'date',
			'required' => true
		),
		'email'          => array(
			'name'     => 'Email Content',
			'type'     => 'textarea',
			'required' => true
		),

	);
}

function inspi_mailchimp_plugin_meta_box() {
	global $post;
	wp_nonce_field(plugin_basename(__FILE__), 'inspi_mailchimp_event_noncename');
	?>
	<dl>
		<?php $fields = inspi_mailchimp_event_get_fields(); ?>
		<?php foreach ($fields as $fieldId => $info) : ?>
			<dt><label for="inspi-<?php echo $fieldId; ?>"><?php echo $info['name']; ?></label></dt>
			<?php $value = get_post_meta($post->ID, 'inspi-' . $fieldId, true); ?>
			<dd>
				<?php if ($info['type'] == 'textarea'): ?>
					<textarea id="inspi-<?php echo $fieldId; ?>" name="inspi-<?php echo $fieldId; ?>" rows="5" cols="100" <?php echo $info['required'] ? 'required="required"' : ''; ?>><?php echo $value; ?></textarea>
				<?php elseif ($info['type'] == 'text'): ?>
				<input type="text" id="inspi-<?php echo $fieldId; ?>" name="inspi-<?php echo $fieldId; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />
				<?php elseif ($info['type'] == 'date'): ?>
				<input type="text" id="inspi-<?php echo $fieldId; ?>" name="inspi-<?php echo $fieldId; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />
					<script>
						jQuery("#inspi-<?php echo $fieldId; ?>").datepicker();
					</script>
				<?php endif; ?>
			</dd>
		<?php endforeach; ?>
	</dl>
<?php
}

function inspi_mailchimp_plugin_add_meta_box() {
	add_meta_box('inspi_event_meta', 'Event Information', 'inspi_mailchimp_plugin_meta_box', 'inspi_event', 'normal', 'default');
}

add_action('admin_init', 'inspi_mailchimp_plugin_add_meta_box', 1);

function inspi_mailchimp_plugin_save_postdata($post_id) {
	if (!isset($_POST['inspi_form_noncename']) || !wp_verify_nonce($_POST['inspi_form_noncename'], plugin_basename(__FILE__))) {
		return $post_id;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} else {
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
	}

	$fields = inspi_form_get_fields();
	foreach ($fields as $fieldId => $info) {

		$value = $_POST['inspi-' . $fieldId];
		update_post_meta($post_id, 'inspi-' . $fieldId, $value);
	}

	return "";
}

add_action('save_post', 'inspi_mailchimp_plugin_save_postdata');