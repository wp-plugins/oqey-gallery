<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'manageskins.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

global $wpdb;
$oqey_skins     = $wpdb->prefix . "oqey_skins";
   
       $rn = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status = '1' "); 
       
       if(empty($rn)){
          
          $i = $wpdb->get_row("SELECT id FROM $oqey_skins WHERE status = '0' ORDER BY id ASC LIMIT 0,1");
          
          if(!empty($i)){
            
              $wpdb->query( $wpdb->prepare("UPDATE $oqey_skins SET status = '1' WHERE id = %d ", $i->id ) );
          
          }
          
       }

if(isset($_GET['new_skin'])) {

  $wpdb->query( $wpdb->prepare( "UPDATE $oqey_skins SET status = %d WHERE status = '1' ", 0  ));
  $wpdb->query( $wpdb->prepare( "UPDATE $oqey_skins SET status = %d WHERE id = %d ", 1, absint($_REQUEST['new_skin'])  ));
  
     $mesaj = '<div class="wrap">
                 <div class="updated below-h2" id="message">
                   <p>'.__('New skin was set.', 'oqey-gallery').'</p>
                 </div>
               </div>';
}

if(isset($_GET['scaner'])){

$root = OQEY_ABSPATH."wp-content/oqey_gallery/skins/".oqey_getBlogFolder($wpdb->blogid);
$skins = oqey_scanSkins($root,"1");
$d=0;

foreach($skins as $skin){

if( !$sql=$wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_skins WHERE folder = %s ", $skin) ) ){
  
  $myFile = OQEY_ABSPATH."wp-content/oqey_gallery/skins/".oqey_getBlogFolder($wpdb->blogid).$skin."/details.xml";
  $xml    = @simplexml_load_file($myFile);
  
  if(!empty($xml)){
    
    $name        = $xml->title;
    $description = $xml->description;
    $commercial  = $xml->commercial;
    $skinid      = $xml->skinid;

    $wpdb->query( $wpdb->prepare( "INSERT INTO $oqey_skins (name, description, folder, commercial, skinid) 
                                                    VALUES ('%s', '%s', '%s', '%s', '%s' )",
                                                             trim(stripslashes_deep($name)),
                                                             trim(stripslashes_deep($description)), 
                                                             trim($skin), 
                                                             trim($commercial), 
                                                             trim($skinid)
                                                            )
                                                            );
    $d++;
  
  }

 }

}

     $r = $wpdb->get_results( "SELECT skinid FROM $oqey_skins "); 
      
     if(!empty($r)){

       delete_option("oqey_flash_gallery_true");       
       
       $rn = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status = '1' "); 
       
       if(empty($rn)){
          
          $i = $wpdb->get_row("SELECT id FROM $oqey_skins WHERE status = '0' ORDER BY id ASC LIMIT 0,1");
          
          if(!empty($i)){
            
              $wpdb->query( "UPDATE $oqey_skins SET status = '1' WHERE id = '".$i->id."'" );
          
          }          
       }            
     }

     $mesaj = '<div class="wrap">
                 <div class="updated below-h2" id="message">
                   <p>'.$d."&nbsp;".__('new skins found.', 'oqey-gallery').'</p>
                 </div>
               </div>';
}
?>

<script type="text/javascript">
function refreshPage(){ window.location = "<?php echo admin_url('admin.php?page=oQeySkins&scaner=true'); ?>"; }
</script>

<div class="wrap" style="width:900px;">
    <h2 style="width: 930px;"><?php _e('Manage Skins', 'oqey-gallery'); ?></h2>
  
    <div id="save" style="width:917px; margin-bottom:10px;">
      <?php if(!empty($mesaj)){ echo $mesaj; } ?>
    </div>
    
</div>

<div class="postbox" style="height:50px; width:900px;">

<table width="900" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="175" height="50" align="center"><?php _e('Upload a new skin', 'oqey-gallery'); ?> (*.zip)</td>
    <td width="123" align="right" valign="middle">
        
        <div class="uploader" id="flashuploader">
            <input type="button" name="uploadimages" id="uploadimages" value="<?php echo __('Upload', 'oqey-gallery'); ?>" class="button" style="float:left;"/>
            <img src="<?php echo oQeyPluginUrl(). '/images/indicator.gif'; ?>" id="inicon"/>     
        </div>
        
<?php
if ( is_ssl() ){ $cookies = $_COOKIE[SECURE_AUTH_COOKIE]; }else{ $cookies = $_COOKIE[AUTH_COOKIE]; }
$datele = '7--'.$cookies.'--'.$_COOKIE[LOGGED_IN_COOKIE].'--'.wp_create_nonce('oqey-skins');
?>
   
<script type="text/javascript">
var uploader = new plupload.Uploader({
runtimes : "gears,html5,flash,silverlight,browserplus",
browse_button : "uploadimages",
container: "flashuploader",
max_file_size : "<?php echo oQeyGetNumericUploadSize()?>",
url : "<?php echo oQeyPluginUrl().'/btupload.php'?>",
flash_swf_url : "<?php echo includes_url()?>'js/plupload/plupload.flash.swf",
silverlight_xap_url : "<?php echo includes_url()?>'js/plupload/plupload.silverlight.xap",
filters : [{title : "Zip files", extensions : "zip"}],
multipart_params : {
        "details" : "<?php echo base64_encode($datele)?>"
    }
});
uploader.init();
uploader.bind("FilesAdded", function(up, files) {
uploader.start();
return false;
});
uploader.bind("UploadProgress", function(up, file) {
document.getElementById("inicon").style.display="block";
})
uploader.bind("UploadComplete", function(up) {
refreshPage();
document.getElementById("inicon").style.display="none";
});
</script>

</td>
    <!--<td width="18">/</td>-->
    <td align="left"> 
    <div align="left" style="padding-top:3px;">
    <form id="scaner" name="scaner" method="post" action="<?php echo admin_url('admin.php?page=oQeySkins&scaner=true'); ?>">     
        <input class="button" type="submit" name="scan" id="scan" value="<?php _e('Magic scan', 'oqey-gallery'); ?>" />              
    </form>  
    </div>
    </td>
    <td width="246" align="right" valign="middle">
    <div style="margin-right:2px;">
    <a href="#get_new_skins" class="get_new_skins"><img src="<?php echo oQeyPluginUrl().'/images/'; ?>getmoreskinsbgn.png" width="250" height="48" /></a>
    </div>
    </td>
  </tr>
</table>
</div>

<div id="new_skins" class="postbox" style="width:900px; display:none; min-height:30px;"><div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div></div>

<div class="postbox" style="width:900px;">
<div id="currentskin">
<table width="870" border="0" cellspacing="0" cellpadding="0" class="default-skin">
  <tr>
    <td colspan="3">
      <div id="current-theme">
  <h4><?php _e('Default skin', 'oqey-gallery'); ?></h4>
</td>
    </tr>
  <tr>
    <td width="161" height="120" align="left" valign="middle" style="margin-left:0;">
  <?php
   global $wpdb; 
   $r = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status ='1' LIMIT 0,1");
   
   if(!empty($r)){
   
      if($r->commercial=="yes"){ $comm = ' - '.__('Commercial skin', 'oqey-gallery'); }else{ $comm = ' - '.__('Free skin', 'oqey-gallery'); }
      echo '<img src="'.oQeyPluginRepoUrl().'/skins/'.oqey_getBlogFolder($wpdb->blogid).$r->folder.'/'.$r->folder.'.jpg" alt="" width="150" height="100" style="border:#999999 solid thin;" />';
  
  }
  ?>
    </td>
    <td style="margin-left: 10px; padding: 5px;" valign="top" width="443" align="left">
    
    <?php 
    
    if(!empty($r)){ ?>

    <h4><?php echo urldecode($r->name).$comm; ?></h4>
     <p><?php echo urldecode($r->description); ?><br />
       <?php _e('Skin files location', 'oqey-gallery'); ?>: <code>/skins/<?php echo oqey_getBlogFolder($wpdb->blogid).$r->folder; ?></code>.</p>
       
       <?php
           $skinpath = oQeyPluginRepoPath().'/skins/'.oqey_getBlogFolder($wpdb->blogid).$r->folder;
	       $sfpath = $skinpath.'/settings.swf';  
           if(is_file($sfpath)){ 
              
              $skoptions = '<a href="#set_skin_options" class="set_skin_options" id="skopt'.$r->id.'" rel="'.$r->folder.'">'.__('Skin Options', 'oqey-gallery').'</a>'; 
           
           }else{ 
            
            $skoptions = ""; 
            
           }
        ?>
    <p><?php echo $skoptions; ?></p>

    <?php 
      }else{
    
         echo "<p>";
         _e('No skins have been installed yet.', 'oqey-gallery');
         echo "<br>";
         _e('In order to install a skin, please click on the link above or upload one if you have downloaded it from oQeySites website.', 'oqey-gallery');                    
         echo "</p>";
     }
?>
    </td>
    <td width="294" height="110" align="left" valign="top">
    
    <?php if($r->commercial=="yes" && $r->firstrun==1){  ?>
 <div style="margin-right:10px; padding:5px;">
               <p>
                <?php _e('Commercial key', 'oqey-gallery'); ?>:<br/>
                <input name="comkey" class="comkey form-input-tip" type="text" value="<?php echo $r->comkey; ?>" id="key<?php echo $r->id; ?>" style="width:190px;" />
                <input type="button" name="savekey" class="savekey button" id="<?php echo $r->id; ?>" value="<?php _e('Save', 'oqey-gallery'); ?>"/>
               </p>
<p>
<form action="http://oqeysites.com/paypal/oqeypaypal.php" name="buyskin" method="post">
<input type="hidden" name="oqey" value="qwe1qw5e4cw8c7fv8h7" />
<input type="hidden" name="website" value="<?php echo urlencode(get_option('siteurl')); ?>" />
<input type="hidden" name="s" value="<?php echo $r->skinid; ?>" />
<input type="hidden" name="skinfolder" value="<?php echo $r->folder; ?>" />
<input type="text" name="d" value="discount code" placeholder="discount code" class="discount_code" style="width:259px;"/>
<a href="#buy_this_skin" class="buy_this_skin"><img src="<?php echo oQeyPluginUrl(); ?>/images/btn_buynowcc_lg.gif" style="margin-top:8px;" /></a>
</form>
</p>
</div>
   <?php } ?> 
    </td>
  </tr>
</table>

</div>
<br class="clear" />
</div>

<div class="postbox" style="width:900px;">
<div id="content"><div class="obis"><?php _e('Loading content...', 'oqey-gallery'); ?></div></div>
</div>

<div class="postbox" style="width:900px;">
<div align="left" style="margin:15px;">
<?php _e('Notes', 'oqey-gallery'); ?>: <br />
         * <?php echo oqey_uploadSize(); ?><br />
         * <?php _e('You may upload new skin files directly to your plugin directory via ftp.', 'oqey-gallery'); ?><br />
         * <?php _e('Your skins folder location', 'oqey-gallery'); ?>: <b><?php echo oQeyPluginRepoUrl().'/skins/'.oqey_getBlogFolder($wpdb->blogid); ?></b>       
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
								
   jQuery.loadImages([ '<?php echo oQeyPluginUrl().'/images/preview_button.png'; ?>', 
                       '<?php echo oQeyPluginUrl().'/images/remove_button.png'; ?>', 
                       '<?php echo oQeyPluginUrl().'/images/edit_button.png'; ?>' ],function(){});

   jQuery(".get_new_skins").click(function(){		
			if (jQuery("#new_skins").is(":hidden") ) {
			 
                jQuery("#new_skins").hide().fadeIn("slow");
			
			jQuery.post(ajaxurl, { action:"oQeyGetNewSkins", get_new_skins: "yes" },
            function(r){
		
            	jQuery("#new_skins").html(r);
			 
			jQuery(".install_skin").click(function(){
			    
                var id = jQuery(this).attr("id");
			
            jQuery.post(ajaxurl, { action:"oQeyInstallNewSkins", install_new_skin: id, nonce: "<?php echo wp_create_nonce('oqey-install-skin'); ?>" },
            function(r){
            
               if(r=="ok"){ refreshPage(); }
			
			 });
			});
			
			});
			
			}else{
			jQuery("#new_skins").show().fadeOut("slow");	
			}
			
	   });

function clearUp(){ 
    setTimeout(function (){ 
        jQuery('#save').fadeOut(function(){ 
            jQuery("#save").html("&nbsp;");
        }); 
    }, 3000); 
}

jQuery.post(ajaxurl, { action:"oQeyGetAllInstalledSkins", allskins: "yes" },
   function(data){
    
            jQuery("#content").hide().html(data).fadeIn('slow');
            
            jQuery(".set_skin_options").click(function(){ //set skin OPTIONS
            
                var id = jQuery(this).attr("id");
	        	var playerVersion = swfobject.getFlashPlayerVersion();
	            if(playerVersion.major<8){	
		           
                   var flash = "no";
	    
                }else{
                    
		           var flash = "yes";
		       
                }
			 
                var id = jQuery(this).attr("id");
                var folder = jQuery(this).attr("rel");
			    
                jQuery.post(ajaxurl, { action:"oQeySkinOptions", folder: folder, flash: flash },
                function(r){

                  var $dialog = jQuery('<div style="height:700px; width:990px; position:relative; display:block;"><\/div>').html(r).dialog({
			      width: 990,
                  height:700,
			      maxWidth: 1000,
			      maxHeight: 700,
			      resizable: false,
			      autoOpen: false,
			      title: "<?php _e('Skin Options', 'oqey-gallery'); ?>",
			      modal: true,
			      draggable: false	
	    	    
                });			
	    	   
               $dialog.dialog('open'); 
			
		       });		
			
            });
						
			jQuery(".set_as_default").click(function(){
                        
                            var id = jQuery(this).attr("id");
			    window.location = "<?php echo admin_url( 'admin.php?page=oQeySkins&new_skin=', 'http' ); ?>" + id;	
                        
                        });
			
			jQuery(".savekey").click(function(){
                            
                          var id     = jQuery(this).attr("id");
                          var comkey = jQuery("#key" + id).val();
			  
              if(jQuery.trim(comkey)!=""){
                  jQuery.post(ajaxurl,{ action: "oQeySaveSkinKey", savekey: id, comkey: comkey },
                      function(r){
                          jQuery("#save").hide().html(r).fadeIn("slow");
			 
                  });
              }
			});

			jQuery(".discount_code").click(function(){
                        
                               var d = jQuery(this).val("");
                        });
			
			jQuery(".buy_this_skin").click(function(){			
			
			    jQuery(this).parent("form").submit();
			
			});
			

			jQuery(".delete_this_skin").click(function(){
                            
                              var id = jQuery(this).attr("id");
                              jQuery.post(ajaxurl, { action:"oQeySkinToTrash", movetotrashskin: id },
                                   function(data){ 
                                       
                                     jQuery('#skink_tr_'+id).fadeOut('slow');
                                     jQuery("#save").hide().html('<div class="wrap"><div class="updated below-h2" id="message"><p><?php _e('Skin was moved to trash', 'oqey-gallery'); ?> <a href="#undo" id="undoskin">undo<\/a><\/p><\/div><\/div>').fadeIn('slow');
			
			             jQuery("#undoskin").click(function(){
			
            jQuery.post(ajaxurl, { action:"oQeySkinFromTrash", undoskin: id },
             function(r){
			
                var data = eval('(' + r + ')');
			    jQuery("#save").hide().html(decodeURIComponent(data.raspuns)).fadeIn("slow");
			    jQuery('#skink_tr_'+id).fadeIn('slow');
			 });			
			});
			
		  });
		
        });
   });
    
<?php if(isset($_REQUEST['showskins']) && $_REQUEST['showskins']=="yes"){ ?>
      jQuery('.get_new_skins').trigger('click');
<?php } ?>   
});
</script>