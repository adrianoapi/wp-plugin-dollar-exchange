<?php
/*
  Plugin Name: Dollar Exchange
  Description: Plugin para exibir a cotação do dolar atualizada
  Version: 1.0
  Author: AdrianoScpace
  Author URI: http://adriano.space/
 */
register_activation_hook(__FILE__, 'exchange_on_activation');
register_deactivation_hook(__FILE__, 'exchange_on_deactivation');
register_uninstall_hook(__FILE__, 'exchange_on_uninstall');

foreach (glob(plugin_dir_path(__FILE__) . 'path/*.php') as $file) {
    include_once $file;
}

function exchange_on_activation()
{
    global $wpdb;
    # Cria a tabela para armazenar as cotações
    $table_name = $wpdb->prefix . "dollar_exchange";
    $create = "CREATE TABLE `{$table_name}` 
    ( 
        `id` INT NOT NULL AUTO_INCREMENT , 
        `type` VARCHAR(20) NOT NULL , 
        `price_buy` DECIMAL(10,2) NOT NULL , 
        `price_sell` DECIMAL(10,2) NOT NULL , 
        `date` DATE NOT NULL , 
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;";

    $wpdb->query($create);

    $insert = "INSERT INTO `{$table_name}` (`id`, `type`, `price_buy`, `price_sell`, `date`)
                VALUES (NULL, 'dólar', '5.24', '5.24', '2021-08-26')";
    $wpdb->query($insert);

}

function exchange_on_deactivation()
{
    //
}

function exchange_on_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "dollar_exchange";
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
}

function voucher_check_register($wpdb)
{
    $table_name = $wpdb->prefix . "dollar_exchange";
    $select = "SELECT * FROM `{$table_name}` 
               WHERE date = '".date('Y-m-d')."'";
    return $wpdb->get_results($select);
}

function voucher_register_table_results()
{
    global $wpdb;
    $result = [];
    $table_name = $wpdb->prefix . "dollar_exchange";

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

            if(!empty($obj->value[$id])){

                $insert = "INSERT INTO `{$table_name}` (`id`, `type`, `price_buy`, `price_sell`, `date`)
                    VALUES (NULL, 'Dólar', '{$obj->value[$id]->cotacaoCompra}', '{$obj->value[$id]->cotacaoVenda}', '".date('Y-m-d')."')";
                $wpdb->query($insert);

            }
            
        }

    }
    
    # Query
    $exchanges = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id desc limit 1");

    $structure = new Structure('tabela', $exchanges);
    echo $structure->render();

?>
    

<?php
}

add_shortcode('exchange_results', 'voucher_register_table_results');

?>