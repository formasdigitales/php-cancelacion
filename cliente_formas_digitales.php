
	<?php 
    class ClienteFormasDigitales{

	public function cancelar($parametros){
		/* conexion al web service */
		$client = new SoapClient('http://dev33.facturacfdi.mx/WSCancelacionService?wsdl', array('trace' => 1));
		$result = $client->Cancelacion_1($parametros);
		echo "<b>Request</b>:<br>" . htmlentities($client->__getLastRequest()) . "\n";
		return $result;
	}

	public function getCertificate($certFile) {
		$cert = file_get_contents($certFile);
		return $cert;
	}
}
?>