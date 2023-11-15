<?php 
    class ClienteFormasDigitales {

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
	}
?>
