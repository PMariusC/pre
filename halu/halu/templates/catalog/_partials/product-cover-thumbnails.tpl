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
 {if $product.id_product == '15' || $product.id_product == '16'} 
  {block name='product_images'}
  <script>
        function changeImageSrc(element) {
            var newSrc = element.getAttribute("data-image-large-sources");
            document.getElementById("changemodal").setAttribute("src", newSrc);
        }
        document.addEventListener("DOMContentLoaded", function() {
            var childDivs = document.getElementsByClassName("images-container2");
            var showMoreButton = document.getElementById("showMoreButton");
            var showLessButton = document.getElementById("showLessButton");
            // Hide all but the first 4 child divs
            for (var i = 4; i < childDivs.length; i++) {
                childDivs[i].classList.add("hideme");
            }

            showMoreButton.addEventListener("click", function() {
                // Show all hidden child divs
                for (var i = 4; i < childDivs.length; i++) {
                    childDivs[i].classList.remove("hideme");
                }
                // Hide the "Show More" button and show the "Show Less" button
                showMoreButton.style.display = "none";
                showLessButton.style.display = "inline-block";
                updateSidebarPosition();
            });

            showLessButton.addEventListener("click", function() {
                // Hide all child divs beyond the first 4
                for (var i = 4; i < childDivs.length; i++) {
                    childDivs[i].classList.add("hideme");
                }
                // Show the "Show More" button and hide the "Show Less" button
                showMoreButton.style.display = "inline-block";
                showLessButton.style.display = "none";
                updateSidebarPosition();
            });

        });
    </script>
    {foreach from=$product.images item=image}
      <div class="images-container2 js-images-container">
         <div class="product-cover">
          <picture>
            {if !empty($image.bySize.small_default.sources.avif)}<source srcset="{$image.bySize.small_default.sources.avif}" type="image/avif">{/if}
            {if !empty($image.bySize.small_default.sources.webp)}<source srcset="{$image.bySize.small_default.sources.webp}" type="image/webp">{/if}
              <img
            class="js-qv-product-cover img-fluid"
            src="{$image.bySize.large_default.url}"
            {if !empty($product.default_image.legend)}
              alt="{$product.default_image.legend}"
              title="{$product.default_image.legend}"
            {else}
              alt="{$product.name}"
            {/if}
            loading="lazy"
            width="{$product.default_image.bySize.large_default.width}"
            height="{$product.default_image.bySize.large_default.height}"
          >
          </picture>
           <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal" onclick="changeImageSrc(this)"
           data-image-large-src="{$image.bySize.large_default.url}"
            {if !empty($image.bySize.large_default.sources)}data-image-large-sources="{$image.bySize.large_default.sources.jpg}"{/if}
           >
              <i class="material-icons zoom-in">search</i>
           </div>
         </div>
      </div>
    {/foreach}
  {/block}
  <button id="showMoreButton" class="btn-primary product-form-save-button btn btn">Show More</button>
  <button id="showLessButton" class="btn-primary product-form-save-button btn btn" style="display:none;">Show Less</button>
  {hook h='displayAfterProductThumbs' product=$product}
 {else}
 {*original*}
<div class="images-container js-images-container">
  {block name='product_cover'}
    <div class="product-cover">
      {if $product.default_image}
        <picture>
          {if !empty($product.default_image.bySize.large_default.sources.avif)}<source srcset="{$product.default_image.bySize.large_default.sources.avif}" type="image/avif">{/if}
          {if !empty($product.default_image.bySize.large_default.sources.webp)}<source srcset="{$product.default_image.bySize.large_default.sources.webp}" type="image/webp">{/if}
          <img
            class="js-qv-product-cover img-fluid"
            src="{$product.default_image.bySize.large_default.url}"
            {if !empty($product.default_image.legend)}
              alt="{$product.default_image.legend}"
              title="{$product.default_image.legend}"
            {else}
              alt="{$product.name}"
            {/if}
            loading="lazy"
            width="{$product.default_image.bySize.large_default.width}"
            height="{$product.default_image.bySize.large_default.height}"
          >
        </picture>
        <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
          <i class="material-icons zoom-in">search</i>
        </div>
      {else}
        <picture>
          {if !empty($urls.no_picture_image.bySize.large_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.avif}" type="image/avif">{/if}
          {if !empty($urls.no_picture_image.bySize.large_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.webp}" type="image/webp">{/if}
          <img
            class="img-fluid"
            src="{$urls.no_picture_image.bySize.large_default.url}"
            loading="lazy"
            width="{$urls.no_picture_image.bySize.large_default.width}"
            height="{$urls.no_picture_image.bySize.large_default.height}"
          >
        </picture>
      {/if}
    </div>
  {/block}

  {block name='product_images'}
    <div class="js-qv-mask mask">
      <ul class="product-images js-qv-product-images">
        {foreach from=$product.images item=image}
          <li class="thumb-container js-thumb-container">
            <picture>
              {if !empty($image.bySize.small_default.sources.avif)}<source srcset="{$image.bySize.small_default.sources.avif}" type="image/avif">{/if}
              {if !empty($image.bySize.small_default.sources.webp)}<source srcset="{$image.bySize.small_default.sources.webp}" type="image/webp">{/if}
              <img
                class="thumb js-thumb {if $image.id_image == $product.default_image.id_image} selected js-thumb-selected {/if}"
                data-image-medium-src="{$image.bySize.medium_default.url}"
                {if !empty($image.bySize.medium_default.sources)}data-image-medium-sources="{$image.bySize.medium_default.sources|@json_encode}"{/if}
                data-image-large-src="{$image.bySize.large_default.url}"
                {if !empty($image.bySize.large_default.sources)}data-image-large-sources="{$image.bySize.large_default.sources|@json_encode}"{/if}
                src="{$image.bySize.small_default.url}"
                {if !empty($image.legend)}
                  alt="{$image.legend}"
                  title="{$image.legend}"
                {else}
                  alt="{$product.name}"
                {/if}
                loading="lazy"
                width="{$product.default_image.bySize.small_default.width}"
                height="{$product.default_image.bySize.small_default.height}"
              >
            </picture>
          </li>
        {/foreach}
      </ul>
    </div>
  {/block}
{hook h='displayAfterProductThumbs' product=$product}
</div>
{/if}