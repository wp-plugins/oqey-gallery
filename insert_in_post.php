<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'insert_in_post.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('Please do not load this page directly. Thanks!');

   $oqey_galls = $wpdb->prefix . "oqey_gallery";
   $oqey_images = $wpdb->prefix . "oqey_images";

   $list = $wpdb->get_results(" SELECT * FROM $oqey_galls WHERE status!=2 ORDER BY id DESC  ");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="font-family:'Century Gothic'; font-size:12px;" class="tablesorter">
<thead>
<tr>
<th align="left">
*just click on picture to add the gallery
</th>
</tr>
</thead>
<tbody>
<?php 
   $j=0;	
   if(count($list)>0){
   foreach ($list as $i){   // $list->qgal_id $list->title  
   
   if($i->splash_img!=0){
   $img = $wpdb->get_row("SELECT * FROM $oqey_images WHERE id='".$i->splash_img."'");  
   }else{
   $img = $wpdb->get_row("SELECT * FROM $oqey_images WHERE gal_id ='".$i->id."' LIMIT 0,1");
   }
   if($j%2){ $colorb = '#F4FAFF'; }else{ $colorb = '#E8F4FF'; }  
   
	echo '<tr>	
	          <td style="background-color:'.$colorb.';">
			  <div>			  
		      <div style="float:left; width:120;">  
			  <a href="#null" onclick="inoQeyContent(\''.$i->id.'\')">
			  <img src="'.get_option('siteurl').'/wp-content/oqey_gallery/galleries/'.$i->folder.'/galthmb/'.$img->title.'" alt="image" class="addinpost" style="border:0; max-width:120px; max-height:80px;"/></a>
              </div>	           
		      <div align="left" style="float:left; padding-left:15px;">'.$i->title.'</div>
			  </div>
			  </td>
          </tr>';
		  $j++;
	}
   }else{ echo '<tr><td><div alighn="left">There is no galleries found. Please create a gallery first.</div></td></tr>'; }
?>
</tbody>
</table>
<script type="text/javascript">
function inoQeyContent($id){
var win = window.dialogArguments || opener || parent || top;
var html = "[oqeygallery id=" + $id + "]";
win.send_to_editor(html);
}
</script>