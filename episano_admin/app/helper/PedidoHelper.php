<?php  

use App\models\Pedidos;

	function pedido($orderId = null) {

		if (!is_null($orderId)) {
			
			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/oms/pvt/orders/".$orderId);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	        

	        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
	        $headers = array();
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'X-VTEX-API-AppToken: IKPIFZAHBQNKGQEJKRFTBCRAUCFQBKLOUGJDIGMPVFNPTCWAFIITGROGTPODUKGQEZQAGBAEEQYWPBGRFFLSBSICRDKTSJUKYWLUMWTDHEPPTSQRIWYPZNOLQNJDRHMV';
	        $headers[] = 'X-VTEX-API-AppKey: vtexappkey-pisano-IKPIFZ';

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $server_output = curl_exec ($ch);
	        if (curl_errno($ch)) {
	             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	        } 
	        curl_close($ch); 
	        
	        return json_decode($server_output);

		} else {
			return "No se ha indicado un ID de pedido, por favor indique alguno.";

		}

	}

	function listaPedidos() {

        /*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/oms/pvt/orders?f_Status=ready-for-handling&amp;orderBy=creationDate,desc&amp;per_page=100&amp;page=1");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        

        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-VTEX-API-AppToken: IKPIFZAHBQNKGQEJKRFTBCRAUCFQBKLOUGJDIGMPVFNPTCWAFIITGROGTPODUKGQEZQAGBAEEQYWPBGRFFLSBSICRDKTSJUKYWLUMWTDHEPPTSQRIWYPZNOLQNJDRHMV';
        $headers[] = 'X-VTEX-API-AppKey: vtexappkey-pisano-IKPIFZ';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec ($ch);
        if (curl_errno($ch)) {
             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        } 

        curl_close($ch); 

        /*Listado de Pedidos*/
        return json_decode($server_output);
       
    }

    function desencriptar_email($email) {
  
    	if (!is_null($email)) {
			
			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "http://conversationtracker.vtex.com.br/api/pvt/emailMapping/?alias=".$email.'&an=pisano');
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	        

	        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
	        $headers = array();
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'X-VTEX-API-AppToken: IKPIFZAHBQNKGQEJKRFTBCRAUCFQBKLOUGJDIGMPVFNPTCWAFIITGROGTPODUKGQEZQAGBAEEQYWPBGRFFLSBSICRDKTSJUKYWLUMWTDHEPPTSQRIWYPZNOLQNJDRHMV';
	        $headers[] = 'X-VTEX-API-AppKey: vtexappkey-pisano-IKPIFZ';

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $server_output = curl_exec ($ch);
	        if (curl_errno($ch)) {
	             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	        } 
	        curl_close($ch); 
	        
	        return json_decode($server_output);

		} else {
			return "No se ha indicado un email, por favor indique alguno.";

		}

    }

    function actualizarEstadoPedido($ordenId = null, $status = null, $info_pedido = null) {

    	if (!is_null($ordenId) && !is_null($status)) {

			$data_string = array();
			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/oms/pvt/orders/".$ordenId."/".$status);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

	        $headers = array();

	        if ($status == "invoice" && !is_null($info_pedido)) {

	        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        	$data_string = $info_pedido;
	        	//$header[] = 'Content-Length: ' . strlen($data_string);
	        }

	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	        

	        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'X-VTEX-API-AppToken: IKPIFZAHBQNKGQEJKRFTBCRAUCFQBKLOUGJDIGMPVFNPTCWAFIITGROGTPODUKGQEZQAGBAEEQYWPBGRFFLSBSICRDKTSJUKYWLUMWTDHEPPTSQRIWYPZNOLQNJDRHMV';
	        $headers[] = 'X-VTEX-API-AppKey: vtexappkey-pisano-IKPIFZ';

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $server_output = curl_exec ($ch);
	        if (curl_errno($ch)) {
	             return curl_error($ch);
	        } 
	        curl_close($ch); 
	        
	        return $server_output;

		} else {
			return "No se ha indicado un ID de pedido o un Estado, por favor indique ambos.";

		}

    }

    /*funcion que envia los datos de envio correspondientes a Integral por edio del FTP en un archivo CSV*/
    function enviarDatosEnvioPedidoIntegral() {

    	$pedidos = Pedidos::getInfoPedidoFacturadoSinTrackingNumberIntegral();
    	if (count($pedidos) > 0) {

    		$datos = array();
	    	foreach ($pedidos as $key => $pedido) {
	    			
			    	$datos[] = array($pedido->nro_orden,
			    							$pedido->nombre_destinatario,
			    	 						"Envio a domicilio",
			    							$pedido->domicilio,
			    							$pedido->ciudad,
			    							$pedido->c_postal,
			    							$pedido->telefono,
			    							"1");
			}

	    	/*$csv_end = "";  
			$csv_sep = "|";  
			$csv_file = "datas.csv";  
			$csv="";  

			
			$csv.= "NUMERO DE PEDIDO DEL CLIENTE".$csv_sep."NOMBRE DEL DESTINATARIO".$csv_sep."MODALIDAD DEL SERVICIO".$csv_sep."DOMICILIO DEL DESTINATARIO".$csv_sep."LOCALIDAD DE DESTINO".$csv_sep."CODIGO POSTAL".$csv_sep."TELEFONO".$csv_end;
	    		
	    	foreach ($pedidos as $key => $pedido) {
	    		
	    		$csv.= $pedido->nro_orden.$csv_sep.$pedido->nombre_destinatario.$csv_sep."Envio a domicilio".$pedido->domicilio.$csv_sep.$pedido->ciudad.$csv_sep.$pedido->c_postal.$csv_sep.$pedido->telefono.$csv_end;
	    	}

	    	header("Content-Description: File Transfer"); 
			header("Content-Type: application/force-download"); 
			header("Content-Disposition: attachment; filename=envios_integral.csv");*/ 


			/*$headers = array();
			$headers[] = "NUMERO DE PEDIDO DEL CLIENTE";
			$headers[] = "NOMBRE DEL DESTINATARIO";
			$headers[] = "MODALIDAD DEL SERVICIO";
			$headers[] = "DOMICILIO DEL DESTINATARIO";
			$headers[] = "LOCALIDAD DE DESTINO";
			$headers[] = "CODIGO POSTAL";
			$headers[] = "TELEFONO";
			$headers[] = "EDOMI";*/

			$fecha_archivo = getdate();
			$fecha_archivo_timeStamp = $fecha_archivo[0];

			$file_name = "envios_integral_".$fecha_archivo_timeStamp.".csv";
			
			$fp = fopen(env('RUTA_INTEGRAL').$file_name, 'a+');

			if ($fp) {

			 	header('Content-Type: text/csv');
			    //header('Content-Disposition: attachment; filename="envios_integral.csv"');
			    header('Pragma: no-cache');
			    header('Expires: 0');
			    //fputcsv($fp, $headers);
			    foreach ($datos as $key => $dato) {
			    	fputcsv($fp, array_values($dato), ';', " ");
			    }
			    
			    fclose($fp);
			    
			    enviarArchivoIntegralFTP($pedidos, $file_name);

	    	}

    	}
    	
    }

    /*Envia un archivo CSV con el listado e informacion de los pedidos de integral Express*/
    function enviarArchivoIntegralFTP($pedidos = array(), $file_name) {
    	
    	# Definimos las variables
		$host="web.integralexpress.com";
		$port=21;
		$user="pisano";
		$password="LEUkFN7L=Am7%7(p";
		$ruta="haciaIPE";

		# Realizamos la conexion con el servidor
		$conn_id=@ftp_connect($host, $port);
		
		if($conn_id)
		{
			# Realizamos el login con nuestro usuario y contraseña
			if(@ftp_login($conn_id,$user,$password))
			{	

				ftp_pasv($conn_id, true);
				# Cambiamos al directorio especificado
				if(@ftp_chdir($conn_id, $ruta))
				{
					# Subimos el fichero
					if(@ftp_put($conn_id, $file_name, env('RUTA_INTEGRAL').$file_name, FTP_BINARY)) {
						echo "Fichero subido correctamente";
						chmod(env('RUTA_INTEGRAL').$file_name, 0777);
						unlink(env('RUTA_INTEGRAL').$file_name);

						Pedidos::actualizarEstadoPedidosEnviadosIntegral($pedidos);

				   } else {
						echo "No ha sido posible subir el fichero";
				   }

				}else
					echo "No existe el directorio especificado";

			}else
				echo "El usuario o la contraseña son incorrectos";
			# Cerramos la conexion ftp
			ftp_close($conn_id);

		} else {
			echo "no se pudo establecer conexion con el servidor FTP";

		}

    }

    /*Valida y va a buscar el tracking number de los pedidos con envio de Integral y que no tengan su numero de guia*/
    function traerTrackingNumberPedidoIntegral($pedidos = array()) {
 		
    	if (count($pedidos) > 0) {

    		foreach ($pedidos as $key => $pedido) {

    			$nro_pedido = $pedido->norden;
    			
    			try {

							$wsdl = "http://soaptracking.integralexpress.com:8082/IPETrackingWS.asmx?wsdl";
							/*Datos de prueba, para ver los de Pisano, revisar los mails*/
							$array_de_datos = array(
					              "token"=> "01adfa3a8dc865ea0f336c6f78a5aac1",
					              "cliente"=>"8270",
					              "id_interno"=>"00044011");

							/*$array_de_datos = array(
					              "token"=> "e2f1e0cb9e8f69a254d2ff3d69d26105",
					              "cliente"=>"8304",
					              "id_interno"=>$nro_pedido
							);*/

							$soap_client = new SoapClient($wsdl);
							$response = $soap_client->BuscarGuiaCorpo($array_de_datos);

							$tracking_number = $response->BuscarGuiaCorpoResult->Guia;

							if(!is_null($tracking_number)) {
								Pedidos::guardarTrackingNumberIntegral($nro_pedido, $tracking_number);

							}

							/*Envia a VTEX la actualizacion de Guia del Pedido correspondiente al mismo y a su factura*/
							actualizarDatosFacturaEnvios($nro_pedido, "integral");

				} catch (Exception $e) {

					$e->getMessage();
					
				}
    		
    		}

    	}

    }

    function traerTrackingNumberPedidoUrbano($pedidos = array()) {
    	
    	foreach ($pedidos as $key => $pedido) {
    		actualizarDatosFacturaEnvios($pedido->norden, "urbano");

    	}

    }

    /*Actualiza la factura de VTEX con datos de envios (tracking number y url)*/
    function  actualizarDatosFacturaEnvios($pedido = null, $tipo_envio = null) {

    	if (!is_null($pedido) && !is_null($tipo_envio)) {
    		$info_pedido = Pedidos::buscarFacturaPedido($pedido);
    		
    		$nro_factura = $info_pedido[0]->nro_factura;
    		$tracking_number = $info_pedido[0]->tracking_number;

    		/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, 'http://pisano.vtexcommercestable.com.br/api/oms/pvt/orders/'.$pedido.'/invoice/'.$nro_factura);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

	        if ($tipo_envio == "integral") {
	        	$data_string = '
	        					{
		        				"trackingNumber":'.'"'.$tracking_number.'"'.',
		        				"trackingUrl": "http://trackingonline.integralexpress.com/tracking_corpo.php?cod=8270&valor='.$pedido.'"'.',
		        				"courier": null,
	        					}
	        				';

	        } elseif ($tipo_envio == "urbano") {
	        	$data_string = '
	        					{
		        				"trackingNumber":'.'"'.$tracking_number.'"'.',
		        				"trackingUrl": "http://desarrollo.urbano.com.ar/webservice/cespecifica/?shi_codigo=2069&cli_codigo='.$pedido.'"'.',
		        				"courier": null,
	        					}
	        				';


	        }

	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	        
	        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
	        $headers = array();
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'X-VTEX-API-AppToken: IKPIFZAHBQNKGQEJKRFTBCRAUCFQBKLOUGJDIGMPVFNPTCWAFIITGROGTPODUKGQEZQAGBAEEQYWPBGRFFLSBSICRDKTSJUKYWLUMWTDHEPPTSQRIWYPZNOLQNJDRHMV';
	        $headers[] = 'X-VTEX-API-AppKey: vtexappkey-pisano-IKPIFZ';

	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	        $server_output = curl_exec ($ch);
	        if (curl_errno($ch)) {
	             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	        } 
	        curl_close($ch); 
	        
	        return json_decode($server_output);

    	}

    }


    function validarDatosDecidirPedido($transactionId, $info_pago_pedido) {
    	//var_dump($transactionId);
    	//var_dump($info_pago_pedido);
    	$monto_pago = $info_pago_pedido->value;
    	
    	//$id_estado_pago = $info_pago_pedido->connectorResponses->IDESTADO;
    	//var_dump($id_estado_pago);die;
    	/*Modo Sandbox*/
    	//$end_point = "https://sandbox.decidir.com/services/t/decidir.net/Operation";
		//$wsdl = "https://sandbox.decidir.com/services/t/decidir.net/Operation?wsdl";


		/*Datos Productivos del WS*/
		//Merchant: "00170117".

		//Descripción: Ecommerce.pisano.com.ar

	 	//URL de Acceso a Webservice:

		//Authorize:
			//Endpoint: https://sps.decidir.com/services/t/1.1/Authorize 
			//Wsdl: https://sps.decidir.com/services/t/1.1/Authorize?wsdl  

		//Operation:
			$end_point =  'https://sps.decidir.com/services/t/1.1/Operation'; 
			$wsdl = 'https://sps.decidir.com/services/t/1.1/Operation?wsdl';

		//"Token": 5HTPLQ6T6SVEU7IS007IB8S1


		$http_header = 'Authorization: PRISMA 5HTPLQ6T6SVEU7IS007IB8S1';

		$headers = array(
		  'http'=>array(
		    'header'=>$http_header)
		);

		$contexto = stream_context_create($headers);

		$array_de_datos = array(
              "IDSITE"=> "00170117",
              "IDTRANSACTIONSIT"=>$transactionId."-0"
		);

		//

		$soap_client = new SoapClient($wsdl, array(
		        'stream_context'=>$contexto,
		              'location'=>$end_point,             
		              ));
var_dump($soap_client);die;
		$rta = $soap_client->Get($array_de_datos);
		var_dump($rta);die;
		if (property_exists($rta, 'Error')) {
			$error_transaccion = 1;
			$result['error'] = 1;
			$result['nro_decidir'] = $transactionId."-0";

			return $result;

		} else {
			$precio_decidir = $rta->Operation->MONTO;
			$estado_decidir = $rta->Operation->IDESTADO;
			$precio_decidir = str_replace('.', "", $precio_decidir);
			$precio_decidir = str_replace(',', "", $precio_decidir);
			
			if ($precio_decidir = $monto_pago && $id_estado_pago == $estado_decidir) {
				$result['error'] = 0;
				$result['nro_decidir'] = $transactionId."-0";

				return $result;

			}

		}
		
    }


    


