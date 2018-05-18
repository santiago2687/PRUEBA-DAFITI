<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function listaPedidos() {

        /*Inicio curl*/
        $ch = curl_init();

        /*Seteo las opciones de curl basicas*/
        curl_setopt($ch,CURLOPT_URL, "https://pisano.vtexcommercestable.com.br/api/oms/pvt/orders");
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
}
