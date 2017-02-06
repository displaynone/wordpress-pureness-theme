<footer>
 	<div class="container">
   		<div class="row"> 

                            <p class="twelvecol">
                                <strong><?php echo antispambot(get_bloginfo('admin_email')); ?></strong> 
                                <a rel="external" class="wordpress" href="http://wordpress.org" title="WordPress">WordPress</a>, <?php $copyright_name = get_option('P_copyright_name'); ?>
                                
                                
                                    <?php echo date('Y.') ?>
                                    
                                    
                            </p>
		

 		</div> <!--end row-->
  	</div><!--end container-->
</footer>

<?php
wp_footer();

$tmp_stats_code = get_option('P_stats_code');
if ($tmp_stats_code != '') {
  echo stripslashes($tmp_stats_code);
}
?>

</body>
</html>