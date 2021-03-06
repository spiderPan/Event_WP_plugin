<?php
/*
Plugin Name: MailChimp Event
Plugin URI: http://inspiratica.ca/
Description: Inspiratica Forms Plugin
Author: Inspiratica
Version: 1.0
Author URI: http://inspiratica.ca/
*/

/************* Post Type inspi_event ********************/
add_action('init', 'mailchimp_event_init');

function mailchimp_event_init() {
	register_post_type(
		'inspi_event',
		array(
			'labels' => array(
				'name'          => 'Events',
				'singular_name' => 'Event'
			),
			'public' => true
		)
	);

	wp_enqueue_script('jquery-time', plugins_url('js/jquery.timepicker.min.js', __FILE__), array('jquery'));
	wp_enqueue_script('jquery-date', plugins_url('js/jquery-ui-1.10.4.custom.min.js', __FILE__), array('jquery'));
	wp_enqueue_style('jquery-date', plugins_url('css/jquery-ui-1.10.4.custom.min.css', __FILE__));
	wp_enqueue_style('jquery-time', plugins_url('css/jquery.timepicker.css', __FILE__));
}

/************* Metabox inspi_event ********************/
function get_mailchimp_fields() {
	return array(
		'segment_id'    => array(
			'name'     => 'Group',
			'type'     => 'hidden',
			'required' => false
		),
		'campaign_id'   => array(
			'name'     => 'campaign',
			'type'     => 'hidden',
			'required' => false
		),
		'event'         => array(
			'name'     => 'Event',
			'type'     => 'text',
			'required' => true
		),

		'event_date'    => array(
			'name'     => 'Event Date',
			'type'     => 'date',
			'required' => true
		),
		'event_time'    => array(
			'name'     => 'Event Time',
			'type'     => 'time',
			'required' => true
		),
		'email_date'    => array(
			'name'     => 'Email Date',
			'type'     => 'date',
			'required' => true
		),
		'email_time'    => array(
			'name'     => 'Email Time',
			'type'     => 'time',
			'required' => true
		),
		'email_content' => array(
			'name'     => 'Email Content',
			'type'     => 'textarea',
			'required' => true
		)
	);
}

function get_campaign_para() {
	return array(
		'event'         => '',
		'email_date'    => '',
		'email_time'    => '',
		'email_content' => ''
	);
}

add_action('admin_init', 'mailchimp_metabox');

function mailchimp_metabox() {
	add_meta_box('mailchimp_meta', 'Event Information', 'mailchimp_metabox_callback', 'inspi_event', 'normal');
}

function mailchimp_metabox_callback($post) {
	wp_nonce_field(plugin_basename(__FILE__), 'inspi_mailchimp_event_meta_box_nonce');

	$mailchimp_fields = get_mailchimp_fields();
	?>
	<dl>
		<?php
		foreach ($mailchimp_fields as $key => $info) :?>

			<?php $value = get_post_meta($post->ID, 'inspi-' . $key, true);
			if ($info['type'] !== 'hidden'):
				?>
				<dt><label for="inspi-<?php echo $key; ?>"><?php echo $info['name']; ?></label></dt>
			<?php endif; ?>
			<dd>
				<?php switch ($info['type']):
				case 'textarea':
					?>
					<textarea id="inspi-<?php echo $key; ?>" name="inspi-<?php echo $key; ?>" rows="5" cols="100" <?php echo $info['required'] ? 'required="required"' : ''; ?>><?php echo $value; ?></textarea>
				<?php break;
				case 'hidden':
				?>
				<input type="hidden" id="inspi-<?php echo $key; ?>" name="inspi-<?php echo $key; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />

				<?php break;
				case 'text':
				?>
				<input type="text" id="inspi-<?php echo $key; ?>" name="inspi-<?php echo $key; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />
				<?php break;
				case 'date':
				?>
				<input type="text" id="inspi-<?php echo $key; ?>" name="inspi-<?php echo $key; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />
					<script>jQuery("#inspi-<?php echo $key; ?>").datepicker();</script>
				<?php break;
				case 'time':
				?>
				<input type="text" id="inspi-<?php echo $key; ?>" name="inspi-<?php echo $key; ?>" value="<?php echo $value; ?>" <?php echo $info['required'] ? 'required="required"' : ''; ?> />
					<script>jQuery("#inspi-<?php echo $key; ?>").timepicker();</script>
					<?php break;

				endswitch;?>

			</dd>
		<?php endforeach; ?>
	</dl>
<?php

}

function inspi_mailchimp_event_plugin_save_postdata($post_id) {
	if (!isset($_POST['inspi_mailchimp_event_meta_box_nonce']) || !wp_verify_nonce($_POST['inspi_mailchimp_event_meta_box_nonce'], plugin_basename(__FILE__))) {
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

	$fields          = get_mailchimp_fields();
	$campaign_fields = get_campaign_para();

	foreach ($fields as $fieldId => $info) {

		$value = $_POST['inspi-' . $fieldId];
		if (array_key_exists($fieldId, $campaign_fields)) {
			$campaign_fields[$fieldId] = $value;
		}
		update_post_meta($post_id, 'inspi-' . $fieldId, $value);
	}

	/************* Set Up MailChimp ********************/

	include 'lib/IntegrateMailChimp.php';

	$IntergrateMailChimp = new IntegrateMailChimp();
	//Detect Segment;
	$segment_id = $_POST['inspi-segment_id'];
	if (empty($segment_id)) {
		$segment_id = $IntergrateMailChimp->check_segment($post_id);
		update_post_meta($post_id, 'inspi-segment_id', $segment_id);
	}

	//Detect Campaign
	$campaign_id = $_POST['inspi-campaign_id'];
	if (empty($campaign_id)) {
		$campaign_id = $IntergrateMailChimp->create_campaign($segment_id, $campaign_fields);
		update_post_meta($post_id, 'inspi-campaign_id', $campaign_id);
	} else {
		echo var_dump($campaign_fields);
		exit;
	}


	/************* Set Up MailChimp ********************/

	return "";
}

add_action('save_post', 'inspi_mailchimp_event_plugin_save_postdata');