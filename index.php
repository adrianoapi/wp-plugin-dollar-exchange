<?php
/*
  Plugin Name: Dollar Exchange
  Description: Plugin para exibir a cotação do dolar atualizada
  Version: 1.0
  Author: AdrianoScpace
  Author URI: http://adriano.space/
 */
add_action('admin_menu', 'exchange_on_menu');
register_activation_hook(__FILE__, 'exchange_on_activation');
register_deactivation_hook(__FILE__, 'exchange_on_deactivation');
register_uninstall_hook(__FILE__, 'exchange_on_uninstall');

foreach (glob(plugin_dir_path(__FILE__) . 'path/*.php') as $file) {
    include_once $file;
}

function exchange_on_menu()
{
  add_menu_page(
    Configuration::getTitlePage(), 
    Configuration::getTitleMenu(),
    'manage_options',
    Configuration::getPluginName(),
    Configuration::getInitial(),
    plugin_dir_url( __FILE__ ) . 'assets/images/dollar.png'
  );
}

function exchange_on_activation()
{
    global $wpdb;

    $orm = new Orm('dollar_exchange', $wpdb);
    $rst = $orm->create();

    $obj = new stdClass();
    $obj->price_buy = '5.24';
    $obj->price_sell = '5.24';
    $obj->date = '2021-08-26';
    $obj->type = 'Dollar';

    $orm = new Orm('dollar_exchange', $wpdb);
    $rst = $orm->insertDefault($obj);

}

function exchange_on_deactivation()
{
    //
}

function exchange_on_uninstall()
{
    global $wpdb;
    $orm = new Orm('dollar_exchange', $wpdb);
    $orm->drop();
}

 
/**
 * Configure admin view
 */
function dollar_exchange_init()
{
  exchange_on_check_delete_item();

  global $wpdb;
  $options = bio_get_config();

  $orm = new Orm('dollar_exchange', $wpdb);
  $rst = $orm->select(["1 ORDER BY date DESC"]);

  $structure = new Structure(new Configuration,'form', $rst);
  echo $structure->render();

}

function exchange_on_check_delete_item()
{
    global $wpdb;
    if(array_key_exists('id', $_GET)){
        $orm = new Orm('dollar_exchange', $wpdb);
        $orm->delete($_GET['id']);
    }
}

function voucher_check_register($wpdb)
{
    $orm = new Orm('dollar_exchange', $wpdb);
    return $orm->select(["date = '".date('Y-m-d')."'"]);
}

/**
 * Configure public view
 */
function voucher_register_table_results()
{
    global $wpdb;

    if(empty(voucher_check_register($wpdb)))
    {
        
        $model = new Soap();
        $model->setDateBegin(date('m-d-Y'))
            ->setDateEnd(date('m-d-Y'));

        $obj = json_decode($model->build());
        if(array_key_exists('value', $obj))
        {
            $size = count($obj->value);
            $id   =  $size > 0 ? $size -1 : $size;

            if(!empty($obj->value[$id]))
            {
                $orm = new Orm('dollar_exchange', $wpdb);
                $orm->insert($obj->value[$id]);
            }
            
        }

    }
    
    $orm = new Orm('dollar_exchange', $wpdb);
    $exchanges = $orm->select(["1 ORDER BY id desc limit 1"]);

    $structure = new Structure(new Configuration, 'table', $exchanges);
    echo $structure->render();

?>
    

<?php
}

add_shortcode('exchange_results', 'voucher_register_table_results');

?>