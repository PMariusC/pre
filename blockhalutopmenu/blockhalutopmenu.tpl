{if $MENU != ''}
    {if $MENU_SEARCH}
        <div class="searchbox-mobile">
            <form id="searchbox-mobile" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
                <p>
                    <input type="hidden" name="controller" value="search"/>
                    <input type="hidden" value="position" name="orderby"/>
                    <input type="hidden" value="desc" name="orderway"/>
                    <input type="text" class="halu_search_query" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}"/>
                </p>
            </form>
        </div>
    {/if}
    <div id="_desktop_top_menu" class="menu js-top-menu position-static hidden-sm-down">
        <div class="cat-title">{l s='Menu' mod='blockhalutopmenu'}</div>
         <ul class="top-menu" id="top-menu" data-depth="0">
            {$MENU nofilter}
            {if $MENU_SEARCH}
                <li class="sf-search noBack" {*style="float:right"*}>
                    <form id="searchbox" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
                        <p>
                            <input type="hidden" name="controller" value="search"/>
                            <input type="hidden" value="position" name="orderby"/>
                            <input type="hidden" value="desc" name="orderway"/>
                            <input type="text" class="halu_search_query" name="search_query" placeholder="{l s='Search' mod='blockhalutopmenu'}" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}"/>
                        </p>
                    </form>
                </li>
            {/if}
            {$MENUEXTRALINKS nofilter}
            <li class="separator">|</li>
         </ul>
    </div>
{/if}