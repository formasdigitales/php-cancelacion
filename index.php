<?php	
require_once('./cliente_formas_digitales.php');
	header ('Content-type: text/html; charset=utf-8');	
	try {
	
		set_time_limit(0);
		date_default_timezone_set("America/Mexico_City");

		// Datos del emisor
		$rfc_emisor = "EWE1709045U0";
		$fecha_actual = substr( date('c'), 0, 19);
		$folios = 'CE1624C7-1317-4C8D-A047-210E043F6F55';
		// Archivos del certificado
		$certFile =  dirname(__FILE__) . "/resources/CSD_EWE1709045U0_20190617_132205s.cer";
		$keyFile =  dirname(__FILE__) . "/resources/CSD_EWE1709045U0_20190617_132205.key";
		$keyPassword =  "12345678a";

		// Inicializamos y pasamos los parametros para hacer el request
		$clienteFD = new ClienteFormasDigitales();
		$parametros = new Parametros();
		$parametros->rfcEmisor = $rfc_emisor;
		$parametros->fecha = $fecha_actual;
		$parametros->folios = $folios;
		$parametros->publicKey = $clienteFD->getCertificate($certFile);
		$parametros->privateKey = $clienteFD->getCertificate($keyFile);
		$parametros->password = $keyPassword;
		// Datos de autenticación del WS
		$autentica = new Autenticar();
		$autentica->usuario = "pruebasWS";
		$autentica->password = "pruebasWS";
		$parametros->accesos = $autentica;
		// Mandamos a cancelar el UUID al WebService
		$responseCancelacion = $clienteFD->cancelar($parametros);


		// En caso de error muestra el código de error y el mensaje de respuesta del servicio
		if(isset($responseCancelacion->return->mensaje)){
			echo "<br><br>Codigo Error: " . $responseCancelacion->return->codEstatus."<br><br>Mensaje: " . $responseCancelacion->return->mensaje. "<br>";
		}

		// En caso de cancelación correcta, guarda el xml en un textarea
		if(isset($responseCancelacion->return->acuse)){
			echo "<br><br>Estatus UUID: " . $responseCancelacion->return->folios->folio->estatusUUID."<br>Mensaje: " . $responseCancelacion->return->folios->folio->mensaje;
			echo '<br>XML timbrado:<br><textarea>' . $responseCancelacion->return->acuse . '</textarea>';
		}

	
	} catch (SoapFault $e) {
		print("Auth Error:::: $e");
	}


	
class Autenticar{
	public $password;
	public $usuario;
}


class Parametros{
	public $rfcEmisor;
	public $fecha;
	public $folios;
	public $publicKey;
	public $privateKey;
	public $password;
	public $accesos;
}
?>