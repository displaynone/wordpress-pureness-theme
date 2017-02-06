jQuery(document).ready(function() {
  var WPSetThumbnailHTMLOld = WPSetThumbnailHTML;
  WPSetThumbnailHTML = function(html) {
    WPSetThumbnailHTMLOld(html);
    ajax_path.postID = html.match(/post_id=(\d+)/)[1];
    jQuery.post(	ajax_path.ajaxurl, {
        action : 'pure_thumbs_alert',
        postID : ajax_path.postID
      }, function( response ) {
        console.log( response );
        if (!response.ok) {
        jQuery('#postimagediv').pointer({
          content: response.message,
          position: 'right',
          open: function() {jQuery('.wp-pointer').css({zIndex: '10'});}
        }).pointer('open');
        }
      }
    );
  }
})


