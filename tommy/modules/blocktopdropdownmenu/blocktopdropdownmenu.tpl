{if $MENU != ''}
    <!-- Menu -->
    {if $MENU_SEARCH}
        <div class="searchbox-mobile">
            <form id="searchbox-mobile" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
                <p>
                    <input type="hidden" name="controller" value="search"/>
                    <input type="hidden" value="position" name="orderby"/>
                    <input type="hidden" value="desc" name="orderway"/>
                    <input type="text" class="mypresta_search_query" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}"/>
                </p>
            </form>
        </div>
    {/if}
    <div id="block_top_menu" class="sf-contener clearfix col-lg-12">
        <div class="cat-title">{l s='Menu' mod='blocktopdropdownmenu'}<span>{renderLogo}</span></div>
        <ul class="sf-menu clearfix menu-content">
                <li class="mobliehome"><a href="{$urls.base_url}" title="Home">Home</a></li>
            {$MENU nofilter}
            {if $MENU_SEARCH}
                <li class="sf-search noBack">
                    <form id="searchbox" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
                        <p>
                            <input type="hidden" name="controller" value="search"/>
                            <input type="hidden" value="position" name="orderby"/>
                            <input type="hidden" value="desc" name="orderway"/>
                            <input type="text" class="mypresta_search_query" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}"/>
                        </p>
                    </form>
                </li>
            {/if}
        </ul>
    </div>
    <!--/ Menu -->
{/if}