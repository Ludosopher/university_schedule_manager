<?php

namespace App\Console\Commands;

use App\Helpers\WebsocketHelpers;
use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ReplacementRequestWS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to start the websocket server';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Start server');
        
        $server = IoServer::factory(
            new HttpServer (
                new WsServer(
                    new WebsocketHelpers()
                )
            ),
            8080
        );
    
        $server->run();
    }
}
