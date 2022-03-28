<?php

namespace App\Jobs;

use App\Models\LineItem;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $shopID = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopId)
    {
        $this->shopID = $shopId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            \Log::info('============== START:: Sync order =============');
            $shop = User::where('id', $this->shopID)->first();

            $data = [];
            $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/orders.json';
            $parameter['fields'] = 'id,line_items';
            $result = $shop->api()->rest('GET', $endPoint, $parameter);
            if( !$result['errors'] ){
                $sh_orders = $result['body']->container['orders'];
                foreach ( $sh_orders as $key=>$val ){
                    $line_items = $val['line_items'];
                    if( !empty( $line_items ) ){
                        foreach( $line_items as $lkey=>$lval ){
                            $line_item = LineItem::where('id', $shop->id)->where('order_id', $val['id'])->where('variant_id', $lval['variant_id'])->first();

                            $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/products/'. $lval['product_id'] .'.json';
                            $parameter['fields'] = 'id,tags';
                            $result = $shop->api()->rest('GET', $endPoint, $parameter);
                            if( !$result['errors'] ) {

                                $sh_product = $result['body']->container['product'];
                                $line_item = ($line_item) ? $line_item : new LineItem;
                                $line_item->user_id = $shop->id;
                                $line_item->order_id = $val['id'];
                                $line_item->variant_id = $lval['variant_id'];
                                $line_item->product_id = $lval['product_id'];
                                $line_item->title = $lval['title'];
                                $line_item->sku = $lval['sku'];
                                $line_item->tags = json_encode(explode( ', ', $sh_product['tags']));

                                $srch_words = explode( ', ', $sh_product['tags']);
                                array_push($srch_words, $lval['sku']);
                                array_push($srch_words, $lval['title']);

                                $line_item->search_words = json_encode($srch_words);
                                $line_item->save();
                            }
                        }
                    }
                }
            }
        }catch( \Exception $e ){
            \Log::info('============== EROR:: Sync order =============');
            \Log::info($e);
        }
    }
}
