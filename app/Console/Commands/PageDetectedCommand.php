<?php

namespace App\Console\Commands;

use App\Models\Lock;
use App\User;
use Illuminate\Console\Command;
use LukeTowers\ShopifyPHP\Shopify;

class PageDetectedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect:page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try{
            \Log::info("================= START:: PageDetected ================");
            $locks = Lock::where('resource', 'page')->where('is_sh_deleted', 0)->get();

            if( $locks->count() > 0 ){
                foreach( $locks as $key=>$val ){
                    $user = User::where('id', $val->user_id)->first();

                    $api = new Shopify($user['name'], $user['password']);

                    $result = $api->call('GET', 'admin/pages.json', [
                        'ids' => $val->resource_id,
                        'fields' => 'id',
                    ]);

                   $page = $result->pages;
                   if( empty($page) ){
                       $val->subtitle = 'A deleted page';
                       $val->is_sh_deleted = 1;
                       $val->save();
                   }
                }
            }
        }catch(\Exception $e){

            \Log::info("================= ERROR:: PageDetected ================");
            \Log::info(json_encode($e));
        }
    }
}
