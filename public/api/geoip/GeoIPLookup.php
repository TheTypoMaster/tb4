<?php

include("geoip.inc");

$gi = geoip_open("GeoIP.dat",GEOIP_STANDARD);

$country_code = geoip_country_code_by_addr($gi, $_GET["ip"]);
if ($country_code) {
    $result = array('code' => $country_code);
    $status = "success";
    $message = "OK";
} else {
    $result = array('code' => NULL);
    $status = "error";
    $message = "Country not permitted";    
}

$json = array('status' => $status, 'message' => $message, 'result' => $result, 'remote_addr' => $_SERVER['REMOTE_ADDR']);
header("content-type: application/json");
echo json_encode($json);

geoip_close($gi);

exit;

?>
