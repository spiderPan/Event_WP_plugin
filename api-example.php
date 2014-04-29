<?php
include 'lib/MailChimp.php';

class IntergrateMailChimp extends \Drewm\MailChimp {
	private $apiKey;
	private $listID;
	private $group_id;
	private $group_name;

	function __construct() {
		return $MailChimp = new \Drewm\MailChimp($this->apiKey);
	}

	private function set_api($apiKey) {
		$this->apiKey = $apiKey;
	}

	private function get_api() {
		return $this->apiKey;
	}

	private function set_listID($list_id) {
		$this->listID = $listID;
	}

	private function get_listID() {
		return $this->listID;
	}

	public function create_campaign() {

	}

	public function create_segment() {

	}

	public function create_segment() {

	}

	public function create_grouping() {

	}

	public function add_to_list() {

	}

}


## Add Segment
$group_id = 8821;
$group_name = 'new_group_added_by_api';
$add_segment = $MailChimp->call(
	'lists/segment-add', array(
		                   'id'   => '2f5a809940',
		                   'opts' => array(
			                   'type'         => 'saved',
			                   'name'         => 'test_seg_get_from_group',
			                   'segment_opts' => array(
				                   'match'      => 'all',
				                   'conditions' => array(
					                   array(
						                   'field' => 'interests-' . $group_id,
						                   'op'    => 'all',
						                   'value' => $group_name
					                   )

				                   )
			                   )
		                   )
	                   )
);
echo var_dump($add_segment);
/**/

## Add interest-grouping

/*
$new_group_name = 'new_group_added_by_api';
$new_group = $MailChimp->call('lists/interest-grouping-add', array(
				'id'                => '2f5a809940',
				'name'=>$new_group_name,
				'type'=>'checkboxes',
				'groups'=>array(
					'name'=>$new_group_name
				)
		));
$new_group_id = $new_group['id'];
echo $new_group_id;
*/
## Add subscribe
/*
$event_id = 8821;
$add_subscribe = $MailChimp->call('lists/subscribe', array(
				'id'                => '2f5a809940',
				'email'             => array('email'=>'pan@testinspi.com'),
				'merge_vars'        => array('groupings'=>array(
					'id'=>$event_id,
					'groups'=>array('event_1')
				)),
				'double_optin'      => false,
				'update_existing'   => true,
				'replace_interests' => false,
				'send_welcome'      => false,
		));
*/
#echo var_dump($add_subscribe);
?>
