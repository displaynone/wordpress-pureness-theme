<?php
require( dirname(__FILE__) . '/../../../../wp-load.php' );
$post_id = isset($_SERVER["HTTP_REFERER"])?preg_replace('#.*post=(\d+).*#', '$1', $_SERVER["HTTP_REFERER"]):0;
$data =  get_post_meta($post_id, 'crop_position', true);
if (!$data) {
  $post_thumbnail_id = get_post_thumbnail_id( $post_id );
  if ($post_thumbnail_id) {
    $thumb = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
    if ($thumb[1] > $thumb[2]) {
      $data = array(intval(($thumb[1]-$thumb[2])/2), 0, $thumb[2], $thumb[2]);
    } else {
      $data = array(intval(($thumb[2]-$thumb[1])/2), 0, $thumb[1], $thumb[1]);
    }
  }
}
?>
var crop;
<?php if ($data) { ?>    
var sel = [<?php echo implode(', ', $data); ?>];
<?php } else { ?>
var sel;
<?php } ?>
jQuery(document).ready(function() {
  jQuery('#edit-thumb').live('click', function() {
    var html = '<img id="pure_thumb_crop" src="'+jQuery('img.attachment-post-thumbnail').attr('src')+'" style="padding: 0px; margin: 0px;" />';
    console.log(html);
    jQuery(html).dialog( {title: "<?php _e('Modify thumbnail position', 'youare'); ?>", modal:true, width: <?php echo $thumb[1]; ?>, buttons: { "<?php _e('Confirm', 'youare'); ?>": function() {	jQuery( '#form-<?php echo $k; ?>' ).submit(); return false; }, "<?php _e('Cancel', 'youare'); ?>": function() { crop.destroy(); jQuery( this ).dialog( "close" ); } }});
    <?php if ($data) { ?>    
    crop = jQuery.Jcrop('#pure_thumb_crop', {aspectRatio: 1, minSize: [150, 150], setSelect: sel});
    <?php } else { ?>
    crop = jQuery.Jcrop('#pure_thumb_crop', {aspectRatio: 1, minSize: [150, 150]});
    <?php } ?>
    return false;
  });
});
