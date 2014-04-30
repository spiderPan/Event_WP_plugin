<?php
include 'lib/MailChimp.class.php';

$MailChimp = new \Drewm\MailChimp('e0504477c7e6147afb13aad8a5ffeebf-us8');
## Add Segment

$listID = '2f5a809940';
$new_segment_id = 11333;
$segment_name = 'TREA_Event_Segment_testID';

/*$segment_id = $MailChimp->call(
	'lists/segments', array(
		              'id'   => $listID,
		              'type' => 'saved'
	              )
);
echo var_dump($segment_id);
*/
/*echo var_dump($groups);
exit;
$new_segment  = $MailChimp->call(
	'lists/segment-add', array(
		                   'id'   => $listID,
		                   'opts' => array(
			                   'type'         => 'saved',
			                   'name'         => $segment_name,
			                   'segment_opts' => array(
				                   'match'      => 'all',
				                   'conditions' => array(
					                   array(
						                   'field' => 'interests-' . $new_group['id'],
						                   'op'    => 'all',
						                   'value' => $new_group['group_name']
					                   )

				                   )
			                   )
		                   )
	                   )
);
*/
$create_campaign = $MailChimp->call(
	'campaigns/create', array(
		                  'type'         => 'plaintext',
		                  'options'      => array(
			                  'list_id'    => $listID,
			                  'subject'    => 'TREA Event Reminder',
			                  'from_email' => 'pan@inspiratica.ca',
			                  'from_name'  => 'TREA'
		                  ),
		                  'content'      => array(
			                  'text' => 'test email'
		                  ),
		                  'segment_opts' => array(
			                  'saved_segment_id' => 11349
		                  )

	                  )
);

echo var_dump($create_campaign);
exit;
/*
 * #$group_id = 8821;
	$group_name = 'new_group_added_by_api';
	$add_segment = $MailChimp -> call('lists/segment-add', array(
				'id'                => '2f5a809940',
				'opts'=>array(
					'type'=>'saved',
					'name'=> 'test_seg_get_from_group',
					'segment_opts'=>array(
						'match'=>'all',
						'conditions'=>array(
							array(
								'field'=>'interests-'.$group_id,
								'op'=>'all',
								'value'=>$group_name
							)
							
						)
					)
				)
		));
	echo var_dump($add_segment);

*/
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

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>MailChimp Test</title>
</head>

<body>
<?php

/*if($retrived_emails){
	echo '<ul>';
	foreach($retrived_emails as $email){
		echo '<li>'.$email['email'].'</li>';
	}
	echo '</ul>';
}*/
?>
<form method="post">
	Email<input type="email" name="email"><br>
	First Name<input type="text" name="fname"><br>
	Last Name<input type="text" name="lname"><br>
	Event<input type="text" name="event"><br>
	<input type="submit" value="submit">
</form>
</body>
</html>