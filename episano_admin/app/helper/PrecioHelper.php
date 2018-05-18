<?php 


	/*Carga lista de precios para cada producto dentro del e-commerce*/
	function uploadPriceList() {

		/*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/pricing/pvt/price-sheet");
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
        
        return $server_output;

	}

	/*Consulta la lista de precios de un producto por sku*/
	function consultPriceListBySku($sku = null) {

		if (!is_null($sku)) {

			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "http://pisano.vtexcommercestable.com.br/api/pricing/pvt/price-sheet/".$sku);
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
			return "No se ha indicado un ID de producto, por favor indique alguno.";

    	}

	}

	function updatePriceListBySku($info_update = array()) {
		
		if (!is_null($info_update)) {
			
			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/pricing/pvt/price-sheet");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        
	        $data_string = '[
	        					{
		        				"id":'.$info_update["id"].',
		        				"itemId":'.$info_update["itemId"].',
		        				"salesChannel":'.$info_update["salesChannel"].',
		        				"price":'.$info_update["price"].',
		        				"listPrice":'.$info_update["listPrice"].'
	        					}
	        				]';

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
	        
	        return $server_output;


		} else {
			\Log::info('Hubo un error en los datos para actualizar el producto');
		}

	}
