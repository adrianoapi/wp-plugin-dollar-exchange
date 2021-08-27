<?php
class Structure
{
    private $tempalte;
    private $data;

    public function __construct($template, $data)
    {
        $this->template = $template;
        $this->data     = $data;
    }

    public function render()
    {
        $html = NULL;
        foreach($this->data as $value):
            $html .= '
            <tr style="background-color: #dbe5f1; padding: 5px;">
                <td>'.$value->type.'</td>
                <td>Compra</td>
                <td>R$ '.number_format($value->price_buy, 2, ',', '.').'</td>
            </tr>
            <tr style="background-color: #fff; padding: 5px;">
            <td>'.$value->type.'</td>
            <td>Venda</td>
            <td>R$ '.number_format($value->price_sell, 2, ',', '.').'</td>
            </tr>
            <tr style="background-color: #dbe5f1; padding: 5px;">
                <td>Data</td>
                <td colspan="2">'.$this->dateBr($value->date).'</td>
            </tr>';
        endforeach;

        $content = file_get_contents(__DIR__."/../view/table.html");

        return str_replace('[@TBODY]', $html, $content);
    }

    private function dateBr($string)
    {
        $str = explode('-',$string);
        return $str[2].'/'.$str[1].'/'.$str[0];
    }

}

?>