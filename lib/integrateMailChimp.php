<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Studio-4
 * Date: 9/18/13
 * Time: 4:03 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('MailChimp.class.php');


class IntegrateMailChimp extends \Drewm\MailChimp {
	private $listID = "2f5a809940";
	private $apiKey = "e0504477c7e6147afb13aad8a5ffeebf-us8";
	private $MailChimp = null;
	private $grouping_id = null;

	private $eventID = null;

	function __construct() {
		if (!$this->MailChimp instanceof \Drewm\MailChimp) {
			return $this->MailChimp = new \Drewm\MailChimp($this->apiKey);
		} else {
			return $this->MailChimp;
		}
	}

	public function create_campaign($segment_id, $campaign_settings) {
		$subject       = $campaign_settings['event'];
		$content       = $campaign_settings['email_content'];
		$schedule_date = $campaign_settings['email_data'] . $campaign_settings['email_time'];
		$campaign      = $this->MailChimp->call(
			'campaigns/create', array(
				                  'type'         => 'plaintext',
				                  'options'      => array(
					                  'list_id'    => $this->listID,
					                  'subject'    => $subject,
					                  'from_email' => 'pan@inspiratica.ca',
					                  'from_name'  => 'TREA'
				                  ),
				                  'content'      => array(
					                  'text' => $content
				                  ),
				                  'segment_opts' => array(
					                  'saved_segment_id' => $segment_id
				                  )

			                  )
		);

		return $campaign['id'];
	}

	public function check_segment($post_id) {


		$new_segment = $this->create_segment($this->create_group($post_id), $post_id);

		return $new_segment['id'];
	}


	private function create_group($post_id) {
		$groups_name = 'TREA_Events_' . $post_id;
		$group_name  = 'Event_' . $post_id;


		$new_grouping         = $this->MailChimp->call(
			'lists/interest-grouping-add', array(
				                             'id'     => $this->listID,
				                             'name'   => $groups_name,
				                             'type'   => 'checkboxes',
				                             'groups' => array(
					                             'name' => $group_name
				                             )
			                             )
		);
		$new_grouping['name'] = $group_name;

		return $new_grouping;
	}

	private function create_segment($new_group, $post_id) {
		$segment_name = 'TREA_Event_Segment_' . $post_id;
		$new_segment  = $this->MailChimp->call(
			'lists/segment-add', array(
				                   'id'   => $this->listID,
				                   'opts' => array(
					                   'type'         => 'saved',
					                   'name'         => $segment_name,
					                   'segment_opts' => array(
						                   'match'      => 'all',
						                   'conditions' => array(
							                   array(
								                   'field' => 'interests-' . $new_group['id'],
								                   'op'    => 'all',
								                   'value' => $new_group['name']
							                   )

						                   )
					                   )
				                   )
			                   )
		);

		return $new_segment;
	}

	public function subscribe() {
		return $this->MailChimp->call(
			'lists/subscribe', array(
				                 'id'                => $this->listID,
				                 'email'             => array('email' => 'pan@testinspi.com'),
				                 'merge_vars'        => array(
					                 'groupings' => array(
						                 'id'     => $this->eventID,
						                 'groups' => array('event_1')
					                 )
				                 ),
				                 'double_optin'      => false,
				                 'update_existing'   => true,
				                 'replace_interests' => false,
				                 'send_welcome'      => false,
			                 )
		);
	}

}




