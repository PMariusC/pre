<?php
/*
* @author Parfene Marius <parfenemariuscatalin@gmail.com>
*/

class Halu_home extends ObjectModel
{
    public $title;
    public $description;
    public $url;
    public $legend;
    public $video;
    public $active;
    public $position;
    public $id_shop;

    public static $definition = [
        'table' => 'haluhome_videos',
        'primary' => 'id_haluhome_videos',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],

            // Lang fields
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'size' => 255],
            'video' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
        ],
    ];

	public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;

        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'haluhome` (`id_shop`, `id_haluhome_videos`)
			VALUES(' . (int) $id_shop . ', ' . (int) $this->id . ')'
        );

        return $res;
    }

    public function delete()
    {
        $res = true;

        $videos = $this->video;
        foreach ($videos as $video) {
            if (preg_match('/sample/', $video) === 0) {
                if ($video && file_exists(__DIR__ . '/videos/' . $video)) {
                    $res &= @unlink(__DIR__ . '/videos/' . $video);
                }
            }
        }

        $res &= $this->reOrderPositions();

        $res &= Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'haluhome`
			WHERE `id_haluhome_videos` = ' . (int) $this->id
        );

        $res &= parent::delete();

        return $res;
    }

    public function reOrderPositions()
    {
        $id_slide = $this->id;
        $context = Context::getContext();
        $id_shop = $context->shop->id;

        $max = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS('
			SELECT MAX(hss.`position`) as position
			FROM `' . _DB_PREFIX_ . 'haluhome_videos` hss, `' . _DB_PREFIX_ . 'haluhome` hs
			WHERE hss.`id_haluhome_videos` = hs.`id_haluhome_videos` AND hs.`id_shop` = ' . (int) $id_shop
        );

        if ((int) $max == (int) $id_slide) {
            return true;
        }

        $rows = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`position` as position, hss.`id_haluhome_videos` as id_slide
			FROM `' . _DB_PREFIX_ . 'haluhome_videos` hss
			LEFT JOIN `' . _DB_PREFIX_ . 'haluhome` hs ON (hss.`id_haluhome_videos` = hs.`id_haluhome_videos`)
			WHERE hs.`id_shop` = ' . (int) $id_shop . ' AND hss.`position` > ' . (int) $this->position
        );

        foreach ($rows as $row) {
            $current_slide = new Halu_home($row['id_slide']);
            --$current_slide->position;
            $current_slide->update();
            unset($current_slide);
        }

        return true;
    }

    public static function getAssociatedIdsShop($id_slide)
    {
        $result = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_shop`
			FROM `' . _DB_PREFIX_ . 'haluhome` hs
			WHERE hs.`id_haluhome_videos` = ' . (int) $id_slide
        );

        if (!is_array($result)) {
            return false;
        }

        $return = [];

        foreach ($result as $id_shop) {
            $return[] = (int) $id_shop['id_shop'];
        }

        return $return;
    }
}