<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AfterAuthenticationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
        \Log::info('============= START:: after authentication job ============');
            $shop = Auth::user();
            \Log::info($shop);
            $lcm = '<p><strong>This content is protected</strong> - please log in with your customer account to continue.</p>';
            $acm = '<p><strong>This content is protected, but it doesn\'t look like you have access.</strong> If you feel this is a mistake, please contact the store owner.</p>';
            $settings = ['is_enable' => 1, 'guest_user_content' => $lcm, 'access_denied_content' => $acm];

            foreach ( $settings as $key=>$val ){
                $is_exist = Setting::where('key', $key)->where('user_id', $shop->id)->first();

                if( !$is_exist ){
                    $new_sett = new Setting;
                    $new_sett->user_id = $shop->id;
                    $new_sett->key = $key;
                    $new_sett->value = $val;
                    $new_sett->save();
                }
            }

//            $this->syncPage();
//            $this->addSnippet();
//            SyncOrderJob::dispatch($shop->id);
        \Log::info('============= END:: after authentication job ============');
        }catch( \Exception $e ){
            \Log::info($e);
        }
    }

    public function syncPage(){
        try{
            \Log::info('============= START:: syncPage ============');
            $shop = \Auth::user();
            $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/pages.json';
            $parameter = [];

            $result = $shop->api()->rest('GET', $endPoint, $parameter);
            if( !$result['errors'] ){
                $pages = $result['body']->container['pages'];
                foreach ( $pages as $key=>$val ){
                    $page = Page::where('user_id', $shop->id)->where('page_id', $val['id'])->first();
                    $page = ( $page ) ? $page : new Page;
                    $page->user_id = $shop->id;
                    $page->page_id = $val['id'];
                    $page->title = $val['title'];
                    $page->handle = $val['handle'];
                    $page->save();
                }
            }
        }catch( \Exception $e ){
            \Log::info('============= ERROR:: syncPage ============');
            \Log::info($e);
        }
    }

    public function addSnippet(){
        try{
            \Log::info('============= START:: addSnippet ============');
            $type = 'add';
            $shop = Auth::user();
            $parameter['role'] = 'main';
            $result = $shop->api()->rest('GET', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/themes.json',$parameter);

//            \Log::info($result);
            $theme_id = $result['body']->container['themes'][0]['id'];
            \Log::info('Theme id :: ' . $theme_id);
            if($type == 'add') {
                $value = <<<EOF
                <script id="module-lock-data" type="application/json">
                    {
                        "shop": {
                        "domain": "{{ shop.domain }}",
                        "permanent_domain": "{{ shop.permanent_domain }}",
                        "url": "{{ shop.url }}",
                        "secure_url": "{{ shop.secure_url }}",
                        "money_format": {{ shop.money_format | json }},
                        "currency": {{ shop.currency | json }}
                        },
                        "customer": {
                            "id": {{ customer.id | json }},
                            "tags": {{ customer.tags | json }}
                        },
                        "cart": {{ cart | json }},
                        "template": "{{ template | split: "." | first }}",
                        "product": {{ product | json }},
                        "collection": {{ collection.products | json }}
                    }
                </script>
                {% if customer %}
                <script>
                    let ml_customer = 1;
                </script>
                {% else %}
                  <script>
                    let ml_customer = 0;
                </script>
                {% endif %}
EOF;
            }
            $parameter['asset']['key'] = 'snippets/module-lock.liquid';
            $parameter['asset']['value'] = $value;
            $asset = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id . '/assets.json',$parameter);

            $this->updateThemeLiquid($theme_id, 'module-lock', $shop);
            \Log::info('============= END:: addSnippet ============');
        }catch( \Exception $e ){
            \Log::info('============= ERROR:: addSnippet ============');
            \Log::info($e);
        }
    }
    public function updateThemeLiquid($theme_id, $snippet_name, $shop)
    {
        try {
            \Log::info('-----------------------updateThemeLiquid-----------------------');
            $asset = $shop->api()->rest('GET', 'admin/themes/'.$theme_id.'/assets.json',
                ["asset[key]" => 'layout/theme.liquid']);
            if (@$asset['body']->container['asset']) {
                $asset = $asset['body']->container['asset']['value'];

                if (!strpos($asset, "{% include '$snippet_name' %}")) {
                    $asset = str_replace('</head>', "{% include '$snippet_name' %}</head>", $asset);
                }

                $parameter['asset']['key'] = 'layout/theme.liquid';
                $parameter['asset']['value'] = $asset;
                $result = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id.'/assets.json', $parameter);
                \Log::info(json_encode($result));
            }
        } catch (\Exception $e) {
            \Log::info('------------ERROR :: updateThemeLiquid--------------');
            \Log::info(json_encode($e));
        }

    }
}
