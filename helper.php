<?php
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class helper_plugin_tabinclude extends DokuWiki_Plugin {
    var $sort = ''; // sort key
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
                        'renderer'         => 'renderer',
                        'tabs'             => array('page'=>'page ID','title'=>'page title','error'=>'error msg'),
                        'init_page_idx'    => 'int',
                        'class (optional)' => 'string'),
                'return' => array('html' => 'string'),
        );
        $result[] = array(
                'name'   => 'renderTabsOdt',
                'desc'   => 'Render tabs ODT of DokuWiki pages',
                'params' => array(
                        'renderer' => 'renderer',
                        'tabs'     =>  array('page'=>'page ID','title'=>'page title','error'=>'error msg'),),
                        'return' => array('odt' => 'string'),
        );

        return $result;
    }



    /**
     * Get tab XHTML from pagenames
     */
    function renderTabsHtml(&$renderer,$tabs,$init_page_idx,$class='') {
        // render
        if($class) $class=' class="'.$class.'"';
        $html.= '<div id="dwpl-ti-container"'.$class.'>'.NL;
        $html.='<ul class="dwpl-ti">'.NL;
        $sz = count($tabs);
        for($i=0;$i<$sz;$i++){
            if(empty($tabs[$i]['error'])){
                $selected_class=($init_page_idx==$i)?' selected':'';
                $html.='<li class="dwpl-ti-tab"><div class="dwpl-ti-tab-title'.$selected_class.'" value="'.$tabs[$i]['page'].'">'.$tabs[$i]['title'].'</div></li>'.NL;
            }else{
                $html.='<li class="dwpl-ti-tab"><div class="dwpl-ti-tab-title error">'.$tabs[$i]['error'].'</div></li>'.NL;
            }
        }
        $html.= '</ul>'.NL;
        $html.='<div class="dwpl-ti-content-box">';
        if($this->getConf('hideloading')!=1){
            $html.='<div id="dwpl-ti-loading" class="dwpl-ti-loading">'.$this->getLang('loading').'</div>';
        }
        $html.='<div id="dwpl-ti-content" class="dwpl-ti-content">';

        $goto = $this->getLang('gotohere');
        $pagelink = tpl_link(wl($tabs[$init_page_idx]['page']),$goto,'',true);
        if($this->getConf('goto_link_header')!=0)
            $html.= '<div class="dwpl-ti-permalink-header">'.$pagelink.'</div>'.NL;
        $html.=tpl_include_page($tabs[$init_page_idx]['page'],false);
        if($this->getConf('goto_link_footer')!=0)
            $html.= '<div class="dwpl-ti-permalink-footer">'.$pagelink.'</div>'.NL;
        $html.= '</div></div>'.NL.'</div>'.NL;

        $renderer->doc.=$html;

        global $conf;
        if($conf['allowdebug']==1){
            $renderer->doc.="<!-- \n".print_r(array($tabs,$init_page_idx,$class),true)."\n -->";
        }

        return true;
    }
    /**
     * Get tab ODT from pagenames
     */
    function renderTabsOdt(&$renderer,$tabs){
        $renderer->strong_open();
        $renderer->doc.='Tab pages';
        $renderer->strong_close();
        $renderer->p_close();

        $renderer->listu_open();
        for($i=0;$i<$sz;$i++){
            $page = $tabs[$i]['page'];
            $title = $tabs[$i]['title'];
            $desc = p_get_metadata($page,'description');

            if(empty($tabs[$i]['error'])){
                $renderer->listitem_open();
                $renderer->p_open();
                $renderer->internallink($page,$title);
                $renderer->p_close();

                if(is_array($desc)){
                    $renderer->p_open();
                    $renderer->doc.=hsc($desc['abstract']);
                    $renderer->p_close();
                }
                $renderer->listitem_close();
            }else{
                $renderer->p_open();
                $renderer->doc.=$tabs[$i]['error'];
                $renderer->p_close();
            }
        }
        $renderer->listu_close();
        $renderer->p_open();

        global $conf;
        if($conf['allowdebug']==1){
            $renderer->doc.="<!-- \n".print_r(array($tabs,$init_page_idx,$class),true)."\n -->";
        }

        return true;
    }
}
