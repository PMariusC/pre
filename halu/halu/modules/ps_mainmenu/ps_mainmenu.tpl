{assign var=_counter value=0}
{function name="menu" nodes=[] depth=0 parent=null}
    {if $nodes|count}
      <ul class="top-menu" {if $depth == 0}id="top-menu"{/if} data-depth="{$depth}">
        {foreach from=$nodes item=node}
            <li class="{$node.type}{if $node.current} current {/if}" id="{$node.page_identifier}">
            {assign var=_counter value=$_counter+1}
              <a
                class="{if $depth >= 0}dropdown-item{/if}{if $depth === 1} dropdown-submenu{/if}"
                href="{$node.url}" data-depth="{$depth}"
                {if $node.open_in_new_window} target="_blank" {/if}
                {if $node.page_identifier == "lnk-search"}style="padding: 0 10px 0 20px;" {/if}
              >
                {if $node.children|count}
                  {* Cannot use page identifier as we can have the same page several times *}
                  {assign var=_expand_id value=10|mt_rand:100000}
                  <span class="float-xs-right hidden-md-up">
                    <span data-target="#top_sub_menu_{$_expand_id}" data-toggle="collapse" class="navbar-toggler collapse-icons">
                      <i class="material-icons add">&#xE313;</i>
                      <i class="material-icons remove">&#xE316;</i>
                    </span>
                  </span>
                {/if}
                {$node.label}
              </a>
              {if $node.page_identifier == "lnk-search"}<img src="http://localhost/superbecul/themes/halu/search.svg" alt="" class="main-nav__search-icon" style="margin-right: 10px;">{/if}
              {if $node.children|count}
              <div {if $depth === 0} class="popover sub-menu js-sub-menu collapse"{else} class="collapse"{/if} id="top_sub_menu_{$_expand_id}">
                {menu nodes=$node.children depth=$node.depth parent=$node}
              </div>
              {/if}
            </li>
        {/foreach}
         {assign var=_lastelement value=end($nodes)}
         
        {if $node == $_lastelement}
         <li class="separator">|</li>
        {/if}
      </ul>
    {/if}
{/function}

<div class="menu js-top-menu position-static hidden-sm-down" id="_desktop_top_menu">
    {menu nodes=$menu.children}
    <div class="clearfix"></div>
</div>
{if $page.page_name == "category" OR $page.page_name == "product"}
{if $product.id_product eq '16'}
  <script>
    document.addEventListener('scroll', updateSidebarPosition);
    function updateSidebarPosition() {
      var footer = document.querySelector('footer');
      var sidebar = document.querySelector('.forprodsa');
      var footerRect = footer.getBoundingClientRect();
    
      if (footerRect.top < window.innerHeight) {
        sidebar.style.position = 'absolute';
        sidebar.style.bottom = '0';
        sidebar.style.right = '0';
      } else {
        sidebar.style.position = 'fixed';
        sidebar.style.bottom = '';
        sidebar.style.right = '0';
      }
    } 
  </script>
{else}
  <script>
    let lastScrollTop = 0;
    const menu = document.querySelector('.header-top');
    const threshold = 100;
    window.addEventListener('scroll', () => {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
       if (scrollTop > lastScrollTop && scrollTop > threshold) {
         menu.style.top = '-100px';
      } else if (scrollTop <= threshold) {
        menu.style.top = '0';
      }  else {
        menu.style.top = '0';
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, false);
  </script>
{/if}
{/if}

