<?php
/*
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Ikuo Obataya (i.obataya[at]gmail.com)
 */
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
    global $ID;
    $match = substr($match,13,-2);
    $pages = explode(',',$match);
    $sz = count($pages);
    if($sz==0) return array();

    // loop for tabs
    $tabs = array();
    $init_page_idx = 0; // initial page index
    for($i=0;$i<$sz;$i++){
        // Put page ID into $page
        // Put text in tab into $title
        $title='';
        $page = trim($pages[$i]);
        if($page[0]=='*'){
            $init_page_idx=count($tabs);
            $page = substr($page,1);
        }
        $items = explode('|',$page);
        if(count($items)>1){
            list($page,$title)=$items;
        }

        // Show namespace(s) of page name in tab ?
        resolve_pageid(getNS($ID),$page,$exists);
        if($title==''){
            $title = $this->getConf('namespace_in_tab')?$page:noNS($page);
        }
        // Show first heading as tab name ?
        if($this->getConf('use_first_heading')){
            $meta_title= p_get_metadata($page,'title');
            if($meta_title!=''){
                $title = $meta_title;
            }
        }

        // Check errors
        if(page_exists($page)==false){
            // page in tab exists ?
            $tabs[] = array('error'=>$this->getLang('error_notfound'));
        }else if($ID==$page){
            // page is identical to parent ?
            $tabs[] = array('error'=>$this->getLang('error_parent'));
        }else{
            $tabs[] = array('page'=>hsc($page),'title'=>hsc($title));
        }
    }
    return array($state,$tabs,$init_page_idx,"");
  }
 /**
  * Render tab control
  */
  function render($mode, &$renderer, $data) {
      $helper = plugin_load('helper','tabinclude');
      list($state, $tabs,$init_page_idx,$class) = $data;
      if ($mode=='xhtml'){
          $helper->renderTabsHtml($renderer,$tabs,$init_page_idx,$class);
          return true;
      }else if($mode=='odt'){
          $helper->getOdtHtml($renderer,$tabs);
          return true;
      }
      return false;
  }
}
?>
