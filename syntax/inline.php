<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
@require_once(DOKU_PLUGIN.'syntax.php');
/**
 * Tab plugin
 */
class syntax_plugin_tabinclude_inline extends DokuWiki_Syntax_Plugin{
  function getType(){ return 'substition'; }
  function getSort(){ return 158; }
  function connectTo($mode){$this->Lexer->addSpecialPattern('\{\{tabinclude>[^}]*\}\}',$mode,'plugin_tabinclude_inline');}
 /**
  * handle syntax
  */
  function handle($match, $state, $pos, &$handler){
    $match = substr($match,13,-2);
    $pages = explode(',',$match);

    $sz = count($pages);
    if($sz==0) return array();

    $pages_array = array();
    // loop for tabs
    for($i=0;$i<$sz;$i++){
        $page = trim($pages[$i]);
        if($i==0) $init_page = $page;
        $selected_class = '';
        $isSelected = false;
        if($page[0]=='*'){
            $page = substr($page,1);
            $init_page = $page;
            $isSelected = true;
        }
        if(strpos($page,'|')!==false){
            $items = explode('|',$page);
            list($page,$title) = $items;
        }else{
            $title = $page;
            resolve_pageid(getNS($ID),$page,$exists);
            $title = p_get_metadata($page,'title');
        }
        $title = empty($title)?$page:$title;

        $pages_array[] = array('id'=>$page,'title'=>$title,'isSelected'=>$isSelected);
    }

    return array($state,$pages_array);
  }
 /**
  * Render tab control
  */
  function render($mode, &$renderer, $data) {
    $helper = plugin_load('helper','tabinclude');
    list($state, $pages_array) = $data;
    if ($mode=='xhtml'){
        $helper->renderTabsHtml($renderer,$pages_array);
        return true;
    }else if($mode=='odt'){
        $helper->getOdtHtml($renderer,$pages_array);
        return true;
    }
    return false;
  }
}
?>
