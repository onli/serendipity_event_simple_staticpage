<?php

if (IN_serendipity !== true) {
    die ("Don't hack!");
}


// Probe for a language include with constants. Still include defines later on, if some constants were missing
$probelang = dirname(__FILE__) . '/' . $serendipity['charset'] . 'lang_' . $serendipity['lang'] . '.inc.php';
if (file_exists($probelang)) {
    include $probelang;
}

include dirname(__FILE__) . '/lang_en.inc.php';

class serendipity_event_simple_staticpage extends serendipity_event {
    var $title = PLUGIN_EVENT_SIMPLE_STATICPAGE_NAME;

    function introspect(&$propbag) {
        global $serendipity;

        $propbag->add('name',          PLUGIN_EVENT_SIMPLE_STATICPAGE_NAME);
        $propbag->add('description',   PLUGIN_EVENT_SIMPLE_STATICPAGE_DESC);
        $propbag->add('stackable',     false);
        $propbag->add('author',        'Malte Paskuda');
        $propbag->add('version',       '0.1');
        $propbag->add('requirements',  array(
            'serendipity' => '1.7'
        ));
        $propbag->add('event_hooks',   array (
                                            'external_plugin'                                           => true,
                                            'backend_sidebar_entries'                                   => true,
                                            'backend_sidebar_entries_event_display_simple_staticpage'   => true,
                                            'genpage'                                                   => true,
                                            'entries_header'                                            => true,
                                            'entry_display'                                             => true
                                            )
                                        );
        $propbag->add('groups', array('MARKUP'));
    }

    function generate_content(&$title) {
        $title = $this->title;
    }


    /*function introspect_config_item($name, &$propbag) {
        
    }*/


    function event_hook($event, &$bag, &$eventData, $addData = null) {
        global $serendipity;

        $hooks = &$bag->get('event_hooks');

        if (isset($hooks[$event])) {
            switch($event) {
                case 'backend_sidebar_entries':
                    if (!serendipity_checkPermission('adminEntries')) {
                        break;
                    }
                    if ($this->get_config('menu', true)) {
                        echo '<li class="serendipitySideBarMenuLink serendipitySideBarMenuEntryLinks">
                            <a href="?serendipity[adminModule]=event_display&serendipity[adminAction]=simple_staticpage">
                                '. PLUGIN_EVENT_SIMPLE_STATICPAGE_NAME .'
                            </a>
                        </li>';
                    }
                    return true;
                    break;
                 case 'backend_sidebar_entries_event_display_simple_staticpage':
                    if (!serendipity_checkPermission('adminEntries')) {
                        break;
                    }
                    $this->setupDB();
                    if ($_GET['editor'] == true) {
                        $this->smarty_show('simple_staticpage_editor.tpl', $this->getPage($_GET['title']));
                    } else {
                        $this->smarty_show('simple_staticpage_menu.tpl', array('pages' => $this->getPages()));
                    }
                    return true;
                    break;
                case 'external_plugin':
                    switch ($eventData) {
						case 'simple_staticpage_save':
                            if (!serendipity_checkPermission('adminEntries')) {
                                break;
                            }
                            $success = $this->savePage($_POST['title'], $_POST['content'], $_POST['publishstatus']);
                            if ($success) {
                                if ($_POST['oldTitle'] != $_POST['title']) {
                                    $this->deletePage($_POST['oldTitle']);
                                }
                            }
                            return true;
                            break;
                        case 'simple_staticpage_delete':
                            if (!serendipity_checkPermission('adminEntries')) {
                                break;
                            }
                            $this->deletePage($_POST['title']);
                            return true;
                            break;
                        case 'simple_staticpage_menu.js':
                            header('Content-Type: text/javascript');
                            echo file_get_contents(dirname(__FILE__). '/simple_staticpage_menu.js');
                            break;
                        case 'simple_staticpage_editor.js':
                            header('Content-Type: text/javascript');
                            echo file_get_contents(dirname(__FILE__). '/simple_staticpage_menu.js');
                            break;
                        case 'simple_staticpage_menu.css':
                            header('Content-Type: text/css');
                            echo file_get_contents(dirname(__FILE__). '/simple_staticpage_menu.css');
                            break;
                        default:
                            return false;
                    }
                    return true;
                    break;
                case 'genpage':
                    $args = implode('/', serendipity_getUriArguments($eventData, true));
                    if ($serendipity['rewrite'] != 'none') {
                        $nice_url = $serendipity['serendipityHTTPPath'] . $args;
                    } else {
                        $nice_url = $serendipity['serendipityHTTPPath'] . $serendipity['indexFile'] . '?/' . $args;
                    }
                    if (empty($serendipity['GET']['subpage'])) {
                        $serendipity['GET']['subpage'] = $nice_url;
                    }
                    if (($page = $this->selected()) != null) {
                        $serendipity['head_title']    = $page['title'];
                        $serendipity['head_subtitle'] = htmlspecialchars($serendipity['blogTitle']);
                    }
                    break;
                case 'entries_header':
                    if (($page = $this->selected()) != null) {
                        $entry['body'] = $page['content'];
                        serendipity_plugin_api::hook_event('frontend_display', $entry, $addData);
                        $page['content'] = $entry['body'];
                        $this->smarty_show('simple_staticpage.tpl', $page);
                    }
                case 'entry_display':
                    if ($this->selected() != null) {
                        if (is_array($eventData)) {
                            $eventData['clean_page'] = true; // This is important to not display an entry list!
                        } else {
                            $eventData = array('clean_page' => true);
                        }
                    }
                    return true;
                break;
                    
                default:
                    return false;
            }
        } else {
            return false;
        }
    }

    function setupDB() {
        global $serendipity;
        $sql = "CREATE TABLE IF NOT EXISTS
                {$serendipity['dbPrefix']}simple_staticpage (
                    title id PRIMARY KEY,
                    title varchar(255) UNIQUE,
                    content text,
                    publishstatus int(1) default 0)";
        serendipity_db_query($sql);
    }

    function selected() {
        global $serendipity;
        
        $pagetitles = explode('/', $serendipity['GET']['subpage']);
        if ($pagetitles[2] == "pages") {
            $staticpages = $this->getPages();
            foreach($staticpages as $staticpage) {
                if ($staticpage['title'] == urldecode($pagetitles[3])) {
                    return $this->getPage($staticpage['title']);
                }
            }
        }
        return null;
    }

    function savePage($title, $content, $publishstatus) {
        global $serendipity;
        $sql = "INSERT INTO
                    {$serendipity['dbPrefix']}simple_staticpage(title, content, publishstatus)
                VALUES
                    ('". serendipity_db_escape_string($title) ."', '". serendipity_db_escape_string($content) ."', ". serendipity_db_escape_string($publishstatus).");";
        serendipity_db_query($sql);
        $sql = "UPDATE
                    {$serendipity['dbPrefix']}simple_staticpage
                SET
                    content = '". serendipity_db_escape_string($content) ."',
                    publishstatus = ". serendipity_db_escape_string($publishstatus)."
                WHERE
                    title = '". serendipity_db_escape_string($title) ."';";
        return serendipity_db_query($sql);
    }


    function getPage($title) {
        global $serendipity;
        $sql = "SELECT content, title, publishstatus FROM {$serendipity['dbPrefix']}simple_staticpage WHERE title = '". serendipity_db_escape_string($title)."';";
        return serendipity_db_query($sql, true, 'assoc');
    }
    
    function deletePage($title) {
        global $serendipity;
        $sql = "DELETE FROM {$serendipity['dbPrefix']}simple_staticpage WHERE title = '". serendipity_db_escape_string($title)."';";
        return serendipity_db_query($sql);
    }

    function getPages() {
        global $serendipity;
        $sql = "SELECT title, publishstatus FROM {$serendipity['dbPrefix']}simple_staticpage ORDER BY title;";
        return serendipity_db_query($sql, false, 'assoc');
    }

    /* Render a smarty-template
     * $template: path to the template-file
     * $data: map with the variables to assign
     * */
    function smarty_show($template, $data = null) {
        global $serendipity;

        if (!headers_sent()) {
            header('HTTP/1.0 200');
            header('Status: 200 OK');
        }
        
        if (!is_object($serendipity['smarty'])) {
            serendipity_smarty_init();
        }
        
        $serendipity['smarty']->assign($data);
        
        $tfile = serendipity_getTemplateFile($template, 'serendipityPath');

        if ($tfile == $template) {
            $tfile = dirname(__FILE__) . "/$template";
        }
        $inclusion = $serendipity['smarty']->security_settings[INCLUDE_ANY];
        $serendipity['smarty']->security_settings[INCLUDE_ANY] = true;
        $content = $serendipity['smarty']->fetch('file:'. $tfile);
        $serendipity['smarty']->security_settings[INCLUDE_ANY] = $inclusion;

        echo $content;
    }

    function debugMsg($msg) {
        global $serendipity;
        
        $this->debug_fp = @fopen ( $serendipity ['serendipityPath'] . 'templates_c/simple_staticpage.log', 'a' );
        if (! $this->debug_fp) {
            return false;
        }
        
        if (empty ( $msg )) {
            fwrite ( $this->debug_fp, "failure \n" );
        } else {
            fwrite ( $this->debug_fp, print_r ( $msg, true ) );
        }
        fclose ( $this->debug_fp );
    }

}

/* vim: set sts=4 ts=4 expandtab : */
?>
