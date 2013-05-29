<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
@require_once(DOKU_PLUGIN.'syntax.php');
/**
 * Tab plugin
 */
class syntax_plugin_tabinclude extends DokuWiki_Syntax_Plugin{
  function getType(){ return 'substition'; }
  function getSort(){ return 158; }
  function connectTo($mode){$this->Lexer->addSpecialPattern('\{\{tabinclude>[^}]*\}\}',$mode,'plugin_tabinclude');}
 /**
  * handle syntax
  */
  function handle($match, $state, $pos, &$handler){
    $match = substr($match,13,-2);
    $pages = explode(',',$match);
    return array($state,$pages);
  }
 /**
  * Render tab control
  */
  function render($mode, &$renderer, $data) {
    global $ID;

    if ($mode=='xhtml'){
        list($state, $pages) = $data;
        $sz = count($pages);
        if($sz==0) return true;
        $html.= '<div id="dwpl-ti-container">'.NL;

        // loop for tabs
        $html.='<ul class="dwpl-ti">'.NL;
        for($i=0;$i<$sz;$i++){
          $page = trim($pages[$i]);
          if($i==0) $init_page = $page;
          $selected_class = '';
          if($page[0]=='*'){
            $page = substr($page,1);
            $init_page = $page;
            $selected_class = ' selected';
          }
          if(strpos($page,'|')!==false){
            $items = explode('|',$page);
            $page = $items[0];
            $title = $items[1];
          }else{
            $title = $page;
            $title = p_get_metadata($page,'title');
          }
          resolve_pageid(getNS($ID),$page,$exists);
          $title = empty($title)?$page:$title;
          $html.='<li class="dwpl-ti-tab"><div class="dwpl-ti-tab-title'.$selected_class.'" value="'.$page.'">'.$title.'</div></li>'.NL;
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
    }else if($mode=='odt'){
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
    return false;
  }
}
?>
