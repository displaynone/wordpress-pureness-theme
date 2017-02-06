<?php
require( dirname(__FILE__) . '/../../../../wp-load.php' );
global $wp_registered_sidebars;

$cats = get_categories(array());
$pages = get_pages(array());
?>
jQuery(document).ready(function() {
var sidebar = null;
<?php
header('Content-type: text/javascript');
foreach($wp_registered_sidebars as $k=>$side) { ?>
sidebar = jQuery('#<?php echo $k; ?>');
<?php if (isset($side['pageable']) && $side['pageable']) { 
$side_opt = get_option('sidebar_'.$k);
//var_dump($side_opt);
$html = '<style>.inactive_item {color: #666; font-style: italic;}</style><form id="form-'.$k.'" action="'.get_template_directory_uri().'/functions/sidebar-widgets.php"><input type="hidden" name="sidebar_id" value="'.$k.'" /><input type="hidden" name="pure_sidebar" value="true" />';
$html .= '<p><input name="posts" type="checkbox" '.($side_opt['posts']? 'checked="ckeched"':'').'/> <strong>'.__('All posts', 'pureness').'</strong> ';
$html .= '<input name="allpages" type="checkbox" '.($side_opt['allpages']? 'checked="ckeched"':'').'/> <strong>'.__('All pages', 'pureness').'</strong> ';
$html .= '<input name="categories" type="checkbox" '.($side_opt['categories']? 'checked="ckeched"':'').'/> <strong>'.__('All categories', 'pureness').'</strong></p>';
$html .= '<h4>'.__('Pages', 'pureness').' <small style="float: right"><input id="selPag-'.$k.'" type="button" value="'.__('Select all', 'pureness').'"/></small></h4><div id="pages-'.$k.'" style="overflow: auto; height: 150px">';
foreach($pages as $p) {
  $html .= '<p '.($side_opt['allpages']? 'class="inactive_item"':'').' ><input type="checkbox" name="page_'.$p->ID.'" '.($side_opt['allpages']? 'disabled="disabled"':'').' '.(is_array($side_opt['pages']) && in_array($p->ID, $side_opt['pages'])? 'checked="ckeched"':'').'> '.$p->post_title.'</p>';
}
$html .= '</div><h4>'.__('Categories', 'pureness').' <small style="float: right"><input id="selCat-'.$k.'" type="button" value="'.__('Select all', 'pureness').'" /></small></h4><div id="cats-'.$k.'" style="overflow: auto; height: 150px">';
foreach($cats as $c) {
  $html .= '<p '.($side_opt['categories']? 'class="inactive_item"':'').' ><input type="checkbox" name="cat_'.$c->cat_ID.'" '.($side_opt['categories']? 'disabled="disabled"':'').' '.(is_array($side_opt['cats']) && in_array($c->cat_ID, $side_opt['cats'])? 'checked="ckeched"':'').'> '.$c->cat_name.'</p>';
}
$html .= '</div></form>';
?>
sidebar.prepend('<div class="sidebar-description"><a id="button-<?php echo $k; ?>"><?php _e('Show in', 'pureness'); ?></a></div><div id="pageable-<?php echo $k; ?>"><?php echo $html; ?> </div>');
jQuery('#pageable-<?php echo $k; ?>').dialog({ autoOpen: false, title: "<?php echo sprintf(__('Show in - %s', 'pureness'), $side['name']); ?>", modal:true, buttons: { "<?php _e('Confirm', 'pureness'); ?>": function() {	jQuery( '#form-<?php echo $k; ?>' ).submit(); return false; }, "<?php _e('Cancel', 'pureness'); ?>": function() { jQuery( this ).dialog( "close" ); } } });
jQuery('#button-<?php echo $k; ?>').button().click(function() {jQuery('#pageable-<?php echo $k; ?>').dialog('open');});
jQuery('#selPag-<?php echo $k; ?>').click(function() {jQuery('#pages-<?php echo $k; ?> input:checkbox').attr('checked', 'checked'); });
jQuery('#selCat-<?php echo $k; ?>').click(function() {jQuery('#cats-<?php echo $k; ?> input:checkbox').attr('checked', 'checked'); });
jQuery('#form-<?php echo $k; ?>').submit(function() {var $this = jQuery(this); jQuery.post( $this.attr('action'), $this.serialize(), function(data) {jQuery('#pageable-<?php echo $k; ?>').dialog('close'); return false; } ); return false; });
jQuery('#form-<?php echo $k; ?> input[name=allpages]').click(function() {jQuery('#pages-<?php echo $k; ?> input').each(function() {var $this = jQuery(this); if($this.attr('disabled')) $this.removeAttr('disabled'); else $this.attr('disabled', 'disabled'); $this.parent().toggleClass('inactive_item'); }); });
jQuery('#form-<?php echo $k; ?> input[name=categories]').click(function() {jQuery('#cats-<?php echo $k; ?> input').each(function() {var $this = jQuery(this); if($this.attr('disabled')) $this.removeAttr('disabled'); else $this.attr('disabled', 'disabled'); $this.parent().toggleClass('inactive_item'); }); });
<?php } ?>

<?php if (isset($side['visible']) && $side['visible']) { 
  $side_visible = get_option('sidebar_visible_'.$k); 
  //var_dump($side_visible); 
?>
sidebar.prepend('<div class="sidebar-description"><input id="visible-<?php echo $k; ?>" type="checkbox" <?php if($side_visible == 'true') {echo ' checked="checked" ';} ?>/> Visible</div>');
jQuery('#visible-<?php echo $k; ?>').change(function() {jQuery.post('<?php echo get_template_directory_uri().'/functions/sidebar-widgets.php'; ?>', 'sidebar_id=<?php echo $k; ?>&pure_sidebar=true&visible='+this.checked);});
<?php } ?>
<?php } ?>
});


