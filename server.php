<?php
    //validate if there is a POST request
    if ($_SERVER['REQUEST_METHOD']=='POST') {
        $dataClient = json_decode(file_get_contents('php://input'), true);
        if (isset($dataClient)) {

             //Codigo de respuesta 200 OK "la solicitud ha tenido exito"
             @header('HTTP/1.1 200 OK');

             //file .pdf
             //decode content of the file that arrives in base64
             $pdf_decoded = base64_decode(@$dataClient['fileBase64']);
             //Write data back to pdf file
             $pdfN = fopen ('Factura.pdf','w');
             fwrite ($pdfN,$pdf_decoded);
             //close output file
             fclose ($pdfN);

             //send email
             //email recipient
             $to = @$dataClient['email'];

             //email sender
             $from = 'corporativo@petshowe.dx.am';
             $fromName = 'M&M';

             //Email subject
             $subject = 'Correo electrónico PHP con datos adjuntos';

             //Attachment path
             $file = "Factura.pdf";

             //Email content
            $htmlContent = '<h1>Factura electronica</h1>
                            <p>Factura enviada desde APIREST</p>';

             //Header for sender information
             $headers = "De: $fromName"." <".$from.">";

             //Limit Email
             $semi_rand = md5(time());
             $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

             //Headers for attachment
             $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

             //multipart limit
             $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

            //attachment preparation
            if(!empty($file)){
                if(is_file($file)){
                    $message .= "--{$mime_boundary}\n";
                    $fp =    @fopen($file,"rb");
                    $data =  @fread($fp,filesize($file));
                    @fclose($fp);

                    $data = chunk_split(base64_encode($data));
                    $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" .
                    "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" .
                    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                }
            }

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $from;

             //Send EMail
             $mail = @mail($to, $subject, $message, $headers, $returnpath);

             // Email delivery status
             // status messages
             $res1 = array('response' => "Mensaje enviado con exito" );
             $res2 = array('response' => "No se pudo enviar mensaje" );

            if($mail){
                echo json_encode($res1);
            }
            else{
                echo json_encode($res2);
            }
             //end send email

        }

        else{
        	// if there is no data, return 404
            echo  @header('HTTP/1.1 404 NOT FOUND');
        }
     }

    ?>