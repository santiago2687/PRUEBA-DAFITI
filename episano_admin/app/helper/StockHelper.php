	<?php 

	/*/Consulta la cantidad de stock para todos los productos del e-commerce*/
	function consultarStock() {

        /*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/logistics/pvt/inventory/warehouseitems/getbalance");
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

	/*Actualiza el stock para X productos. Se puede actualizar 1 solo o la cantidad que se quiera*/
	function actualizarStockProducto($sku, $cantidad, $flag) {
	
        /*Flag: si es 0 => es proceso masivo de actualizacion, si es 1 => corresponde para una venta*/
        if ($flag === 1) {
            $data_string = '{
                        "unlimitedQuantity": false,
                        "dateUtcOnBalanceSystem": null,
                        "quantity":'.(int)$cantidad.',
                        "reservedQuantity": 0
                        }';
            
        } elseif ($flag === 0) {
            $data_string = '{
                                "unlimitedQuantity": false,
                                "dateUtcOnBalanceSystem": null,
                                "quantity":' .(int)$cantidad.'
                            }';

        } 

        /*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "http://logistics.vtexcommercestable.com.br/api/logistics/pvt/inventory/skus/".$sku."/warehouses/1_1?an=pisano");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
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

    function consultarStockBySku($sku) {

        /*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "http://logistics.vtexcommercestable.com.br/api/logistics/pvt/inventory/skus/".$sku."?an=pisano");
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
