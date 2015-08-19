<?php

ini_set('display_errors', 'off');
//error_reporting(E_ALL ^ E_NOTICE);

require('/var/www/lib/Gbg/Cobro.php');
require('/var/www/lib/Gbg/stdResponse.php');

$response = new Gbg_StdResponse();

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Allow: POST", true, 405);
    $response->setException(Gbg_StdResponse::METHOD_NOT_ALLOWED, 'Method Not Allowed');
    salida($response);
}

$id_operador    = trim($_POST['idop']);   // el id de la operadora (requerido)
$numero_celular = trim($_POST['cel']);    // el número de móvil del cliente (requerido)
$idbc           = trim($_POST['idbc']);   // el id del monto o billing code (requerido)
$id_contenido   = trim($_POST['cont']);   // id_contenido (requerido)
$ref            = trim($_POST['ref']);    // ref (requerido)
$user_agent     = trim($_POST['ua']);     // (requerido)
$servicio       = trim($_POST['serv']);   // descripcion del servicio (opcional)

if(empty($id_operador)) {
    $response->addFieldError('idop', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}
elseif(!is_numeric($id_operador)) {
    $response->addFieldError('idop', Gbg_stdResponse::FIELD_DATATYPE_ERROR);
}
if(empty($numero_celular)) {
    $response->addFieldError('cel', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}
if(empty($idbc)) {
    $response->addFieldError('idbc', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}
if(empty($id_contenido)) {
    $response->addFieldError('cont', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}
if(empty($ref)) {
    $response->addFieldError('ref', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}
if(empty($user_agent)) {
    $response->addFieldError('ua', Gbg_stdResponse::FIELD_REQUIRED_ERROR);
}

try {
    $cobro = new Gbg_Cobro($id_operador, $numero_celular, $idbc, $id_contenido, $user_agent);
} catch (Exception $exc) {
    $response->addFieldError($exc->getMessage(), $exc->getCode());
    salida($response);
}

$resultado = $cobro->comprar($servicio);
if($resultado !== true) {
    $response->setException(101, 'Error');
}
salida($response);


//function salida($success, $code, $message = '') {
function salida($response) {

    $response = $response->getResponse();
    $document = new DOMDocument('1.0', 'utf-8');
    $document->formatOutput = true;
    $root = $document->appendChild($document->createElement('xml'));
        $action = $root->appendChild($document->createElement('action'));
        $action->setAttribute('name', 'charge');
            $result = $action->appendChild($document->createElement('result'));
            if($response->success === true) {
                //$tagName = ($response->success === true) ? 'code' : 'error';
                $result->appendChild($document->createElement('code', 100));
                $result->appendChild($document->createElement('message', 'Billing successfull'));
            }
            else {
                if(isset($response->errors) && count($response->errors)) {
                    $errors = $result->appendChild($document->createElement('errors'));
                    foreach($response->errors AS $k => $v) {
                        $error = $errors->appendChild($document->createElement('error'));
                        $error->appendChild($document->createElement('field', $k));
                        $error->appendChild($document->createElement('code', $v->cod));
                        $error->appendChild($document->createElement('message', $v->description));
                    }
                }
                elseif(isset($response->messages) && count($response->messages)) {
                    $result->appendChild($document->createElement('error', $response->messages[0]->cod));
                    $result->appendChild($document->createElement('message', $response->messages[0]->description));
                }
            }

    $xml = $document->saveXML();

    header("Content-Type: application/xml; charset=UTF-8");
    header("Content-Length: ".strlen($xml));
    exit($xml);
}

?>