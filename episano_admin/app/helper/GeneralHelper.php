<?php 
	
	use App\models\Stock;
	use App\models\Tp_adic;
	use App\models\E_Prod_Vtex_Activos;
	use App\models\ProductosSimilares;
	use App\models\ListaPrecios;
	
	function formatear_precio($precio) {

		if (!is_null($precio)) {
			if ($precio === 0) {
				$importe_array_formateado = $precio.".00";
				return $importe_array_formateado;
			}
			$substr_importe = substr($precio, -2);
		 	$importe_array = str_split($precio);
		 	$cant_caracteres_importe_array = count($importe_array);
		 	$pos_to_change = $cant_caracteres_importe_array -2;
			$importe_array[$pos_to_change] = ".".$importe_array[$pos_to_change];
			$importe_array_formateado = implode($importe_array);

			return $importe_array_formateado;

		} else {
			$importe_array_formateado = 1;
			return $importe_array_formateado;

		}
	}

	function consultar_producto_by_sku($sku = null) {
		if (!is_null($sku)) {
			
			/*Inicio curl*/
	        $ch = curl_init();

	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL, "http://pisano.vtexcommercestable.com.br/api/catalog_system/pvt/sku/stockkeepingunitbyid/".$sku);
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

		}
	}


	function insertProducts($list_product = array(), $estado = null) {

		/*Funcion que sube y registra productos en VTEX por medio de WS SOAP*/

		/*Valido si se quiere activar un producto desde el ABM*/
		if (is_array($list_product) && count($list_product != 0)) {
    	
	    	try {

	    		/*Recorro el listado de productos para realizar la insercion*/
	    		foreach ($list_product as $key => $product) {
	    			
	    			/*Obtengo el precio del producto*/
	    			$cost_price = Stock::getPriceBySku($product->codigo);

	    			$list_price = ListaPrecios::getPriceBySku($product->codigo);

	    			/*Se realiza el primer paso, se registra el producto en VTEX*/

					/*Inicio curl*/
			        $ch = curl_init();

			        if ($product->v_id_subca != 0) {
			        	$departmentId = (int)$product->v_id_categ;
			        	$categoryId = (int)$product->v_id_subca;

			        } else {  
			        	$departmentId = (int)$product->v_id_dep;
			        	$categoryId = (int)$product->v_id_categ;

			        }

			    
			        /*Estructura del xml SOAP*/
			        /*$soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:vtex="http://schemas.datacontract.org/2004/07/Vtex.Commerce.WebApps.AdminWcfService.Contracts">
	   					<soapenv:Header/>
		   				<soapenv:Body>
					      <tem:ProductInsertUpdate>
					         <!--Optional:-->
					         <tem:productVO>
					            <vtex:BrandId>'.$product->v_id_marca.'</vtex:BrandId>
					            <vtex:CategoryId>'.$categoryId.'</vtex:CategoryId>
					            <vtex:DepartmentId>'.$departmentId.'</vtex:DepartmentId>
					            <vtex:Description>'.$product->descripcio.'</vtex:Description>
					            <vtex:Id>'.$product->v_id_produ.'</vtex:Id>
					            <vtex:IsActive>true</vtex:IsActive>
					            <vtex:IsVisible>true</vtex:IsVisible>
					            <vtex:KeyWords>'.$product->nombrevtx.'</vtex:KeyWords>
					            <vtex:LinkId>'.$product->id_link.'</vtex:LinkId>
					            <vtex:ListStoreId xmlns:b="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
					               <b:int>1</b:int>
					            </vtex:ListStoreId>
					            <vtex:MetaTagDescription>'.$product->nombrevtx.'</vtex:MetaTagDescription>
					            <vtex:Name>'.$product->nombrevtx.'</vtex:Name>
					            <vtex:RefId>'.$product->codigo.'</vtex:RefId>
					            <vtex:ShowWithoutStock>false</vtex:ShowWithoutStock>
					            <vtex:Title>'.$product->nombrevtx.'</vtex:Title>
					         </tem:productVO>
					      </tem:ProductInsertUpdate>
		   				</soapenv:Body>
					</soapenv:Envelope>';*/

					/*Estructura del xml SOAP*/
			        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:vtex="http://schemas.datacontract.org/2004/07/Vtex.Commerce.WebApps.AdminWcfService.Contracts">
	   					<soapenv:Header/>
		   				<soapenv:Body>
					      <tem:ProductInsertUpdate>
					         <!--Optional:-->
					         <tem:productVO>
					            <vtex:BrandId>'.$product->v_id_marca.'</vtex:BrandId>
					            <vtex:CategoryId>'.$categoryId.'</vtex:CategoryId>
					            <vtex:DepartmentId>'.$departmentId.'</vtex:DepartmentId>
					            <vtex:Description>'.$product->descripcio.'</vtex:Description>
					            <vtex:Id>'.$product->v_id_produ.'</vtex:Id>
					            <vtex:IsActive>true</vtex:IsActive>
					            <vtex:IsVisible>true</vtex:IsVisible>
					            <vtex:KeyWords>'.$product->detalle.'</vtex:KeyWords>
					            <vtex:LinkId>'.$product->id_link.'</vtex:LinkId>
					            <vtex:ListStoreId xmlns:b="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
					               <b:int>1</b:int>
					            </vtex:ListStoreId>
					            <vtex:MetaTagDescription>'.$product->detalle.'</vtex:MetaTagDescription>
					            <vtex:Name>'.$product->detalle.'</vtex:Name>
					            <vtex:RefId>'.$product->codigo.'</vtex:RefId>
					            <vtex:ShowWithoutStock>false</vtex:ShowWithoutStock>
					            <vtex:Title>'.$product->detalle.'</vtex:Title>
					         </tem:productVO>
					      </tem:ProductInsertUpdate>
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
				        "SOAPAction: http://tempuri.org/IService/ProductInsertUpdate",
				        "Content-length: ".strlen($soap_request),
		    		);

			        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			        $server_output = curl_exec ($ch);

			        if (curl_errno($ch)) {
			             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
			        } 

			        curl_close($ch);

			        /*Sube el detalle del producto recien registrado en VTEX*/
		        	uploadProductDetail($product, $cost_price, $list_price);

		        	/*EXPOSICIÓN	SUPERFICIE	ACABADO	DILUCIÓN	BASE	RENDIMIENTO	MANOS	APLICACIÓN*/

			        /*Carga los atributos adicionales del Producto en VTEX*/
					if (!is_null($product->color)) {
		        		uploadAttributesSku('COLOR', $product->color, $product->codigo);

		        	}

		        	uploadAttributesSku($product->patron, $product->v_n_patron, $product->codigo);

			        if (!is_null($product->sup1) && $product->sup1 != "") {
			        	
			        	uploadAttributesProducts($product->patron, $product->sup1, $product->v_id_produ);

			        }

			        if (!is_null($product->sup2) && $product->sup2 != "") {
			        	uploadAttributesProducts('SUPERFICIE', $product->sup2, $product->v_id_produ);

			        }

			        if (!is_null($product->acabado) && $product->acabado != "") {
			        	uploadAttributesProducts('ACABADO', $product->acabado, $product->v_id_produ);

			        }

			        if (!is_null($product->dilucion) && $product->dilucion != "") {
			        	uploadAttributesProducts('DILUCIÓN', $product->dilucion, $product->v_id_produ);

			        }

			        if (!is_null($product->base) && $product->base != "") {
			        	uploadAttributesProducts('BASE', $product->base, $product->v_id_produ);

			        }

			        if (!is_null($product->rendimient) && $product->rendimient != "") {
			        	uploadAttributesProducts('RENDIMIENTO', $product->rendimient, $product->v_id_produ);

			        }

			        if (!is_null($product->manos) && $product->manos != "") {
			        	uploadAttributesProducts('MANOS', $product->manos, $product->v_id_produ);

			        }

			        if (!is_null($product->aplicacion) && $product->aplicacion != "") {
			        	uploadAttributesProducts('APLICACION', $product->aplicacion, $product->v_id_produ);

			        }

			        if (!is_null($product->patron) && $product->patron != "") {
			        	uploadAttributesProducts($product->patron, $product->v_n_patron, $product->v_id_produ);

			        }

			        /*Subir imagen del producto*/
			        uploadProductImage($product->v_imagen, $product->codigo, $product->codigo);

			
		        	/*Activa el proucto que se registro recientemente*/
		        	activeProduct($product->codigo);

		        	/*Valido si ya existe en la tabla de productos activos*/
		        	$existe_producto = E_Prod_Vtex_Activos::validarExistenciaSku($product->codigo);
		        	
		        	if (count($existe_producto) > 0) {
		        		E_Prod_Vtex_Activos::actualizarEstadoSku($product->codigo, 'activo');
		        		
		        	} else {
		        		E_Prod_Vtex_Activos::saveProductoActivo($product->codigo);

		        	}

	    		}

			} catch (Exception $e) {

				$e->getMessage();
				
			}

    	} else {
			/*Se traen todos los productos de la tabla tp_adic*/

		}
	
	}


	function uploadProductDetail($info_product, $cost_price, $list_price, $estado = null) {
		
		/*Funcion que sube y registra el detalle del producto a registrar en VTEX*/
		if (!is_null($info_product) && is_array($cost_price) && count($cost_price) > 0 && is_array($list_price) && count($list_price) > 0) {
			
			try {

				/*Inicio curl*/
		        $ch = curl_init();
		         
		        if ($info_product->peso != "") {
		        	if (strpos($info_product->peso, " ")) {
		        		$peso = str_replace(" ", ".", $info_product->peso);
		        		
		        	} else {
		        		$peso = $info_product->peso;

		        	}
		        	
		        	
		        } else {
		        	$peso = 0;
		        
		        }

		        if (!is_null($estado)) {
		        	$estado = 'false';

		        } else {
		        	$estado = "true";

		        }
		        
		        /*Estructura del xml SOAP*/
		        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:vtex="http://schemas.datacontract.org/2004/07/Vtex.Commerce.WebApps.AdminWcfService.Contracts">
	   				<soapenv:Header/>
	   				<soapenv:Body>
				      <tem:StockKeepingUnitInsertUpdate>
				         <!--Optional:-->
				         <tem:stockKeepingUnitVO>
				            <vtex:CommercialConditionId>1</vtex:CommercialConditionId>
				            <vtex:CostPrice>'.$cost_price[0]->costo.'</vtex:CostPrice>
				            <vtex:CubicWeight>0.5625</vtex:CubicWeight>
				            <vtex:Height>15.0000</vtex:Height>
				            <vtex:Id>'.$info_product->codigo.'</vtex:Id>
				            <vtex:IsActive>'.$estado.'</vtex:IsActive>
				            <vtex:IsAvaiable>true</vtex:IsAvaiable>
				            <vtex:IsKit>false</vtex:IsKit>
				            <vtex:Length>15.0000</vtex:Length>
				            <vtex:ListPrice>'.$list_price[0]->precio.'</vtex:ListPrice>
				            <vtex:MeasurementUnit>un</vtex:MeasurementUnit>
				            <vtex:ModalId>1</vtex:ModalId>
				            <vtex:Name>'.$info_product->nombrevtx.'</vtex:Name>
				            <vtex:Price>'.$list_price[0]->precio.'</vtex:Price>
				            <vtex:ProductId>'.$info_product->v_id_produ.'</vtex:ProductId>
				            <vtex:ProductName>'.$info_product->nombrevtx.'</vtex:ProductName>
				            <vtex:RefId>'.$info_product->codigo.'</vtex:RefId>
				            <vtex:UnitMultiplier>1.0000</vtex:UnitMultiplier>
				            <vtex:VenderSeparadamente>false</vtex:VenderSeparadamente>
				            <vtex:WeightKg>'.$peso.'</vtex:WeightKg>
				            <vtex:Width>15.0000</vtex:Width>
				         </tem:stockKeepingUnitVO>
				      </tem:StockKeepingUnitInsertUpdate>
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
			        "SOAPAction: http://tempuri.org/IService/StockKeepingUnitInsertUpdate",
			        "Content-length: ".strlen($soap_request),
	    		);

		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		        
		        $server_output = curl_exec ($ch);

		        /*Traigo el stock para este sku*/
		        $stock = Stock::getStockBySku($info_product->codigo);

		        /*Sube Stock del Producto*/
			$stock = $stock[0]->s_deposito;

			actualizarStockProducto($info_product->codigo, $stock, 1);

		     	
		        if (curl_errno($ch)) {
		             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		        } 

		        curl_close($ch);

			} catch (Exception $e) {
				
				$e->getMessage();
				
			}

		} else { 
			

		}
	
	}

	function activeProduct($sku_product) {

		/*Funcion que activa el Producto subido a VTEX previamente, solo implica usar el sku que se genera al registrar el producto para poder activarlo posteriormente*/
		
		if (!is_null($sku_product)) {

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

		        $server_output = curl_exec($ch);
		        
		        if (curl_errno($ch)) {
		             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		        } 

		        curl_close($ch);

			} catch (Exception $e) {

				$e->getMessage();
				
			}
			
		}

	}


	function inactiveProduct($sku) {

		/*Funcion que inactiva el Producto subido a VTEX previamente, solo implica usar el sku que se genera al registrar el product;o para poder inactivarlo posteriormente*/

		$info_product = Tp_adic::getProductBySku($sku);
		
		if (count($info_product) > 0) {

			foreach ($info_product as $key => $product) {

		   		/*Obtengo el precio del producto*/
		   		$cost_price = Stock::getPriceBySku($product->codigo);
				$list_price = ListaPrecios::getPriceBySku($product->codigo);
			
		   		uploadProductDetail($product, $cost_price, $list_price, 'inactive');

	   		}
		}
	   	
	}

	function uploadProductImage($imageLink, $image_name, $sku) {

		if (!is_null($imageLink) && !is_null($image_name) && !is_null($sku)) {


			/*Funcion que publica imagen del producto que se subio a VTEX*/
			$image_name = $image_name.'.png';
		
			try {

				/*Inicio curl*/
		        $ch = curl_init();

		        /*Estructura del xml SOAP*/
		        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:ImageServiceInsertUpdate>
         <!--Optional:-->
         <tem:urlImage>'.$imageLink.'</tem:urlImage>
         <!--Optional:-->
         <tem:imageName>'.$image_name.'</tem:imageName>
         <!--Optional:-->
         <tem:stockKeepingUnitId>'.$sku.'</tem:stockKeepingUnitId>
      </tem:ImageServiceInsertUpdate>
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
			        "SOAPAction: http://tempuri.org/IService/ImageServiceInsertUpdate",
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
	}

	/*Funcion que elimina todas las imagenes de un sku en VTEX*/
	function borrarImagenesSku($sku = null) {
		if (!is_null($sku)) {
			
			try {

				/*Inicio curl*/
	        	$ch = curl_init();

	        	$soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   					<soapenv:Header/>
  			 		<soapenv:Body>
      					<tem:StockKeepingUnitImageRemove>
         				<!--Optional:-->
         				<tem:stockKeepingUnitId>'.$sku.'</tem:stockKeepingUnitId>
      					</tem:StockKeepingUnitImageRemove>
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
			        "SOAPAction: http://tempuri.org/IService/StockKeepingUnitImageRemove",
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
	}

	function uploadAttributesSku($attribute, $value, $sku) {

		/*Funcion que sube los atributos adicionales de un producto a VTEX*/

		try {

			/*Inicio curl*/
	        $ch = curl_init();

	        /*Estructura del xml SOAP*/
	        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:StockKeepingUnitEspecificationInsert>
         <!--Optional:-->
         <tem:idSku>'.$sku.'</tem:idSku>
         <!--Optional:-->
         <tem:fieldName>'.$attribute.'</tem:fieldName>
         <!--Optional:-->
         <tem:fieldValues>
            <!--Zero or more repetitions:-->
            <arr:string>'.$value.'</arr:string>
         </tem:fieldValues>
      </tem:StockKeepingUnitEspecificationInsert>
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
		        "SOAPAction: http://tempuri.org/IService/StockKeepingUnitEspecificationInsert",
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

	function uploadAttributesProducts($attribute, $value, $idProduct) {
		
		/*Funcion que sube los atributos adicionales de un producto a VTEX*/
		try {

			/*Inicio curl*/
	        $ch = curl_init();

	        /*Estructura del xml SOAP*/
	        $soap_request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:ProductEspecificationInsert>
         <!--Optional:-->
         <tem:idProduct>'.$idProduct.'</tem:idProduct>
         <!--Optional:-->
         <tem:fieldName>'.$attribute.'</tem:fieldName>
         <!--Optional:-->
         <tem:fieldValues>
            <!--Zero or more repetitions:-->';

            $pos = strpos($value,',');
			
			if ($pos !== false) {
				$val_atrr = explode(",", $value);

				foreach ($val_atrr as $key => $val) {
            		
					$soap_request .= '<arr:string>'.$val.'</arr:string>';
				}

			} else {
				$soap_request .= '<arr:string>'.$value.'</arr:string>';

			}

        $soap_request .= '</tem:fieldValues>
      </tem:ProductEspecificationInsert>
   </soapenv:Body>
</soapenv:Envelope>';
			
	        /*Seteo las opciones de curl basicas*/
	        curl_setopt($ch,CURLOPT_URL,"http://webservice-pisano.vtexcommerce.com.br/AdminWebService/Service.svc?wsdl");
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
		        "SOAPAction: http://tempuri.org/IService/ProductEspecificationInsert",
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

	function productosSimilares() {

		/*Funcion que permite mostrar en VTEX los productos similares al que uno esta viendo*/
		//$sku_array = array('7572', '7573');
		/*Obtenemos los productos similares de acuerdo al sku actual*/
		$prod_similares = ProductosSimilares::getAllProductosSimilares();
		
		if (count($prod_similares) > 0) {

			try {

					foreach ($prod_similares as $key => $pS) {
						
						/*Inicio curl*/
		        		$ch = curl_init();

						/*Estructura del xml SOAP*/
		        	$soap_request =	'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:vtex="http://schemas.datacontract.org/2004/07/Vtex.Commerce.WebApps.AdminWcfService.Contracts" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <tem:StockKeepingUnitComplementInsertUpdate>
				         <!--Optional:-->
				         <tem:objStockKeepingUnitComplementDTO>
				            <!--Optional:-->
				            <vtex:ComplementType>'.$pS->tipo_complemento.'</vtex:ComplementType>
				            <!--Optional:-->
				            <vtex:StockKeepingUnitComplements>';


						$pos = strpos($pS->sku_similar,',');
			
						if ($pos !== false) {
							$val_atrr = explode(",", $pS->sku_similar);

							foreach ($val_atrr as $key => $val) {
			            		
								$soap_request .= '<!--Zero or more repetitions:-->
				               					<arr:int>'.$val.'</arr:int>';
							}

						} else {
							$soap_request .= '<!--Zero or more repetitions:-->
				               					<arr:int>'.$pS->sku_similar.'</arr:int>';

						}
					

		            $soap_request .= '</vtex:StockKeepingUnitComplements>
				            <!--Optional:-->
				            <vtex:StockKeepingUnitId>'.$pS->sku_origen.'</vtex:StockKeepingUnitId>
				         </tem:objStockKeepingUnitComplementDTO>
				      </tem:StockKeepingUnitComplementInsertUpdate>
				   </soapenv:Body>
				</soapenv:Envelope>';
			
				/*Seteo las opciones de curl basicas*/
		        curl_setopt($ch,CURLOPT_URL,"http://webservice-pisano.vtexcommerce.com.br/AdminWebService/Service.svc?wsdl");
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
			        "SOAPAction: http://tempuri.org/IService/StockKeepingUnitComplementInsertUpdate",
			        "Content-length: ".strlen($soap_request),
	    		);
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		        $server_output = curl_exec ($ch);
		        
		        if (curl_errno($ch)) {
		             die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		        } 

		        curl_close($ch);

		       }
				
			} catch (Exception $e) {

				$e->getMessage();

			}


		} else {
			echo "No existen productos complementarios para los productos seleccionados";

		}
			
	}

	function actualizarAtributos($lista_prod_activos) {
		
		foreach ($lista_prod_activos as $key => $product) {
			
			if (!is_null($product->color)) {
        		uploadAttributesSku('COLOR', $product->color, $product->codigo);


        	}

        	uploadAttributesSku($product->patron, $product->v_n_patron, $product->codigo);
        	
	        if (!is_null($product->sup1) && $product->sup1 != "") {
	        	uploadAttributesProducts('EXPOSICIÓN', $product->sup1, $product->v_id_produ);

	        }

	        if (!is_null($product->sup2) && $product->sup2 != "") {
	        	uploadAttributesProducts('SUPERFICIE', $product->sup2, $product->v_id_produ);

	        }

	        if (!is_null($product->acabado) && $product->acabado != "") {
	        	uploadAttributesProducts('ACABADO', $product->acabado, $product->v_id_produ);

	        }

	        if (!is_null($product->dilucion) && $product->dilucion != "") {
	        	uploadAttributesProducts('DILUCIÓN', $product->dilucion, $product->v_id_produ);

	        }

	        if (!is_null($product->base) && $product->base != "") {
	        	uploadAttributesProducts('BASE', $product->base, $product->v_id_produ);

	        }

	        if (!is_null($product->rendimient) && $product->rendimient != "") {
	        	uploadAttributesProducts('RENDIMIENTO', $product->rendimient, $product->v_id_produ);

	        }

	        if (!is_null($product->manos) && $product->manos != "") {
	        	uploadAttributesProducts('MANOS', $product->manos, $product->v_id_produ);

	        }

	        if (!is_null($product->aplicacion) && $product->aplicacion != "") {
	        	uploadAttributesProducts('APLICACION', $product->aplicacion, $product->v_id_produ);

	        }

	        if (!is_null($product->patron) && $product->patron != "") {
	        	uploadAttributesProducts($product->patron, $product->v_n_patron, $product->v_id_produ);

	        }

		}

	}

?>
