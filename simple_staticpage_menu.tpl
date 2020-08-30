<script src="{$serendipityBaseURL}index.php?/plugin/simple_staticpage_menu.js" ></script>
<link rel="stylesheet" href="{$serendipityBaseURL}index.php?/plugin/simple_staticpage_menu.css"></link>

<h3>{$CONST.PLUGIN_EVENT_SIMPLE_STATICPAGE_NAME}</h3>
<form method="GET" >
    <input type="hidden" name="serendipity[adminModule]" value="event_display" />
    <input type="hidden" name="serendipity[adminAction]" value="simple_staticpage" />
    <input type="hidden" name="editor" value="true" />
    <input type="submit" value="New Page" class="serendipityPrettyButton input_button"></input>
</form>

{if {$pages[0].title} }
    <ol>
        {foreach $pages as $page}
            <li>{$page.title}
                <form method="GET">
                    <input type="hidden" name="serendipity[adminModule]" value="event_display" />
                    <input type="hidden" name="serendipity[adminAction]" value="simple_staticpage" />
                    <input type="hidden" name="editor" value="true" />
                    <input type="hidden" name="title" value="{$page.title}" />
                    <button type="submit"><img src="{serendipity_getFile file="admin/img/edit.png"}" /></button>
                </form>
                <form method="POST" action="{$serendipityBaseURL}index.php?/plugin/simple_staticpage_delete" class="pageDelete">
                    <input type="hidden" name="title" value="{$page.title}" />
                    <button type="submit"><img src="{serendipity_getFile file="admin/img/delete.png"}" /></button>
                </form>
            </li>
        {/foreach}
    </ol>
{/if}
