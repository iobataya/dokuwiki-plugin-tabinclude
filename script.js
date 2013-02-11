/**
 * Refresh tab page
 * @param page
 */
function dwpl_ti_refresh(page){
  if(page){
    // show loading
    jQuery('#dwpl-ti-content').css({visibility:'hidden'});
    jQuery('#dwpl-ti-loading').css({display:'block'});
    // POST AJAX
    jQuery.post(DOKU_BASE + 'lib/plugins/tabinclude/ajax.php', { call: 'content', page: page })
      .done(function(data) {
        // Refresh tab page
        jQuery('#dwpl-ti-content').html(data);
        jQuery('#dwpl-ti-loading').css({display:'none'});
        jQuery('#dwpl-ti-content').css({visibility:'visible'});
      });
  }
}
/**
 * Initial process
 */
function dwpl_ti_showInitialPage(){
  // set event handler
  jQuery('ul.dwpl-ti li div.dwpl-ti-tab-title').each(function(){
    jQuery(this).click(function(){
      // unselect all tabs
      jQuery('.dwpl-ti-tab-title').removeClass('selected');
      // select a tab
      jQuery(this).addClass('selected');
      dwpl_ti_refresh(jQuery(this).attr('value'));
    })
  });
  // set initial page
  selectedpage = jQuery('#dwpl-ti-initpage');
  if(selectedpage){
    dwpl_ti_refresh(selectedpage.val());
  }
}
jQuery(function(){dwpl_ti_showInitialPage();});

