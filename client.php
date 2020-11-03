<?php
// Information:
// Developer friend, when I wrote this code,
// only God and I knew what we were doing, now only God knows.

//consumo de web service SOAP de facturacion electronica, metdodo de descarga PDF devuelve base64
// se envia resultado como adjunto por email, consumiendo API

require_once 'lib/nusoap.php'; //library

    //parameters received
    $email="example@example.com";//email recipient
    $num_factura="148";//invoice number

    // parameters for WS consumption
    $username = "usuarioDIAN";//user Dian
    $password = "PasswordDIAN";//password Dian
    $prefix="prefijo";//para factura
    $invoice=$num_factura;

    //container
    $response= array();

    //webservice url
    $wsdl="https://ws.facturatech.co/21/index.php?wsdl";


//WS method to download invoice in pdf
function downloadPDFFile($us,$pass,$pre,$fol,$ws){
 $client = new nusoap_client($ws,false);
 $parameters= array('username'=>$us,'password'=>$pass,'prefijo'=>$pre,'folio'=>$fol);
 $result = $client->call("FtechAction.downloadPDFFile",$parameters);

  //results
  $code=$result['code'];
  $succes=$result['success'];
  $results=$result['resourceData'];
  $error=$result['error'];

  //if OK return base64
  if ($code=='201') {
   return $results;
  }
  else{
  	return "";
  }

}
//get base64 document response (execute method)
$pdf=downloadPDFFile($username,$password,$prefix,$num_factura,$wsdl);

  //consume restful api for sending email
  //API URL
$url = 'http://petshowe.dx.am/server.php';

//create a new cURL resource
$ch = curl_init($url);

//setup request to send json via POST
$data = array(
    'email' => $email,
    'fileBase64' => $pdf
);
//encode $date in json
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

//get response from API WS
$obj = json_decode($result);
$res=(array)$obj;

?>
<!-- print response-->
<h1>Respuesta:</h1>
<p><?php echo $res['response']?></p>

