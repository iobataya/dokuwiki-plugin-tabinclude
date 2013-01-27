<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
@require_once(DOKU_PLUGIN.'syntax.php');
/**
 * Tab plugin using jQuery
 */
class syntax_plugin_tabinclude extends DokuWiki_Syntax_Plugin{
 /**
  * return some info
  */
  function getInfo(){
    return array(
    'author' => 'Ikuo Obataya',
    'email'  => 'I.Obataya@gmail.com',
    'date'   => '2013-01-27',
    'name'   => 'Tab control using jQuery UI',
    'desc'   => 'Create tab control using jQuery UI
                 {{tab>(page1),(page2),(page3)...}}',
    'url'    => 'http://symplus.edu-wiki.org/en/tabinclude_plugin',
    );
  }
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
  * Render tab control by using jQuery UI
  */
  function render($mode, &$renderer, $data) {
    global $conf;
    global $ID;
    list($state, $pages) = $data;
    $sz = count($pages);
    if($sz==0) return true;
    if ($mode=='xhtml'){
      ob_start();
      echo '<div id="jquery-tabs"><ul>'.NL;
      for($i=0;$i<$sz;$i++){
        $page = hsc(trim($pages[$i]));
        resolve_pageid(getNS($ID),$page,$exists);
        $title = p_get_metadata($page,'title');
        $title = empty($title)?$page:hsc(trim($title));
        echo '<li><a href="'.wl($pages[$i]).'"><span>'.$title.'</span></a></li>'.NL;
      }
      echo '</ul></div>';
      echo '<input id="explicit_container" type="hidden" value="'.$this->getConf('explicit_container').'"/>';
      echo '</div>'.NL;
      $renderer->doc.=ob_get_contents();

      ob_end_clean();
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