<h3>{$title}</h3>
<div class="content serendipity_entry_body">{$content}</div>
{if 'adminUsersGroups'|checkPermission}
    <div class="serendipity_edit_nugget">
        <a href="{$serendipityBaseURL}serendipity_admin.php?serendipity[adminModule]=event_display&serendipity[adminAction]=simple_staticpage&editor=true&title={$title}">{$CONST.EDIT}</a>
    </div>
{/if}