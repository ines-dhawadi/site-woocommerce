<?php function pra_alltestimonials_shortcode($atts)
{
	 	global $wpdb;	
    	$pro_table_prefix=$wpdb->prefix.'pra_';
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args=array(
  		'post_type' => 'pra_testimonials',
		'post_status' => 'publish',
		'posts_per_page' => $atts['perpage'],
  		'paged'=>$paged);

		$my_query = null;
		$my_query = new WP_Query($args);
		if( $my_query->have_posts() )
				 {
					$myrows = $wpdb->get_col("SELECT value FROM ".$pro_table_prefix."testimonial_settings" ); 
					$display_arrow = $myrows[1];
					$show_image = $myrows[2];
					$show_star_ratings = $myrows[7];
					$show_designation = $myrows[8];
					$pra_bgcolor = $myrows[9];
		
					$pauseduration = $myrows[3];
					if($pauseduration=='')
					{
						$pauseduration=9000;
					}
					
					$scrollduration = $myrows[4];
					if($scrollduration=='')
					{
						$scrollduration=1000;
					} 
					
					if($display_arrow==1) 
					{ 
						$arrowneeded =  'style="display:block"'; 
					} 
					else 
					{ 
						$arrowneeded =  'style="display:none"'; 
					}
			ob_start();		
			$output =   '<div class="wrap">';
			foreach($my_query->posts as $postdetail)
				 { 		  
									$img_width = (int) 80;
									$img_height = (int) 80;
									$thumbnail_id = get_post_meta($postdetail->ID,'_thumbnail_id', true );
																	
									$thumb = wp_get_attachment_image( $thumbnail_id, array($img_width, $img_height), true );
									$pra_customefield = get_post_meta($postdetail->ID);
									$pra_ratings = $pra_customefield['pra_ratings'][0];
									$pra_designation = $pra_customefield["pra_designation"][0];
									
									
									if($show_image==1) { $imageneeded = $thumb; } 
									    $output .= '<div class="pra_testimonial">
										  <div  class="pra_testimonialtext" style="background-color: '.$pra_bgcolor.'"> <span class="quote-left"></span> <div class="testi-content">'.$postdetail->post_content.'<span class="quote-right"></span></div> </div>
										  <span class="pra_arrow"></span> <cite> <span class="photo">';
											  if($show_image==1) {  
																  if($thumbnail_id)
																	  {
																		  $output .= $thumb; 
																	  }												 
																	 else
																	  {
																		  $output .= '<img width="80" height="80" alt="NO Image" class="attachment-80x80" src="'.PR_IMAGE_DIR.'/noimage.png">';
																	  }
																 } 
										  $output .= '</span>          
										  <div class="pra_rightsection">
										  <span class="author">By <span>'.$postdetail->post_title.'</span>';
										  
										 if($show_designation==1) { 
											  $output .= '<strong>'.$pra_designation.'</strong>';
										 }
										  
										 $output .= '</span>';
										  
										 if($show_star_ratings==1) 
										 { 
											 $output .= '<div class="pra_front_stars">';
								
												 if($pra_ratings==1)
												{
													$pra_style= 'style=width:20%';
												} 
											  else if($pra_ratings==2)
												{
													$pra_style= 'style=width:40%';
												}
											  else if($pra_ratings==3)
												{
													$pra_style= 'style=width:60%';
												} 
											  else if($pra_ratings==4)
												{
													$pra_style= 'style=width:80%';
												}
											  else
												{
													$pra_style= 'style=width:100%';
												}
													 $output .= '<div class="rating '.$pra_ratings.'"  '.$pra_style.'></div>
													<input type="radio" name="pra_ratings" id="star5" value="5">
													<label for="star5"></label>
													<input type="radio" name="pra_ratings" id="star4" value="4">
													<label for="star4"></label>
													<input type="radio" name="pra_ratings" id="star3" value="3">
													<label for="star3"></label>
													<input type="radio" name="pra_ratings" id="star2" value="2">
													<label for="star2"></label>
													<input type="radio" name="pra_ratings" id="star1" value="1">
													<label for="star1"></label>
												  </div>';
											   }
											   											  
									     $output .= '</div><div class="pra_clear"></div></cite></div>';
					  
									 } 
		 }  
		 
		 
	$output .= '<div class="pra_pagination">';
	global $wp_query;
	$big = 999999999; // need an unlikely integer
	$output .= paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'prev_next'    => False,
	'current' => max( 1, get_query_var('paged') ),
	'total' => $my_query->max_num_pages
	) );
    $output .= '</div>';		 

		 
  $output .= ob_get_clean();
  return $output;
 
}



