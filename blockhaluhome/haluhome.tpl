{**
 * @author Parfene Marius <parfenemariuscatalin@gmail.com>
 *}
{if $haluhome.videos}
  <div class="main-galery-video slides-container">
    {foreach from=$haluhome.videos item=video name='haluhome'}
      <div class="blocurivideo">
        <div class="video-text-block{if $video.position != 1} video-text-block_bottom{/if}">
          <h3 class="video-text-block__title">{$video.title}</h3>
          {if isset($video.legend) && $video.legend != ''}<a href="{$video.url}" class="video-text-block__btn video-text-block__btn_underhead" tabindex="0">{$video.legend}</a>{/if}
        </div>
        <video class="video-block slide" playsinline="" muted="" loop="" autoplay="" preload="metadata" data-src="{$video.video_url}" type="video/mp4" src="{$video.video_url}"></video>
      </div>
    {/foreach}
  </div>
{/if}
