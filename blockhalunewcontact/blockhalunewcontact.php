<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class blockhalunewcontact extends Module
{
	public function __construct()
    {
        $this->name = 'blockhalunewcontact';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Made by Marius Parfene';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('New Contact for Halu Theme');
        $this->description = $this->l('Adds new contact page for halu theme');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '15');
    }

    public function installTab($parent_class, $class_name, $name)  {
      	$tab = new Tab();
      	// Define the title of your tab that will be displayed in BO
      	$tab->name[$this->context->language->id] = $name; 
      	// Name of your admin controller 
      	$tab->class_name = $class_name;
      	// Id of the controller where the tab will be attached
      	// If you want to attach it to the root, it will be id 0 
      	$tab->id_parent = (int) Tab::getIdFromClassName($parent_class);
      	// Name of your module, if you're not working in a module, just ignore it, it will be set to null in DB
      	$tab->module = $this->name;
      	// Other field like the position will be set to the last, if you want to put it to the top you'll have to modify the position fields directly in your DB
      	return $tab->add();
	} 

    public function install()
    {
    	if(
    		parent::install() &&
    		$this->registerHook('displayTop') &&
    		$this->registerHook('displayHeader') 
    	) {
    		$this->installTab('AdminParentOrders', 'NewContactHaluAdminControllerCore', 'Comenzii Halu');
    		$res = true;
            $res &= $this->createTables();
            return (bool) $res;
    	}

    	return false;
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
    	   $res = $this->deleteTables();
           return (bool) $res;
        }
    	return false;
    }

    protected function createTables()
    {
        $res = (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'modhalucos` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `nume` varchar(255) NOT NULL,
                `prenume` varchar(255) NOT NULL,
                `telefon` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `mesaj` text NOT NULL,
                `id_product` varchar(255) NOT NULL,
                `product_qty` varchar(255) NOT NULL,
                `data` datetime NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;

        ');
    	return $res;
    }

    protected function deleteTables()
    {
    	 return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'modhalucos`;
        ');
    }

    public function hookDisplayTop($param)
    {

    }

    public function hookdisplayHeader($param)
    {

    }

    public function postProcess()
    {

    }

    public function getContent()
    {
        return "AAAAA";
    	 //return $this->postProcess() . $this->renderForm();
	}

	public function renderForm()
    {

    }
}
