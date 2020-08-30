<script src="{$serendipityBaseURL}index.php?/plugin/simple_staticpage_editor.js"></script>
<h3>{$CONST.PLUGIN_EVENT_SIMPLE_STATICPAGE_NAME}</h3>
<form method="POST" action="{$serendipityBaseURL}index.php?/plugin/simple_staticpage_save">
    <input type="hidden" name="oldTitle" value="{$title}" />
    <ul class="plainList">
        <li><label>Titel: <input  id="entryTitle" class="input_textbox" name="title" type="text" value="{$title}"></input></label><input type="hidden" name="publishstatus" value="1" /></li>
        <input type="hidden" name="oldTitle" value="{$title}" />
        <li><textarea id="serendipity[body]" name="content" cols="80" rows="20">{$content}</textarea></li>
        <li><input type="button" id="preview" class="serendipityPrettyButton input_button" value="{$CONST.PREVIEW}"></input></li>
        <li><input type="submit" class="serendipityPrettyButton input_button" value="{$CONST.SAVE}"></input></li>
    </ul>
</form>

