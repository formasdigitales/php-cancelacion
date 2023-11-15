# PHP Ejemplo de Cancelación
<br/>

El ejemplo presentado se realizo en la versión PHP 8.2.12, habilitando la extension **_extension=soap_**.

La clase **_Cancelacion_** integra todos los metodos, variables y atributos requeridos para la cancelación.

```PHP
    ////////////////////////////////////////////////////////////////////////////////////////////////
    // ATRIBUTO
	public $datos;

	// METODO CONSTRUCTOR
	function __construct(){

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

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // METODO PARA CANCELAR
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
```

# Enviar Solicitud
La clase **_ClienteFormasDigitales_** recibirá los parámetros para la cancelación.


```PHP
    private $wsdlUrl = 'http://dev33.facturacfdi.mx:80/WSCancelacion40Service?wsdl';
		
	public function enviarCancelacion($parametros) {
		try {
			// CONEXION AL SERVICIO WEB SOAP
			$client = new SoapClient($this->wsdlUrl, array('trace' => 1));

			// MANDAMOS A LLAMAR AL METODO DE CANCELACION E INSERTAMOS LOS PARAMETROS
			$result = $client->Cancelacion40_1($parametros);

			// MOSTRAMOS EL REQUEST (ÚTIL PARA DEPURAR)
			echo "<b>Request</b>:<br><p style='word-break:break-all;'>" . htmlentities($client->__getLastRequest()) . "</p>\n";

			return $result;
		} catch (SoapFault $e) {
			// ERRORES SOAP
			echo "Error en la llamada SOAP: " . $e->getMessage();
			return null;
		} catch (Exception $e) {
			// OTROS ERRORES
			echo "Error general: " . $e->getMessage();
			return null;
		}
	}
```

Si los datos requeridos son correctos entonces se imprime el XML con la respuesta, de lo contrario se imprimen los errores relacionados.

