# PHP Ejemplo de Cancelación


<br/>

En PHP tenemos la clase **_ClienteFormasDigitales_** que nos ayuda en el proceso de cancelacion del CFDi.

Inicializamos la clase y pasamos los parámetros para hacer el request

# Ejemplo de cancelación

```PHP
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
```

<br>

Una vez que llenamos los datos requeridos para el request, mandamos a cancelar al WebService.

```PHP
    $responseCancelacion = $clienteFD->cancelar($parametros);
```

La clase **_ClienteFormasDigitales_** recibirá los parámetros para la cancelación.


```PHP
public function cancelar($parametros){
    /* conexion al web service */
    $client = new SoapClient('http://dev33.facturacfdi.mx/WSCancelacionService?wsdl', array('trace' => 1));
    $result = $client->Cancelacion_1($parametros);
    // Mostramos el XML Request enviado al WebService
    echo "<b>Request</b>:<br>" . htmlentities($client->__getLastRequest()) . "\n";
    return $result;
}
```

Si todo salió bien el $responseCancelacion tendrá una variable que se llama acuse.

```PHP
// En caso de cancelación correcta, guarda el xml en un textarea
if(isset($responseCancelacion->return->acuse)){
        echo "<br><br>Estatus UUID: " . $responseCancelacion->return->folios->folio->estatusUUID."<br>Mensaje: " . $responseCancelacion->return->folios->folio->mensaje;
        echo '<br>XML response:<br><textarea>' . $responseCancelacion->return->acuse . '</textarea>';
}
```

