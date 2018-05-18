<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\PedidoController;

class consultarPedidosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:consultarPedidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trae los pedidos pendientes a procesar desde el e-commerce';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pedido = new PedidoController();
        $pedido->listarPedidos();
        \Log::info('se corrio el cron de listado de pedidos');
    }
}
