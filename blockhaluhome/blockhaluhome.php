<?php
/*
* @author Parfene Marius <parfenemariuscatalin@gmail.com>
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

include_once __DIR__ . '/Halu_home.php';

class BlockHaluHome extends Module implements WidgetInterface
{
	const PS_16_EQUIVALENT_MODULE = 'blockhaluhome';

	private $templateFile;

	public function __construct()
    {
        $this->name = 'blockhaluhome';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Parfene Marius';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Halu Home', [], 'Modules.BlockHaluHome.Admin');
        $this->description = $this->trans('Adds videos and links to homepage', [], 'Modules.BlockHaluHome.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];

        $this->templateFile = 'module:blockhaluhome/haluhome.tpl';
    }

    public function install()
    {
    	if(
            parent::install() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('actionShopDataDuplication')
        ) {
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
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'haluhome` (
                `id_haluhome_videos` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_haluhome_videos`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;

        ');

    	$res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'haluhome_videos` (
              `id_haluhome_videos` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id_haluhome_videos`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'haluhome_videos_lang` (
              `id_haluhome_videos` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              `description` text NOT NULL,
              `legend` varchar(255) NOT NULL,
              `url` varchar(255) NOT NULL,
              `video` varchar(255) NOT NULL,
              PRIMARY KEY (`id_haluhome_videos`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        return $res;
    }

    protected function deleteTables()
    {
    	$videos = $this->getVideos();
    	foreach ($videos as $video) {
            $to_del = new Halu_home($video['id_slide']);
            $to_del->delete();
        }

         return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'haluhome`, `' . _DB_PREFIX_ . 'haluhome_videos`, `' . _DB_PREFIX_ . 'haluhome_videos_lang`;
        ');
    }

    public function getVideos($active = null)
    {
    	$this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        $slides = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
            'SELECT hs.`id_haluhome_videos` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
            hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`video`
            FROM ' . _DB_PREFIX_ . 'haluhome hs
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos hss ON (hs.id_haluhome_videos = hss.id_haluhome_videos)
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos_lang hssl ON (hss.id_haluhome_videos = hssl.id_haluhome_videos)
            WHERE id_shop = ' . (int) $id_shop . '
            AND hssl.id_lang = ' . (int) $id_lang . '
            AND hssl.`video` <> ""' .
            ($active ? ' AND hss.`active` = 1' : ' ') . '
            ORDER BY hss.position'
        );

        foreach ($slides as &$slide) {
            $slide['video_url'] = $this->context->link->getMediaLink(_MODULE_DIR_ . 'blockhaluhome/videos/' . $slide['video']);
            $slide['url'] = $this->validateUrl($slide['url']);
        }

        return $slides;
    }

    protected function validateUrl($link)
    {
    	// Empty or anchor link.
        if (empty($link) || 0 === strpos($link, '#')) {
            return $link;
        }

        $host = parse_url($link, PHP_URL_HOST);
        // links starting with http://, https:// or // have $host determined, the rest needs more validation
        if (empty($host)) {
            if (preg_match('/^(?!\-|index\.php)(?:(?:[a-z\d][a-z\d\-]{0,61})?[a-z\d]\.){1,126}(?!\d+)[a-z\d]{1,63}/i', $link)) {
                // handle strings considered to be domain names without protocol eg. 'prestashop.com', excluding 'index.php'
                // ref. https://stackoverflow.com/a/16491074/6389945
                $link = '//' . $link;
            } else {
                // consider other links shop internal and add shop domain in front
                $link = $this->context->link->getBaseLink() . ltrim($link, '/');
            }
        }

        return $link;
    }

    public function getContent()
    {

    	if (
            Tools::isSubmit('submitSlide') ||
            Tools::isSubmit('delete_id_slide') ||
            Tools::isSubmit('submitSlider') ||
            Tools::isSubmit('changeStatus')
        ) {
    		if ($this->_postValidation()) {
                $this->_postProcess();
               /* $this->_html .= $this->renderForm(); */
                $this->_html .= $this->renderList();
            } else {
                $this->_html .= $this->renderAddForm();
            }

            $this->clearCache();
    	} elseif (Tools::isSubmit('addSlide') || (Tools::isSubmit('id_slide') && $this->slideExists((int) Tools::getValue('id_slide')))) {
    		if (Tools::isSubmit('addSlide')) {
                $mode = 'add';
            } else {
                $mode = 'edit';
            }

            if ($mode == 'add') {
                if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL) {
                    $this->_html .= $this->renderAddForm();
                } else {
                    $this->_html .= $this->getShopContextError(null, $mode);
                }
            } else {
                $associated_shop_ids = Halu_home::getAssociatedIdsShop((int) Tools::getValue('id_slide'));
                $context_shop_id = (int) Shop::getContextShopID();

                if ($associated_shop_ids === false) {
                    $this->_html .= $this->getShopAssociationError((int) Tools::getValue('id_slide'));
                } elseif (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL && in_array($context_shop_id, $associated_shop_ids)) {
                    if (count($associated_shop_ids) > 1) {
                        $this->_html = $this->getSharedSlideWarning();
                    }
                    $this->_html .= $this->renderAddForm();
                } else {
                    $shops_name_list = [];
                    foreach ($associated_shop_ids as $shop_id) {
                        $associated_shop = new Shop((int) $shop_id);
                        $shops_name_list[] = $associated_shop->name;
                    }
                    $this->_html .= $this->getShopContextError($shops_name_list, $mode);
                }
            }
    	} else {
            $this->_html .= $this->getWarningMultishopHtml() . $this->getCurrentShopInfoMsg(); /* . $this->renderForm()*/

            if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->_html .= $this->renderList();
            }
        }

        return $this->_html;
    }


    protected function _postValidation()
    {
    	$errors = [];

    	if (Tools::isSubmit('changeStatus')) {
            if (!Validate::isInt(Tools::getValue('id_slide'))) {
                $errors[] = $this->trans('Invalid slide', [], 'Modules.BlockHaluHome.Admin');
            }
        } elseif (Tools::isSubmit('submitSlide')) {
            /* Checks state (active) */
            if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1)) {
                $errors[] = $this->trans('Invalid slide state.', [], 'Modules.BlockHaluHome.Admin');
            }
            /* If edit : checks id_slide */
            if (Tools::isSubmit('id_slide')) {
                if (!Validate::isInt(Tools::getValue('id_slide')) && !$this->slideExists(Tools::getValue('id_slide'))) {
                    $errors[] = $this->trans('Invalid slide ID', [], 'Modules.BlockHaluHome.Admin');
                }
            }
            /* Checks title/url/legend/description/image */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->trans('The title is too long.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::strlen(Tools::getValue('legend_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->trans('The caption is too long.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::strlen(Tools::getValue('url_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->trans('The URL is too long.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::strlen(Tools::getValue('description_' . $language['id_lang'])) > 4000) {
                    $errors[] = $this->trans('The description is too long.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::strlen(Tools::getValue('url_' . $language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_' . $language['id_lang']))) {
                    $errors[] = $this->trans('The URL format is not correct.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::getValue('video_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('video_' . $language['id_lang']))) {
                    $errors[] = $this->trans('Invalid filename.', [], 'Modules.BlockHaluHome.Admin');
                }
                if (Tools::getValue('video_old_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('video_old_' . $language['id_lang']))) {
                    $errors[] = $this->trans('Invalid filename.', [], 'Modules.BlockHaluHome.Admin');
                }
            }

            /* Checks title/legend/description for default lang */
            $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
            if (!Tools::isSubmit('has_picture') && (!isset($_FILES['video_' . $id_lang_default]) || empty($_FILES['video_' . $id_lang_default]['tmp_name']))) {
                $errors[] = $this->trans('The video is not set.', [], 'Modules.BlockHaluHome.Admin');
            }
            if (Tools::getValue('video_old_' . $id_lang_default) && !Validate::isFileName(Tools::getValue('video_old_' . $id_lang_default))) {
                $errors[] = $this->trans('The video is not set.', [], 'Modules.BlockHaluHome.Admin');
            }
        } elseif (Tools::isSubmit('delete_id_slide') && (!Validate::isInt(Tools::getValue('delete_id_slide')) || !$this->slideExists((int) Tools::getValue('delete_id_slide')))) {
            $errors[] = $this->trans('Invalid slide ID', [], 'Modules.BlockHaluHome.Admin');
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    protected function _postProcess()
    {
    	$errors = [];
        $shop_context = Shop::getContext();

        if (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_slide')) {
            $slide = new Halu_home((int) Tools::getValue('id_slide'));
            if ($slide->active == 0) {
                $slide->active = 1;
            } else {
                $slide->active = 0;
            }
            $res = $slide->update();
            $this->clearCache();
            $this->_html .= ($res ? $this->displayConfirmation($this->trans('Configuration updated', [], 'Admin.Notifications.Success')) : $this->displayError($this->getTranslator()->trans('The configuration could not be updated.', [], 'Modules.BlockHaluHome.Admin')));
        } elseif (Tools::isSubmit('submitSlide')) {
            /* Sets ID if needed */
            if (Tools::getValue('id_slide')) {
                $slide = new Halu_home((int) Tools::getValue('id_slide'));
                if (!Validate::isLoadedObject($slide)) {
                    $this->_html .= $this->displayError($this->trans('Invalid slide ID', [], 'Modules.BlockHaluHome.Admin'));

                    return false;
                }
            } else {
                $slide = new Halu_home();
                /* Sets position */
                $slide->position = (int) $this->getNextPosition();
            }
            /* Sets active */
            $slide->active = (int) Tools::getValue('active_slide');

            /* Sets each langue fields */
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                $slide->title[$language['id_lang']] = Tools::getValue('title_' . $language['id_lang']);
                $slide->url[$language['id_lang']] = Tools::getValue('url_' . $language['id_lang']);
                $slide->legend[$language['id_lang']] = Tools::getValue('legend_' . $language['id_lang']);
                $slide->description[$language['id_lang']] = Tools::getValue('description_' . $language['id_lang']);

                /* Uploads image and set video */
                if (
                    isset($_FILES['video_' . $language['id_lang']]) &&
                    !empty($_FILES['video_' . $language['id_lang']]['tmp_name'])
                ) {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['video_' . $language['id_lang']]['name'], '.'), 1));
                }
                if(!empty($type) && $type == 'mp4') {
                    if(!empty($_FILES['video_' . $language['id_lang']]['name'])) {
                        $idslide = Tools::getValue('id_slide');

                        $lang_id = $language['id_lang'];
                        if(isset($idslide) && $idslide != '') {
                            $gettodelete = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS('SELECT `video` FROM `' . _DB_PREFIX_ . 'haluhome_videos_lang` WHERE `id_haluhome_videos` = '.$idslide.' AND `id_lang` = ' . $lang_id . '');
                            foreach($gettodelete as $rowtd) {
                                $vidotd = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR. $rowtd['video'];
                                if(file_exists($vidotd)){
                                    @unlink($vidotd);
                                }
                            }
                        }
                        

                        if(!move_uploaded_file($_FILES['video_' . $language['id_lang']]['tmp_name'], dirname(__FILE__) . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR.$_FILES['video_' . $language['id_lang']]['name'])) {
                            return false;
                        }
                    }
                    $slide->video[$language['id_lang']] = $_FILES['video_' . $language['id_lang']]['name'];
                }
            }

            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_slide')) {
                    if (!$slide->add()) {
                        $errors[] = $this->displayError($this->trans('The video could not be added.', [], 'Modules.BlockHaluHome.Admin'));
                    }
                } elseif (!$slide->update()) {
                    $errors[] = $this->displayError($this->trans('The video could not be updated.', [], 'Modules.BlockHaluHome.Admin'));
                }
                $this->clearCache();
            }
        } elseif (Tools::isSubmit('delete_id_slide')) {
            $slide = new Halu_home((int) Tools::getValue('delete_id_slide'));
            $res = $slide->delete();
            $this->clearCache();
            if (!$res) {
                $this->_html .= $this->displayError('Could not delete.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=1&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitSlide') && Tools::getValue('id_slide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        } elseif (Tools::isSubmit('submitSlide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        }
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->registerStylesheet('modules-haluhome', 'modules/' . $this->name . '/css/haluhome.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript('modules-haluhome', 'modules/' . $this->name . '/js/haluhome.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId());
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $slides = $this->getVideos(true);
        if (is_array($slides)) {
            foreach ($slides as &$slide) {
                $slide['sizes'] = @getimagesize((__DIR__ . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $slide['image']));
                if (isset($slide['sizes'][4]) && $slide['sizes'][4]) {
                    $slide['size'] = $slide['sizes'][4];
                }
            }
        }

        return [
            'haluhome' => [
                'videos' => $slides,
            ],
        ];
    }

    public function clearCache()
    {
        $this->_clearCache($this->templateFile);
    }

    public function hookActionShopDataDuplication($params)
    {
        Db::getInstance()->execute(
            'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'haluhome (id_haluhome_videos, id_shop)
            SELECT id_haluhome_videos, ' . (int) $params['new_id_shop'] . '
            FROM ' . _DB_PREFIX_ . 'haluhome
            WHERE id_shop = ' . (int) $params['old_id_shop']
        );
        $this->clearCache();
    }

    public function getNextPosition()
    {
        $row = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getRow(
            'SELECT MAX(hss.`position`) AS `next_position`
            FROM `' . _DB_PREFIX_ . 'haluhome_videos` hss, `' . _DB_PREFIX_ . 'haluhome` hs
            WHERE hss.`id_haluhome_videos` = hs.`id_haluhome_videos` AND hs.`id_shop` = ' . (int) $this->context->shop->id
        );

        return ++$row['next_position'];
    }

    public function getSlides($active = null)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        $slides = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
            'SELECT hs.`id_haluhome_videos` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
            hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`video`
            FROM ' . _DB_PREFIX_ . 'haluhome hs
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos hss ON (hs.id_haluhome_videos = hss.id_haluhome_videos)
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos_lang hssl ON (hss.id_haluhome_videos = hssl.id_haluhome_videos)
            WHERE id_shop = ' . (int) $id_shop . '
            AND hssl.id_lang = ' . (int) $id_lang . '
            AND hssl.`video` <> ""' .
            ($active ? ' AND hss.`active` = 1' : ' ') . '
            ORDER BY hss.position'
        );

        foreach ($slides as &$slide) {
            $slide['video_url'] = $this->context->link->getMediaLink(_MODULE_DIR_ . 'blockhaluhome/videos/' . $slide['video']);
            $slide['url'] = $this->validateUrl($slide['url']);
        }

        return $slides;
    }

    public function getAllImagesBySlidesId($id_slides, $active = null, $id_shop = null)
    {
        $this->context = Context::getContext();
        $images = [];

        if (!isset($id_shop)) {
            $id_shop = $this->context->shop->id;
        }

        $results = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
            'SELECT hssl.`video`, hssl.`id_lang`
            FROM ' . _DB_PREFIX_ . 'haluhome hs
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos hss ON (hs.id_haluhome_videos = hss.id_haluhome_videos)
            LEFT JOIN ' . _DB_PREFIX_ . 'haluhome_videos_lang hssl ON (hss.id_haluhome_videos = hssl.id_haluhome_videos)
            WHERE hs.`id_haluhome_videos` = ' . (int) $id_slides . ' AND hs.`id_shop` = ' . (int) $id_shop .
            ($active ? ' AND hss.`active` = 1' : ' ')
        );

        foreach ($results as $result) {
            $images[$result['id_lang']] = $result['image'];
        }

        return $images;
    }

    public function displayStatus($id_slide, $active)
    {
        $title = ((int) $active == 0 ? $this->trans('Disabled', [], 'Admin.Global') : $this->trans('Enabled', [], 'Admin.Global'));
        $icon = ((int) $active == 0 ? 'icon-remove' : 'icon-check');
        $class = ((int) $active == 0 ? 'btn-danger' : 'btn-success');
        $html = '<a class="btn ' . $class . '" href="' . AdminController::$currentIndex .
            '&configure=' . $this->name .
            '&token=' . Tools::getAdminTokenLite('AdminModules') .
            '&changeStatus&id_slide=' . (int) $id_slide . '" title="' . $title . '"><i class="' . $icon . '"></i> ' . $title . '</a>';

        return $html;
    }

    public function slideExists($id_slide)
    {
        $req = 'SELECT hs.`id_haluhome_videos` as id_slide
                FROM `' . _DB_PREFIX_ . 'haluhome` hs
                WHERE hs.`id_haluhome_videos` = ' . (int) $id_slide;
        $row = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getRow($req);

        return $row;
    }

    public function renderList()
    {
        $slides = $this->getSlides();
        foreach ($slides as $key => $slide) {
            $slides[$key]['status'] = $this->displayStatus($slide['id_slide'], $slide['active']);
            $associated_shop_ids = Halu_home::getAssociatedIdsShop((int) $slide['id_slide']);
            if ($associated_shop_ids && count($associated_shop_ids) > 1) {
                $slides[$key]['is_shared'] = true;
            } else {
                $slides[$key]['is_shared'] = false;
            }
        }

        $this->context->smarty->assign(
            [
                'link' => $this->context->link,
                'slides' => $slides,
                'image_baseurl' => $this->_path . 'videos/',
            ]
        );

        return $this->display(__FILE__, 'list.tpl');
    }

    public function renderAddForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Slide information', [], 'Modules.BlockHaluHome.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'file_lang',
                        'label' => $this->trans('Video', [], 'Admin.Global'),
                        'name' => 'video',
                        'required' => true,
                        'lang' => true,
                        'desc' => $this->trans('Maximum video size: %s.', [ini_get('upload_max_filesize')], 'Admin.Global'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Title', [], 'Admin.Global'),
                        'name' => 'title',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Target URL', [], 'Modules.BlockHaluHome.Admin'),
                        'name' => 'url',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Caption', [], 'Modules.BlockHaluHome.Admin'),
                        'name' => 'legend',
                        'lang' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->trans('Description', [], 'Admin.Global'),
                        'name' => 'description',
                        'autoload_rte' => true,
                        'lang' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        'name' => 'active_slide',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        if (Tools::isSubmit('id_slide') && $this->slideExists((int) Tools::getValue('id_slide'))) {
            $slide = new Halu_home((int) Tools::getValue('id_slide'));
            $fields_form['form']['input'][] = ['type' => 'hidden', 'name' => 'id_slide'];
            $fields_form['form']['images'] = $slide->video;

            $has_picture = true;

            foreach (Language::getLanguages(false) as $lang) {
                if (!isset($slide->image[$lang['id_lang']])) {
                    $has_picture &= false;
                }
            }

            if ($has_picture) {
                $fields_form['form']['input'][] = ['type' => 'hidden', 'name' => 'has_picture'];
            }
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlide';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = [
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => [
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code,
            ],
            'fields_value' => $this->getAddFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path . 'videos/',
        ];

        $helper->override_folder = '/';

        $languages = Language::getLanguages(false);

        if (count($languages) > 1) {
            return $this->getMultiLanguageInfoMsg() . $helper->generateForm([$fields_form]);
        } else {
            return $helper->generateForm([$fields_form]);
        }
    }

    /* In case i will need config 
    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlider';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    } */

    public function getAddFieldsValues()
    {
        $fields = [];

        if (Tools::isSubmit('id_slide') && $this->slideExists((int) Tools::getValue('id_slide'))) {
            $slide = new Halu_home((int) Tools::getValue('id_slide'));
            $fields['id_slide'] = (int) Tools::getValue('id_slide', $slide->id);
        } else {
            $slide = new Halu_home();
        }

        $fields['active_slide'] = Tools::getValue('active_slide', $slide->active);
        $fields['has_picture'] = true;

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['video'][$lang['id_lang']] = Tools::getValue('video_' . (int) $lang['id_lang']);
            $fields['title'][$lang['id_lang']] = Tools::getValue(
                'title_' . (int) $lang['id_lang'],
                isset($slide->title[$lang['id_lang']]) ? $slide->title[$lang['id_lang']] : ''
            );
            $fields['url'][$lang['id_lang']] = Tools::getValue(
                'url_' . (int) $lang['id_lang'],
                isset($slide->url[$lang['id_lang']]) ? $slide->url[$lang['id_lang']] : ''
            );
            $fields['legend'][$lang['id_lang']] = Tools::getValue(
                'legend_' . (int) $lang['id_lang'],
                isset($slide->legend[$lang['id_lang']]) ? $slide->legend[$lang['id_lang']] : ''
            );
            $fields['description'][$lang['id_lang']] = Tools::getValue(
                'description_' . (int) $lang['id_lang'],
                isset($slide->description[$lang['id_lang']]) ? $slide->description[$lang['id_lang']] : ''
            );
        }

        return $fields;
    }

    protected function getMultiLanguageInfoMsg()
    {
        return '<p class="alert alert-warning">' .
            $this->trans('Since multiple languages are activated on your shop, please mind to upload your videos for each one of them', [], 'Modules.BlockHaluHome.Admin') .
            '</p>';
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
                $this->trans('You cannot manage slides items from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', [], 'Modules.BlockHaluHome.Admin') .
                '</p>';
        } else {
            return '';
        }
    }

    protected function getShopContextError($shop_contextualized_name, $mode)
    {
        if (is_array($shop_contextualized_name)) {
            $shop_contextualized_name = implode('<br/>', $shop_contextualized_name);
        }

        if ($mode == 'edit') {
            return '<p class="alert alert-danger">' .
                $this->trans('You can only edit this slide from the shop(s) context: %s', [$shop_contextualized_name], 'Modules.BlockHaluHome.Admin') .
                '</p>';
        } else {
            return '<p class="alert alert-danger">' .
                $this->trans('You cannot add slides from a "All Shops" or a "Group Shop" context', [], 'Modules.BlockHaluHome.Admin') .
                '</p>';
        }
    }

    protected function getShopAssociationError($id_slide)
    {
        return '<p class="alert alert-danger">' .
            $this->trans('Unable to get slide shop association information (id_slide: %d)', [(int) $id_slide], 'Modules.BlockHaluHome.Admin') .
            '</p>';
    }

    protected function getCurrentShopInfoMsg()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->trans('The modifications will be applied to shop: %s', [$this->context->shop->name], 'Modules.BlockHaluHome.Admin');
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_info = $this->trans('The modifications will be applied to this group: %s', [Shop::getContextShopGroup()->name], 'Modules.BlockHaluHome.Admin');
            } else {
                $shop_info = $this->trans('The modifications will be applied to all shops and shop groups', [], 'Modules.BlockHaluHome.Admin');
            }

            return '<div class="alert alert-info">' . $shop_info . '</div>';
        } else {
            return '';
        }
    }

    protected function getSharedSlideWarning()
    {
        return '<p class="alert alert-warning">' .
            $this->trans('This slide is shared with other shops! All shops associated to this slide will apply modifications made here', [], 'Modules.BlockHaluHome.Admin') .
            '</p>';
    }
}
