<?php global $wpdb;	
$pro_table_prefix=$wpdb->prefix.'pra_';

if(isset($_POST['submit']))
{	
	 $pra_effect = sanitize_text_field($_POST['pra_effect']);
	 $pra_display_arrow = sanitize_text_field($_POST['pra_display_arrow']);	
	 $pra_show_image = sanitize_text_field($_POST['pra_show_image']);	
 	 $pra_pauseduration = sanitize_text_field($_POST['pra_pauseduration']);	
 	 $pra_scrollduration = sanitize_text_field($_POST['pra_scrollduration']);	
  	 $pra_pauseonhover = sanitize_text_field($_POST['pra_pauseonhover']);	
	 $pra_autoplay = sanitize_text_field($_POST['pra_autoplay']);	
	 $pra_star_ratings = sanitize_text_field($_POST['pra_star_ratings']);	
	 $pra_designation = sanitize_text_field($_POST['pra_designation']);	
	 $pra_bgcolor = sanitize_text_field($_POST['pra_bgcolor']);	


	 
    $wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_effect
			), 
			array( 'id' => 1 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
	
	
	
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_display_arrow
			), 
			array( 'id' => 2 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);

	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_show_image
			), 
			array( 'id' => 3 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);
			
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_pauseduration
			), 
			array( 'id' => 4 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
			
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_scrollduration
			), 
			array( 'id' => 5 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_pauseonhover
			), 
			array( 'id' => 6 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_autoplay
			), 
			array( 'id' => 7 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
			
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_star_ratings
			), 
			array( 'id' => 8 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);
	
	
	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_designation
			), 
			array( 'id' => 9 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);

	$wpdb->update( 
	$pro_table_prefix.'testimonial_settings', 
			array( 
				'value' => $pra_bgcolor
			), 
			array( 'id' => 10 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
			echo '<div class="wrap">
      <div class="updated" style="background-color:#7AD03A;">
        <p><strong style="color:#FFF;" >Details are updated successfully.
        </strong> </p>
      </div>
    </div>';
			
}
?>
<script type="text/javascript">

function isNumber(evt) 
				{
					evt = (evt) ? evt : window.event;
					var charCode = (evt.which) ? evt.which : evt.keyCode;
					
					if (charCode > 31 && (charCode < 48 || charCode > 57)) 
					{
						return false;
					}
					return true;
				}

function CopyToClipBoard(whichcode) 
{
  /* Get the text field */
  var copyText = document.getElementById(whichcode);

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  alert("Copied the code: " + copyText.value);
} 

</script>

<div id="wpbody">
  <div tabindex="0" aria-label="Main content"  style="overflow: hidden;">
    <div id="execphp-message"></div>
    <div class="wrap">
      <div class="updated fade">
        <p><strong>Testimonial Setting </strong> </p>
      </div>
    </div>
    <div class="clear"></div>
    <div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Testimonial :
          <input type="text" id="BaseShortCode" value="[pra_testimonial]" style=" border-radius: 5px; width: 200px;" readonly="readonly" > <button onclick="CopyToClipBoard('BaseShortCode')" class="button">Copy code</button>
          </strong> </p>
      </div>
      <div class="updated" style=" background-color: #3498db;">
   		 <p><strong style="color:#FFF;">Short code to display ALL Testimonials :  
      		<input type="text" id="SmartShortCode" value='[pra_alltestimonials PerPage="10"]' style=" border-radius: 5px; width: 255px;" readonly="readonly" ><button onclick="CopyToClipBoard('SmartShortCode')" class="button">Copy code</button>
      		</strong> </p>
 	   </div>
    </div>
  </div>
  
  <!-- wpbody-content -->
  <div class="clear"></div>
  <?php $myrows = $wpdb->get_col("SELECT value FROM ".$pro_table_prefix."testimonial_settings" );  ?>
  <div  class="postbox">
    <form action="" method="post" >
      <table id="misc-publishing-actions " class="misc-pub-section" cellpadding="0" cellspacing="0">
        <tr>
          <td  class="misc-pub-section" >Effect : </td>
          <td class="misc-pub-section">
          <select name="pra_effect" id="pra_effect" >
              <option  value="crossfade"  <?php if($myrows[0]=='crossfade') echo 'selected="selected"'; ?> >CrossFade</option>
              <option  value="scroll" <?php if($myrows[0]=='scroll') echo 'selected="selected"'; ?> >Scroll</option>
              <option  value="fade" <?php if($myrows[0]=='fade') echo 'selected="selected"'; ?> >Fade</option>
              <option  value="cover" <?php if($myrows[0]=='cover') echo 'selected="selected"'; ?> >Cover</option>
              <option  value="cover-fade" <?php if($myrows[0]=='cover-fade') echo 'selected="selected"'; ?> >Cover Fade</option>
              <option  value="uncover" <?php if($myrows[0]=='uncover') echo 'selected="selected"'; ?> >Uncover</option>
              <option  value="uncover-fade" <?php if($myrows[0]=='uncover-fade') echo 'selected="selected"'; ?> >Uncover Fade</option>
              <option  value="none" <?php if($myrows[0]=='none') echo 'selected="selected"'; ?> >None</option>
            </select></td>
        </tr>
        <tr>
          <td class="misc-pub-section">Show Arrow  : </td>
          <td class="misc-pub-section"><select name="pra_display_arrow" id="pra_display_arrow">
              <option  value="1" <?php if($myrows[1]==1) echo 'selected="selected"'; ?> >Yes</option>
              <option  value="0"  <?php if($myrows[1]==0) echo 'selected="selected"'; ?> >No</option>
            </select></td>
        </tr>
        <tr>
          <td class="misc-pub-section">Show Author Image  : </td>
          <td class="misc-pub-section"><select name="pra_show_image" id="pra_show_image">
              <option  value="1" <?php if($myrows[2]==1) echo 'selected="selected"'; ?> >Yes</option>
              <option  value="0"  <?php if($myrows[2]==0) echo 'selected="selected"'; ?> >No</option>
            </select></td>
        </tr>
		<tr>
          <td class="misc-pub-section">Show Star Ratings  : </td>
          <td class="misc-pub-section"><select name="pra_star_ratings" id="pra_star_ratings">
              <option  value="1" <?php if($myrows[7]==1) echo 'selected="selected"'; ?> >Yes</option>
              <option  value="0"  <?php if($myrows[7]==0) echo 'selected="selected"'; ?> >No</option>
            </select></td>
        </tr>
        <tr>
          <td class="misc-pub-section">Show Author Designation/Post  : </td>
          <td class="misc-pub-section"><select name="pra_designation" id="pra_designation">
              <option  value="1" <?php if($myrows[8]==1) echo 'selected="selected"'; ?> >Yes</option>
              <option  value="0"  <?php if($myrows[8]==0) echo 'selected="selected"'; ?> >No</option>
            </select></td>
        </tr>
        <tr>
          <td class="misc-pub-section">Scroll Duration  : </td>
          <td class="misc-pub-section"><input type="text" onkeypress="return isNumber(event);" name="pra_pauseduration" value="<?php echo $myrows[3]; ?>"  />
            <em>Seconds [Default : 9]</em></td>
        </tr>
        <tr>
          <td class="misc-pub-section">Pause On Hover : </td>
          <td class="misc-pub-section"><select name="pra_pauseonhover">
              <option  value="true" <?php if($myrows[5]=='true') echo 'selected="selected"'; ?> >Yes</option>
              <option  value="false" <?php if($myrows[5]=='false') echo 'selected="selected"'; ?> >No</option>
            </select>
        </tr>
        <tr>
          <td class="misc-pub-section">Autoplay  : </td>
          <td class="misc-pub-section"><select name="pra_autoplay">
              <option  value="true" <?php if($myrows[6]=='true') echo 'selected="selected"'; ?> >Yes</option>
              <option  value="false" <?php if($myrows[6]=='false') echo 'selected="selected"'; ?> >No</option>
            </select></td>
        </tr>
         <tr>
          <td class="misc-pub-section">Background Color  : </td>
          <td class="misc-pub-section"><input type="color" id="pra_bgcolor" name="pra_bgcolor" value="<?php echo $myrows[9]; ?>" ></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="misc-pub-section"><input type="submit" class="button button-primary button-large"  name="submit" value="Submit" onclick="return checkform();" /></td>
        </tr>
      </table>
    </form>
     <div class="pra_lovedto"><a href="https://wordpress.org/support/plugin/awesome-testimonials/reviews/#new-post"></a></div>
  </div>
</div>
 <div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Testimonial :
          <input type="text" id="BaseShortCode" value="[pra_testimonial]" style=" border-radius: 5px; width: 200px;" readonly="readonly" > <button onclick="CopyToClipBoard('BaseShortCode')" class="button">Copy code</button>
          </strong> </p>
      </div>
      <div class="updated" style=" background-color: #3498db;">
   		 <p><strong style="color:#FFF;">Short code to display ALL Testimonials :  
      		<input type="text" id="SmartShortCode" value='[pra_alltestimonials PerPage="10"]' style=" border-radius: 5px; width: 255px;" readonly="readonly" ><button onclick="CopyToClipBoard('SmartShortCode')" class="button">Copy code</button>
      		</strong> </p>
 	   </div>
    </div>