<?php
/*
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Satoshi Sahara (sahara.satoshi[at]gmail.com
 * @author     Ikuo Obataya (i.obataya[at]gmail.com)
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
@require_once(DOKU_PLUGIN.'syntax.php');
/**
 * Yet another sytax component for Tab plugin,
 * provided by satoshi.sahara@gmail.com
*/
class syntax_plugin_tabinclude_lines extends DokuWiki_Syntax_Plugin{
    function getType(){ return 'substition'; }
    function getSort(){ return 158; }
    function connectTo($mode){
        $this->Lexer->addSpecialPattern('<tabbed.+?</tabbed>', $mode, 'plugin_tabinclude_lines');
    }
    /**
     * handle syntax
     */
    function handle($match, $state, $pos, &$handler){
        global $ID;  //SAHARA : required for resolve_pageid()
        $match = substr($match,7,-9);  // strip markup
        list($class, $match) = explode('>', $match); // extract class
        $pages = explode("\n", $match);
        $sz = count($pages);
        if($sz==0) return array();

        // loop for tabs
        $tabs = array();
        $init_page_idx = 0;  // initial page index
        for($i=0; $i<$sz; $i++){
            // Put page ID into $page
            // Put text in tab into $title
            $page = trim($pages[$i]);

            if (preg_match('/\[\[(.+?)\]\]/', $page, $match)){
                // Pagelist like syntax,
                // each tabbed page is provided as DokuWiki unordered list syntax:
                // *[[id|title]] or **[[id|title]].
                $p = substr($page,0,strpos($page,'[['));
                $page = $match[1];
                if($page=='') continue;
                if (strpos($p,'*') !== strrpos($p,'*')) {
                    // multiple '*' means initial page!!
                    $init_page_idx = count($tabs);
                }
            } else {
                // original syntax
                $asterisk = false;
                while($page[0]=='*'){
                    $page = substr($page,1);
                    $asterisk = true;
                }
                if($page=='') continue;
                if($asterisk) $init_page_idx = count($tabs);
            }
            list($page, $title) = explode('|', $page, 2);
            list($page ,$section) = explode('#', $page, 2);
            if($page=='') continue;

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
        return array($state,$tabs,$init_page_idx,hsc(trim($class)));
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
