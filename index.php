<?php	
require_once "./cliente_formas_digitales.php";
header('Content-type: text/html; charset=utf-8');	

class Cancelacion{

	// ATRIBUTO
	public $datos;

	// METODO CONSTRUCTOR
	function __construct(){

		// ASIGNAMOS LOS DATOS
		$this->datos = [
			"rfcEmisor"=>"EKU9003173C9",
			"fechaCancelacion"=>$this->getDateISO8601(),
			"cerCSD"=>"C:\\Certificados\\CSD_Sucursal_1_EKU9003173C9_20230517_223850.cer",
			"keyCSD"=>"C:\\Certificados\\CSD_Sucursal_1_EKU9003173C9_20230517_223850.key",
			"keyPassCSD"=>"12345678a",
			"userWS"=>"pruebasWS",
			"passWS"=>"pruebasWS"
			
		];
	}

	// METODO PARA RETORNAR EL CERTIFICADO EN BYTE[]
	public function getByte($certFile) {
		return file_get_contents($certFile);
	}
 
	// METODO PARA OBTENER LA FECHA ACTUAL
	public function getDateISO8601(){
		return substr( date('c'), 0, 19);
	}

	//	METODO PARA CANCELAR
	public function solicitudCancelacion(){
		try {
			date_default_timezone_set("America/Mexico_City");
			set_time_limit(0); // OPCIONAL: EJECUTA ESTE SCRIPT SIN LIMITE DE TIEMPO
			
	
			// OBJETO WSFOLIOS
			$wsFolios40 = new stdClass();

			// GENERAMOS LOS FOLIOS A CANCELAR
			$wsFolios40 =  array(
				$this->WSFolios("01","B2E348F2-5163-4CFA-BB23-5A514C8E63D4","969D1264-EE5D-49A4-979B-96521CC82ADB"),
				$this->WSFolios("02","314FEAB4-8555-446D-831F-E0D187BFDA79",""),
				$this->WSFolios("03","12DB9C0D-543A-488A-962E-D25929096249",""),
				$this->WSFolios("04","681CFDCF-8304-4C76-93AF-4C517407142C","")
			);

			// INSTANCIAMOS PARA INGRESAR LOS DATOS AL REQUEST
			$clienteFD = new ClienteFormasDigitales();

			// AUTENTICACION
			$accesos = new Autenticar();
			$accesos->usuario = $this->datos["userWS"];
			$accesos->password = $this->datos["passWS"];

			// AGREGAMOS LOS PARAMETROS
			@$parametros = new Parametros();
			@$parametros->rfcEmisor = $this->datos["rfcEmisor"];
			@$parametros->fecha = $this->datos["fechaCancelacion"];
			@$parametros->folios = $wsFolios40;
			@$parametros->publicKey = $this->getByte( $this->datos["cerCSD"] );
			@$parametros->privateKey = $this->getByte( $this->datos["keyCSD"] );
			@$parametros->password = $this->datos["keyPassCSD"];
			@$parametros->accesos = $accesos;
			
			// MANDAMOS A CANCELAR
			$responseCancelacion = $clienteFD->enviarCancelacion($parametros);

			// MOSTRAMOS LOS MENSAJES DE RESPUESTA
			echo "<b>Response</b>:<br>";

			if(isset($responseCancelacion->return->mensaje))
				echo "Cod. Error: ". $responseCancelacion->return->codEstatus. " Mensaje: " .$responseCancelacion->return->mensaje;
			
			// SE IMPRIME EL XML EN UN TEXTAREA
			if(isset($responseCancelacion->return->acuse)) 
				echo "<textarea style='display:block;width:100%;height:300px'>".$responseCancelacion->return->acuse."</textarea>";
				
		
		} catch (SoapFault $e) {
			print("Auth Error:::: $e");
		}
	}

	// METODO PARA CREAR LOS FOLIOS A CANCELAR
	public function WSFolios($motivo,$uuid,$folioSustitucion){		
		$wsFolio = new stdClass(); // CREAMOS WSFOLIO

		// INSERTAMOS LOS DATOS
		$wsFolio->motivo = $motivo;
		$wsFolio->uuid = $uuid; // Puedes cambiar el valor según tus necesidades
		$wsFolio->folioSustitucion = ($motivo=="01") ? $folioSustitucion : '' ; // Puedes cambiar el valor según tus necesidades
		
		// INSERTAMOS EL FOLIO A FOLIOS Y RETORNAMOS
		$wsFolios40 = new stdClass(); // CREAMOS WSFOLIOS40
		$wsFolios40->folio = $wsFolio;
		return $wsFolios40;
	}
}

// CLASE PARA LOS ACCESOS DEL WEB SERVICE
class Autenticar{
	public $password; // STRING
	public $usuario;  // STRING
}

// PARAMETROS REQUERIDOS PARA LA CANCELACION
class Parametros{
	public $rfcEmisor; 			// STRING
	public $fechaCancelacion;	// STRING
	public $folios;				// LIST
	public $cerCSD;				// BYTE[]
	public $keyCSD;				// BYTE[]
	public $keyPassCSD;			// STRING
	public $accesos;			// CLASE
}

// EJECUTAMOS LA CANCELACION
$cancelacion = new Cancelacion();
$cancelacion->solicitudCancelacion();
?>
