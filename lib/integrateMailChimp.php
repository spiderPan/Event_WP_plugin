<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Studio-4
 * Date: 9/18/13
 * Time: 4:03 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('mailChimp/MailChimp.class.php');

$email = $_POST['email'];
$fname  = $_POST['fname'];
$lname  = $_POST['lname'];


$MailChimp = new \Drewm\MailChimp('53487d33b473f5e22d832571629adb6d-us6');
$list_id   = "7020b31c98";

#echo var_dump( $MailChimp->call( 'lists/list' ) );
#exit;
$retval = $MailChimp->call(
	'lists/subscribe', array(
		                 'id'                => $list_id,
		                 'email'             => array('email' => $email),
		                 'merge_vars'        => array('FNAME' => $fname, 'LNAME' => $lname),
		                 'double_optin'      => false,
		                 'update_existing'   => true,
		                 'replace_interests' => false,
		                 'send_welcome'      => false
	                 )
);




