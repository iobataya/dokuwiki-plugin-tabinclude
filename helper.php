<?php
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class helper_plugin_tabinclude extends DokuWiki_Plugin {
    var $sort       = '';      // sort key
    /**
     * Constructor
     */
    function helper_plugin_tabinclude() {
        global $conf;
        // load sort key from settings
        $this->sort = $this->getConf('sortkey');
    }

    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'renderTabsHtml',
                'desc'   => 'Render tabs HTML of DokuWiki pages',
                'params' => array(
                        'renderer'=>'renderer',
                        'pages' => array('id'=>'page ID','title'=>'page title','isSelected (optional)'=>'bool'),
                        'class (optional)' => 'string'),
                'return' => array('html' => 'string'),
        );
        $result[] = array(
                'name'   => 'renderTabsOdt',
                'desc'   => 'Render tabs ODT of DokuWiki pages',
                'params' => array(
                        'renderer',
                        'pages' => array('id'=>'page ID','title'=>'page title')
                        ,),
                'return' => array('odt' => 'string'),
        );

        return $result;
    }

    /**
     * Get tab XHTML from pagenames
     * pages_array[]['id'] : pagename
     * pages_array[]['title'] : title to display on tab
     * pages_array[]['isSelected'] : initial page or not
     *
     * $class: class name for CSS
     */
    function renderTabsHtml(&$renderer,$pages_array,$class='') {
        $cl = $class?' class="'.hsc($class).'"':'';
        $html = '<div id="dwpl-ti-container"'.$cl.'><ul class="dwpl-ti">';
        for($i=0;$i<count($pages_array);$i++){
            $isSel = $pages_array[$i]['isSelected']?' selected':'';
            $init_page = $pages_array[$i]['isSelected']?$pages_array[$i]['id']:$init_page;
            $html.='<li class="dwpl-ti-tab"><div class="dwpl-ti-tab-title'.$isSel.'" value="'.$pages_array[$i]['id'].'">'.$pages_array[$i]['title'].'</div></li>';
        }
        $html.= '</ul>'.NL;

        $html.= '<input id="dwpl-ti-initpage" type="hidden" value="'.$init_page.'"/>';
        $html.='<div class="dwpl-ti-content-box">';
        if($this->getConf('hideloading')!=1){
            $html.='<div id="dwpl-ti-loading" class="dwpl-ti-loading">'.$this->getLang('loading').'</div>';
        }
        $html.='<div id="dwpl-ti-content" class="dwpl-ti-content"></div>';
        $html.= '</div>'.NL.'</div>'.NL;
        $renderer->doc.=$html;
        return true;
    }
    /**
     * Get tab ODT from pagenames
     * pages_array[]['id'] : pagename
     * pages_array[]['title'] : title to display on tab
     *
     */
    function renderTabsOdt(&$renderer,$pages_array){
        $renderer->strong_open();
        $renderer->doc.='Tab pages';
        $renderer->strong_close();
        $renderer->p_close();
        $renderer->listu_open();
        for($i=0;$i<$sz;$i++){
            $page = hsc(trim($pages[$i]));
            resolve_pageid(getNS($ID),$page,$exists);
            $title = p_get_metadata($page,'title');
            $title = empty($title)?$page:hsc(trim($title));
            $abstract = p_get_metadata($page);
            $renderer->listitem_open();
            $renderer->p_open();
            $renderer->internallink($page,$title);
            $renderer->p_close();
            $renderer->p_open();
            if(is_array($abstract))
                $renderer->doc.=hsc($abstract['description']['abstract']);
            $renderer->p_close();
            $renderer->listitem_close();
        }
        $renderer->listu_close();
        $renderer->p_open();
        return true;
    }
}