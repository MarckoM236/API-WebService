<?php
// Information:
// Developer friend, when I wrote this code,
// only God and I knew what we were doing, now only God knows.

//Enviar cualquier pdf que este codificado a base 64 (adjunto base64.txt)
    $email="marck_123456@hotmail.com";//email recipient
    $response= array();//content response

    //obtener contenido de txt
function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}
    $filesBase64=get_include_contents('base64.txt');

//consume restful api for sending email
//API URL
$url = 'http://petshowe.dx.am/server.php';

//create a new cURL resource
$ch = curl_init($url);

//setup request to send json via POST
$data = array(
    'email' => $email,
    'fileBase64' => $filesBase64
);
$payload = json_encode($data);

//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute the POST request
$result = curl_exec($ch);

//close cURL resource
curl_close($ch);

//get response from WSREST
$obj = json_decode($result);
$res=(array)$obj;
?>
<!-- Print response-->
<h1>Respuesta:</h1>
<p><?php echo $res['response']?></p>

