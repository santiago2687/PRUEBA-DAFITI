<?php
	
	function autenticacionEmblue() {

		try {

				/*Inicio curl*/
		        $ch = curl_init();

		        /*Estructura del xml SOAP*/
		        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
	   				<soapenv:Header/>
	   				<soapenv:Body>
				      <tem:StockKeepingUnitActive>
				         <!--Optional:-->
				         <tem:idStockKeepingUnit>'.$sku_product.'</tem:idStockKeepingUnit>
				      </tem:StockKeepingUnitActive>
	   				</soapenv:Body>
				</soapenv:Envelope>';

		        /*Seteo las opciones de curl basicas*/
		        curl_setopt($ch,CURLOPT_URL, "http://webservice-pisano.vtexcommerce.com.br/service.svc?wsdl");
		        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($ch, CURLOPT_POST, true );
	    		curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_request);
	    		
		        //$authorization = base64_encode("sbermudez:sbermudez");

		        $soapUser = "sbermudez";
		        $soapPassword = "sbermudez";

		        curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword);

		        /*Armo el header con la info para conectarse correctamente a la API de VTEX*/
		        $headers = array(
	        		"Content-type: text/xml;charset=\"utf-8\"",
	        		"Accept: text/xml",
			        "Cache-Control: no-cache",
			        "Pragma: no-cache",
			        "SOAPAction: http://tempuri.org/IService/StockKeepingUnitActive",
			        "Content-length: ".strlen($soap_request),
	    		);

		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		        $server_output = curl_exec ($ch);
		        
		        if (curl_errno($ch)) {
		             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		        } 

		        curl_close($ch);

			} catch (Exception $e) {

				$e->getMessage();
				
			}
		
	}


?>