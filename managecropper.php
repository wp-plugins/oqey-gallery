<?php
//if(!empty($_SERVER['SCRIPT_FILENAME']) && 'managecropper.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

include("../../../wp-load.php");

?>

<script  type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery/jquery-1.4.2.min.js"></script>
<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL; ?>/oqey-photo-cropper/css/jquery.jcrop.css" type="text/css" />
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/oqey-photo-cropper/js/jquery.jcrop.js"></script>

<img src="http://www.darii.eu/dorin/wp-content/oqey_gallery/galleries/rascals/iphone/tristalerit_children_orangecountychildrensphotographer_0002_1301911978.jpg" id="cropbox" />
<div style="width:150px;height:100px;overflow:hidden;">
<img src="http://www.darii.eu/dorin/wp-content/oqey_gallery/galleries/rascals/iphone/tristalerit_children_orangecountychildrensphotographer_0002_1301911978.jpg" id="preview" />
</div>

<script type="text/javascript">

/*			jQuery(window).load(function(){

				jQuery('#cropbox').Jcrop({
					onChange: showPreview,
					onSelect: showPreview,
					aspectRatio: 1.5
				});

			});
*/
			// Our simple event handler, called from onChange and onSelect
			// event handlers, as per the Jcrop invocation above
			function showPreview(coords)
			{
				if (parseInt(coords.w) > 0)
				{
					var rx = 150 / coords.w;
					var ry = 100 / coords.h;

					jQuery('#preview').css({
						width: Math.round(rx * 900) + 'px',
						height: Math.round(ry * 600) + 'px',
						marginLeft: '-' + Math.round(rx * coords.x) + 'px',
						marginTop: '-' + Math.round(ry * coords.y) + 'px'
					});
                    
           
                    
                //    var xx = coords.w + " - " + coords.h + " - " + coords.x + " - " + coords.y;                    
                   // setTimeout(function(){ alert(xx) }, 1500);
				}
			}

</script>

<script type="text/javascript">
<!--
	jQuery(document).ready(function(){
	jQuery('#cropbox').Jcrop({
					onChange: showPreview,
					onSelect: showPreview,
					aspectRatio: 1.5
				});
	});
-->
</script>