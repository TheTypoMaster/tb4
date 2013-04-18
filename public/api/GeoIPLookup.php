<?php 
// TODO: implement actual GeoIP lookup
/*
 * status: success/error
 * success - return 'code' (country code?)
 * error - return 'message'
 */
$result = array('code' => 'AU');
$json = array('status' => 'success', 'result' => $result);
echo json_encode($json);
exit;
 ?>