<?php

class halunewcontactControllerCore extends FrontController
{
    public $php_self = 'halunewcontact';

    public function initContent()
    {
        // TO DO PAgina multumesc text etc
        $var ="Thank you!<br/>we will contact you soon.";

        $this->context->smarty->assign(
            array(
                'variabila' => $var
            )
        );

    parent::initContent();

    $this->setTemplate('halunewcontact');
    }

    public function postProcess(){

        if(Tools::getValue('submitMessage') && Tools::getValue('submitMessage')=='Submit'){
            $nume = Tools::getValue('name');
            $prenume = Tools::getValue('surname');
            $telefon = Tools::getValue('telefon');
            $email = Tools::getValue('email');
            $mesaj = Tools::getValue('message');
            $subject = "Mesaj Halu";
            $to = "webmaster@halulight.com"; // de schimbat preluat din modul
            $products_id = Tools::getValue('id_products');
            $products_qty = Tools::getValue('products_qty');
            $date = date('Y-m-d H:i:s');

            $arr_products_id = explode(',', $products_id);
            $arr_products_qty = explode(',', $products_qty);
            $firstarr = array_combine($arr_products_id,$arr_products_qty);
            $secondarr = array();
            foreach ($firstarr as $key_id => $value_id) {
                $req = "SELECT `name` FROM `" . _DB_PREFIX_ . "product_lang` WHERE id_product=".$key_id;
                $res = Db::getInstance()->executeS($req);
                foreach($res as $names) {
                    $secondarr[$key_id] = $names['name'];
                }
            }

            $prod_nume_qty = '';
            foreach ($secondarr as $keyl => $valuel) {
                foreach($firstarr as $keyll => $valuell) {
                    if( $keyl == $keyll) {$prod_nume_qty .= "<br/>Produs:".$valuel." Cantitate:".$valuell."<br/>";}
                }
            }



            $message = "
                <html>
                    <head>
                        <title>Mesaj Client Halu</title>
                    </head>
                    <body>
                        <p>Mesaj Client Halu!</p>
                    <table>
                        <tr>
                            <th>Nume</th>
                            <th>Prenume</th>
                            <th>Telefon</th>
                            <th>Mail</th>
                            <th>Mesaj</th>
                        </tr>
                        <tr>
                            <td>".$nume."</td>
                            <td>".$prenume."</td>
                            <td>".$telefon."</td>
                            <td>".$email."</td>
                            <td>".$prod_nume_qty.$mesaj."</td>
                        </tr>
                    </table>
                    </body>
                </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <webmaster@halulight.com>' . "\r\n";

            /*mail($to,$subject,$message,$headers); OPRIT MOMENTAN TEST INITIAL MERGEA */

            /*INSERT IN SQL MODUL */

            $request =  'INSERT INTO `'. _DB_PREFIX_.'modhalucos` (`nume`, `prenume`, `telefon`, `email`, `mesaj`, `id_product`, `product_qty`, `data`, `id_shop`)
             VALUES ("'.$nume.'","'.$prenume.'","'.$telefon.'","'.$email .'","'.$mesaj.'","'.$products_id.'","'.$products_qty.'","'. $date.'","1")';
             echo $request;
            $result = Db::getInstance()->execute($request);
            $this->context->cart->delete();
        }
    }

}