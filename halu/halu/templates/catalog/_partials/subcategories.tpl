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
{*{if !empty($subcategories)}
  {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
    <div id="subcategories" class="card card-block">
      <h2 class="subcategory-heading">{l s='Subcategories' d='Shop.Theme.Category'}</h2>

      <ul class="subcategories-list">
        {foreach from=$subcategories item=subcategory}
          <li>
            <div class="subcategory-image">
              <a href="{$subcategory.url}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                {if !empty($subcategory.image.large.url)}
                  <picture>
                    {if !empty($subcategory.image.large.sources.avif)}<source srcset="{$subcategory.image.large.sources.avif}" type="image/avif">{/if}
                    {if !empty($subcategory.image.large.sources.webp)}<source srcset="{$subcategory.image.large.sources.webp}" type="image/webp">{/if}
                    <img
                      class="img-fluid"
                      src="{$subcategory.image.large.url}"
                      alt="{$subcategory.name|escape:'html':'UTF-8'}"
                      loading="lazy"
                      width="{$subcategory.image.large.width}"
                      height="{$subcategory.image.large.height}"/>
                  </picture>
                {/if}
              </a>
            </div>

            <h5>
              <a class="subcategory-name" href="{$subcategory.url}">
                {$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}
              </a>
            </h5>
            {if $subcategory.description}
              <div class="cat_desc">{$subcategory.description|unescape:'html' nofilter}</div>
            {/if}
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
{/if}*}

{if !empty($subcategories)}
  {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
    {foreach from=$subcategories item=subcategory}
    {if $subcategory.id_category == '4'}
      <div id="cat4test" class="subcategory-name">
    {else}
    <a href="{$subcategory.url}"><div class="subcategory-name">
    {/if} 
    <div class="subsubtitle">
        {$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}
          {if $subcategory.id_category == '4'}
            <span></span>
    </div>
            <div class="sub-sub-cathidden">
              <div class="products row">
                <div class="js-product product col-xs-12">
                  <article class="product-miniature js-product-miniature reviews-loaded">
                    <div class="thumbnail-container">
                      <a href="{$subcategory.url}">
                        <img src="{$urls.img_ps_url}cms/subcat-2.jpg">
                        <div class="subsub-desc">TITAN MAGNETIC</div>
                      </a>
                    </div>
                  </article>
                </div>
              </div>
            </div>
            <div class="sub-sub-cathidden">
              <div class="products row">
                <div class="js-product product col-xs-12">
                  <article class="product-miniature js-product-miniature reviews-loaded">
                    <div class="thumbnail-container">
                      <a href="{$subcategory.url}">
                        <img src="{$urls.img_ps_url}cms/subcat-1.jpg">
                        <div class="subsub-desc">TITAN MAGNETIC 5MM</div>
                      </a>
                    </div>
                  </article>
                </div>
              </div>
            </div>
            <div class="sub-sub-cathidden">
              <div class="products row">
                <div class="js-product product col-xs-12">
                  <article class="product-miniature js-product-miniature reviews-loaded">
                    <div class="thumbnail-container">
                      <a href="{$subcategory.url}">
                        <img src="{$urls.img_ps_url}cms/subcat-3.jpg">
                        <div class="subsub-desc">MAGNETIC ULTRA SLIM</div>
                      </a>
                    </div>
                  </article>
                </div>
              </div>
            </div>
          {/if}
    {if $subcategory.id_category == '4'}
      </div>
    {else}
      </div></a>
    {/if}
    {/foreach}
  {/if}
{/if}