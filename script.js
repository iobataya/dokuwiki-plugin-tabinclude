jQuery(function(){
  jQuery('#jquery-tabs').tabs(
    {
      ajaxOptions: {dataFilter:function(data){
        //prevent call find() before the ajax request receives a response?
        jQuery(data).append('<br /><p>tabinclude</p>');

        // #explicit_container has a name of id or class of dokuwiki contents.
        // Find the parameter in configuration manager. default is .page
        var container_name = jQuery('#explicit_container').val();
        var bd = jQuery(data).find(container_name);
        return bd.html();}
      },
      cache: true
     }
  )
});