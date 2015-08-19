<?php
include_once("xmlParser.php");
define("CODIGO_PROVEEDOR_TIGO", "008");

class BillingTigo {
    var $transaction_id;
    var $numero_celular;
    var $provider;
    var $modelo_celular;
    var $id_contenido;

    function BillingTigo($transaction_id, $numero_celular, $modelo_celular, $id_contenido){
        $this->transaction_id = CODIGO_PROVEEDOR_TIGO . $transaction_id;
        $this->numero_celular = $numero_celular;
        $this->modelo_celular = $modelo_celular;
        $this->id_contenido = $id_contenido;
        $this->provider = CODIGO_PROVEEDOR_TIGO;
    }

    function generarXML(){        
        //$transaction_id_encoded = base64_encode($transaction_id);
        
        $xml = "<VAS>\n";
        $xml .= "<Operacion nombre=\"Solicitud_Descarga\">\n";
        $xml .= "<Parametros>\n";
        $xml .= "<CodTransaccion>" . $this->transaction_id . "</CodTransaccion>\n";
		$xml .= "<MSISDN>" . $this->numero_celular . "</MSISDN>\n";
		$xml .= "<CodProveedor>" . $this->provider . "</CodProveedor>\n";
		$xml .= "<CodDispositivo>" . $this->modelo_celular . "</CodDispositivo>\n";
		$xml .= "<CodContenido>" . $this->id_contenido . "</CodContenido>\n";
	    $xml .= "</Parametros>\n";
        $xml .= "</Operacion>\n";
        $xml .= "</VAS>\n";
        
        $xml = str_replace("\n", "", $xml);
        $fplog = fopen("/var/www/tmp/billing_tigo_co/billing_tco.log","a");
        fwrite($fplog,date("Y-m-d H:i:s")."\n $xml \n \n");
        fclose($fplog);

        return $xml;
    }
        
    function consultar(){                    
        $xml = $this->generarXML();

        $retorno = "";

        // Relacionado a la conexión
        //$host = "localhost";
        //$script = "/netpeople/bic_argentina/generaRespuesta.php";

		$host = "10.0.0.242";
		$port = "10009";

	    $esXML = false; //Cuando se vuelve true, guardamos las lineas del xml

        $output = array();
        // abro la conexión
        $socket = @fsockopen($host, $port, $errno, $errstr);
        
        if ($socket) // Si está abierta...
        {            
			$parametros = "xml=" . $xml;
            fputs($socket, "POST /BillingGateway/DescargasServlet HTTP/1.0\n");
			fputs($socket, "Host: $host\n");
			fputs($socket, "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\n");
            fputs($socket, "Content-length: ".strlen($parametros)."\n\n");
			//fputs($socket, $xml);
            fputs($socket, $parametros);

            while(!feof($socket))
            {
	 	        $linea = fgets($socket); // obtengo el resultado
                $fplog = fopen("/var/www/tmp/billing_tigo_co/billing_tco.log","a");
                fwrite($fplog,date("Y-m-d H:i:s")." | $linea \n");
                fclose($fplog);

		        if(trim($linea) == "<VAS>") {
			        $esXML = true;
		        }
		        if($esXML)  { //Si estamos dentro del XML, vamos guardando sus lineas
                    $output[] = $linea;
		        }
		        if(trim($linea) === "</VAS>") {
			        $esXML = false;
		        }

            }
            fclose($socket);
        } else{
            $fplog = fopen("/var/www/tmp/billing_tigo_co/billing_tco.log","a");
            fwrite($fplog,date("Y-m-d H:i:s")." | Error Socket \n \n");
            fclose($fplog);
        }
        /*
        $output[] = "<VAS>\n";
        $output[] = "<Operacion nombre=\"Respuesta_Descarga\">\n";
        $output[] = "<Parametros>\n";
        $output[] = "    <CodTransaccion>1111</CodTransaccion>\n";
		$output[] = "    <MSISDN>0000000000</MSISDN>\n";
		$output[] = "    <CodProveedor>555</CodProveedor>\n";
		$output[] = "    <EntregaContenido>N</EntregaContenido>\n";
		$output[] = "	 <CodResultado>102</CodResultado>\n";
	    $output[] = "</Parametros>\n";
        $output[] = "</Operacion>\n";
        $output[] = "</VAS>\n";
        */
	    //unset($output[count($output)-1]);
	    $output = implode("\n", $output);
	    
        $xmlParser = new xml2Array();
	    $xmlArray = $xmlParser->parse($output);
        
        $entrega_contenido = $xmlArray[0]['children'][0]['children'][0]['children'][3]['tagData'];
        $cod_resultado = $xmlArray[0]['children'][0]['children'][0]['children'][4]['tagData'];

        if ($entrega_contenido == "Y"){
            echo("Se efectuó el cobro correctamente.");
        } else{
            echo("No se pudo efectar el cobro correctamente.");
            
            $mensaje = $this->retornarMensaje($cod_resultado);
            echo($this->limpiarTexto($mensaje));
        }
    }

    function retornarMensaje($codigo){
        switch(intval($codigo)){
            case 101:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor intente más tarde.";
            break;
            
            case 102:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor intente más tarde.";
            break;
            
            case 103:
                $mensaje_retorno = "Por favor consulte el estado de su cuenta pospago.  Llamar *300.";
            break;
            
            case 202:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor intente más tarde.";
            break;
            
            case 203:
                $mensaje_retorno = "Para realizar descargas debe cargar su móvil.";
            break;
            
            case 204:
                $mensaje_retorno = "Su saldo ha expirado, para realizar descargas debe cargar su móvil.";
            break;
            
            case 205:
                $mensaje_retorno = "Esta línea se encuentra suspendida, no se pueden realizar descargas.";
            break;
            
            case 206:
                $mensaje_retorno = "En este momento el servicio no está activo, marque *300 para más información.";
            break;
            
            case 207:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor llamar al *300 para más información.";
            break;
            
            case 208:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor consulte el estado de su línea.  Llamar *300.";
            break;
            
            case 209:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor consulte el estado de su línea.  Llamar *300.";
            break;
            
            case 305:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor consulte el estado de su línea.  Llamar *300.";
            break;
            
            case 999:
                $mensaje_retorno = "En este momento su descarga no se pudo realizar, por favor intente más tarde.";
            break;
            
            default:
                $mensaje_retorno = "No es posible efectuar el cobro.";
            break;            
        }

        return $mensaje_retorno;
    }

    function limpiarTexto($texto){
         $texto = str_replace("Á","&Aacute;",$texto);
         $texto = str_replace("É","&Eacute;",$texto);
         $texto = str_replace("Í","&Iacute;",$texto);
         $texto = str_replace("Ó","&Oacute;",$texto);
         $texto = str_replace("Ú","&Uacute;",$texto);
         $texto = str_replace("Ñ","&Ntilde",$texto);
         $texto = str_replace("á","&aacute;",$texto);
         $texto = str_replace("é","&eacute;",$texto);
         $texto = str_replace("í","&iacute;",$texto);
         $texto = str_replace("ó","&oacute;",$texto);
         $texto = str_replace("ú","&uacute;",$texto);
         $texto = str_replace("ñ","&ntilde;",$texto);

         return $texto;
    }
    
    function logERROR($texto){
        $path = dirname(__FILE__) . "./billingTigo.log";
        $fp = fopen($path,"a");
        fwrite($fp, date("YmdHis")." | " . $this->de . " | " . $this->mensaje . " | " . $this->operadora . " | " . $texto . "\n");
        fclose($fp);
    }
}
?>