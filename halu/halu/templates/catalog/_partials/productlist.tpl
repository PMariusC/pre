{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{capture assign="productClasses"}{if !empty($productClass)}{$productClass}{else}col-xs-12 col-sm-6 col-xl-4{/if}{/capture}

<div class="products{if !empty($cssClass)} {$cssClass}{/if}">
    {foreach from=$products item="product" key="position"}
        {include file="catalog/_partials/miniatures/product.tpl" product=$product position=$position productClasses=$productClasses}
    {/foreach}
{* delete this *}
{if $category.id == 3}
<div class="js-product product col-xs-12 col-sm-6 col-xl-4">
  <article class="product-miniature js-product-miniature reviews-loaded" data-id-product="21" data-id-product-attribute="0">
    <div class="thumbnail-container">
    <div class="thumbnail-top">      
        <a href="http://localhost/prestarn/en/produse/21-shure-se210-sound-isolating-earphones-for-ipod-and-iphone.html" class="thumbnail product-thumbnail">
          <picture>
            <img src="{$urls.img_url}/sale_banner.jpg" alt="Shure SE210 Sound-Isolating Earphones for iPod and iPhone" loading="lazy" data-full-size-image-url="{$urls.img_url}/sale_banner.jpg" width="" height="" style="max-height: 325px;width: 100%;">
          </picture>
        </a>
    </div>
    </div>
  </article>
</div>
{/if}
</div>
