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

{if $homeslider.slides}
{*
  <div id="carousel" data-ride="carousel" class="carousel slide" data-interval="{$homeslider.speed}" data-wrap="{(string)$homeslider.wrap}" data-pause="{$homeslider.pause}" data-touch="true">
    <ol class="carousel-indicators">
      {foreach from=$homeslider.slides item=slide key=idxSlide name='homeslider'}
      <li data-target="#carousel" data-slide-to="{$idxSlide}"{if $idxSlide == 0} class="active"{/if}></li>
      {/foreach}
    </ol>
    <ul class="carousel-inner" role="listbox" aria-label="{l s='Carousel container' d='Shop.Theme.Global'}">
      {foreach from=$homeslider.slides item=slide name='homeslider'}
        <li class="carousel-item {if $smarty.foreach.homeslider.first}active{/if}" role="option" aria-hidden="{if $smarty.foreach.homeslider.first}false{else}true{/if}">
          {if !empty($slide.url)}<a href="{$slide.url}">{/if}
            <figure>
              <img src="{$slide.image_url}" alt="{$slide.legend|escape}" loading="lazy" width="1110" height="340">
              {if $slide.title || $slide.description}
                <figcaption class="caption">
                  <h2 class="display-1 text-uppercase">{$slide.title}</h2>
                  <div class="caption-description">{$slide.description nofilter}</div>
                </figcaption>
              {/if}
            </figure>
          {if !empty($slide.url)}</a>{/if}
        </li>
      {/foreach}
    </ul>
    <div class="direction" aria-label="{l s='Carousel buttons' d='Shop.Theme.Global'}">
      <a class="left carousel-control" href="#carousel" role="button" data-slide="prev" aria-label="{l s='Previous' d='Shop.Theme.Global'}">
        <span class="icon-prev hidden-xs" aria-hidden="true">
          <i class="material-icons">&#xE5CB;</i>
        </span>
      </a>
      <a class="right carousel-control" href="#carousel" role="button" data-slide="next" aria-label="{l s='Next' d='Shop.Theme.Global'}">
        <span class="icon-next" aria-hidden="true">
          <i class="material-icons">&#xE5CC;</i>
        </span>
      </a>
    </div>
  </div>
*}
<div class="main-galery-video slides-container">
  <div class="blocurivideo">
    <div class="video-text-block ">
     <h3 class="video-text-block__title">Halü by Titan</h3>
    </div>
    <video class="video-block slide" playsinline="" muted="" loop="" autoplay="" preload="metadata" data-src="http://localhost/superbecul/modules/ps_imageslider/images/first-video_banner.mp4" type="video/mp4" src="http://localhost/superbecul/modules/ps_imageslider/images/first-video_banner.mp4"></video>
  </div>
<div class="blocurivideo">
<div class="video-text-block video-text-block_bottom">
    <h3 class="video-text-block__title">SHOWROOM</h3>       
    <a href="#" class="video-text-block__btn video-text-block__btn_underhead" tabindex="0">Learn more</a>
</div>
<video class="video-block slide" playsinline="" muted="" loop="" autoplay="" preload="metadata" data-src="http://localhost/superbecul/modules/ps_imageslider/images/banner_video.mp4" type="video/mp4"  src="http://localhost/superbecul/modules/ps_imageslider/images/banner_video.mp4"></video>
</div>
<div class="blocurivideo">
  <div class="video-text-block video-text-block_bottom">
    <h3 class="video-text-block__title">OFERTE SPECIALE</h3>       
    <a href="#" class="video-text-block__btn video-text-block__btn_underhead" tabindex="0">Learn more</a>
</div>
<video class="video-block slide" playsinline="" muted="" loop="" autoplay="" preload="metadata" data-src="http://localhost/superbecul/modules/ps_imageslider/images/home-page-1.mp4" type="video/mp4" src="http://localhost/superbecul/modules/ps_imageslider/images/home-page-3.mp4"></video>
</div>
<div class="blocurivideo">
  <div class="video-text-block video-text-block_bottom">
    <h3 class="video-text-block__title">OFERTE DEZVOLTATORI</h3>       
    <a href="#" class="video-text-block__btn video-text-block__btn_underhead" tabindex="0">Learn more</a>
</div>
<video class="video-block slide" playsinline="" muted="" loop="" autoplay="" preload="metadata" data-src="http://192.168.5.185/superbecul/modules/ps_imageslider/images/home-page-1.mp4" type="video/mp4" src="http://localhost/superbecul/modules/ps_imageslider/images/home-page-4.mp4"></video>
</div>
</div>
{/if}
