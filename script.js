/**
 * Refresh tab page
 * @param page
 */
function dwpl_ti_refresh(_this, page){
    if(page){
        var content = jQuery(_this).closest('#dwpl-ti-container').find('#dwpl-ti-content');
        var loading = jQuery(_this).closest('#dwpl-ti-container').find('#dwpl-ti-loading');
        // show loading
        content.css({visibility:'hidden'});
        loading.css({display:'block'});
        // POST AJAX
        jQuery.post(DOKU_BASE + 'lib/plugins/tabinclude/ajax.php', { call: 'content', page: page })
            .done(function(data) {
                // Refresh tab page
                content.html(data);
                loading.css({display:'none'});
                content.css({visibility:'visible'});
            });
    }
}
/**
 * Initial process
 */
function dwpl_ti_init(){
    // set event handler
    jQuery('ul.dwpl-ti li div.dwpl-ti-tab-title').each(function(){
        jQuery(this).click(function(){
            // unselect all tabs
            jQuery('.dwpl-ti-tab-title').removeClass('selected');
            // select a tab
            jQuery(this).addClass('selected');
            dwpl_ti_refresh(jQuery(this), jQuery(this).attr('value'));
        });
    });
}
jQuery(function(){dwpl_ti_init();});

