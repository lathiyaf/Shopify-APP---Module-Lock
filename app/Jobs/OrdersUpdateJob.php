<?php namespace App\Jobs;

use App\Models\LineItem;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;

class OrdersUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain
     * @param stdClass $data    The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $domainName = $this->shopDomain->toNative();
        $shop = User::where('name', $domainName)->first();
        $sh_order = $this->data;
        $line_items = $sh_order['line_items'];
        if( !empty( $line_items ) ){
            foreach( $line_items as $lkey=>$lval ){
                $line_item = LineItem::where('id', $shop->id)->where('order_id', $sh_order['id'])->where('variant_id', $lval['variant_id'])->first();

                $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/products/'. $lval['product_id'] .'.json';
                $parameter['fields'] = 'id,tags';
                $result = $shop->api()->rest('GET', $endPoint, $parameter);
                if( !$result['errors'] ) {

                    $this->deleteLineItems($sh_order['id'], $shop['id']);

                    $sh_product = $result['body']->container['product'];
                    $line_item = ($line_item) ? $line_item : new LineItem;
                    $line_item->user_id = $shop['id'];
                    $line_item->order_id = $sh_order['id'];
                    $line_item->variant_id = $lval['variant_id'];
                    $line_item->product_id = $lval['product_id'];
                    $line_item->title = $lval['title'];
                    $line_item->sku = $lval['sku'];

                    $srch_words = (@$sh_product['tags'] != '') ? explode( ', ', $sh_product['tags']) : [];
                    array_push($srch_words, $lval['sku']);
                    array_push($srch_words, $lval['title']);

                    $line_item->tags = json_encode($srch_words);
                    $line_item->save();
                }
            }
        }
    }

    public function deleteLineItems($order_id, $user_id){
        $lineItems = LineItem::where('id', $user_id)->where('order_id', $order_id)->get();

        if( $lineItems->count() > 0 ){
            foreach( $lineItems as $key=>$val ){
                $val->delete();
            }
        }
    }
}
