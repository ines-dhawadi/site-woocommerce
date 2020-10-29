<?php
/*
Plugin Name: Awesome Testimonials
Plugin URI: https://wordpress.org/plugins/awesome-testimonials/
Description: Wordpress Testimonials with front end Add Testimonials Facility.
Version: 2.2.0
Author: Prakash
License: GPL
*/

$siteurl = get_option('siteurl');

global $wpdb;

register_activation_hook(__FILE__,'pra_install');
register_deactivation_hook(__FILE__ , 'pra_uninstall' );

if(!defined('PR_CSS_DIR'))
 {
    define('PR_CSS_DIR',plugin_dir_url( __FILE__ ).'css');
 }
 if(!defined('PR_IMAGE_DIR'))
 {
    define('PR_IMAGE_DIR',plugin_dir_url( __FILE__ ).'images');
 }
 



function pra_theme_enqueue() {
		wp_register_style( 'pra_TestimonialsCss', plugins_url('css/pra_testimonial.css', __FILE__) );
		wp_enqueue_style( 'pra_TestimonialsCss' );
		
  	
		wp_enqueue_script('jquery');		
		
		wp_register_script( 'pra_TestimonialMinCss', plugins_url('js/jquery.carouFredSel-6.2.1.js', __FILE__) );
		wp_enqueue_script( 'pra_TestimonialMinCss' );
		
		wp_register_script( 'pra_TestimonialsJs', plugins_url('js/pra_testimonials.js', __FILE__) );
		wp_enqueue_script( 'pra_TestimonialsJs' );
	}


function pra_admin_enqueue() {
		wp_register_style( 'pra_AdminCss', plugins_url('css/admin-style.css', __FILE__) );
		wp_enqueue_style( 'pra_AdminCss' );
}
	


add_action('wp_enqueue_scripts','pra_theme_enqueue');
add_action('admin_enqueue_scripts','pra_admin_enqueue');
//register plugin scripts and css in wp-admin


function pra_install()
{

	global $wpdb;
	$pro_table_prefix=$wpdb->prefix.'pra_';
	
	$table2 = $pro_table_prefix."testimonial_settings";
    $structure2 = "CREATE TABLE $table2 (
    		id int(11) NOT NULL AUTO_INCREMENT,
  			metaname varchar(255) NOT NULL,
 		 	value varchar(225) NOT NULL,
			PRIMARY KEY (`id`)
    );";
    $wpdb->query($structure2);
	
	
	 $wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (1,'effect', 'crossfade')");
		
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (2,'display_arrow', 1)");
	
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (3,'show_image', 1)");

	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (4,'pauseduration', 9)");

	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (5,'scrollduration', 1000)");

	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (6,'pauseonhover', 'true')");
		  
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (7,'autoplay','true' )");
		  
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (8,'show_star_ratings','1' )");
	
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (9,'show_designation','1' )");

	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
	      VALUES (10,'BG_color','#ffffff' )");
		  

	  
}
function pra_uninstall()
{
   global $wpdb;
   $pro_table_prefix=$wpdb->prefix.'pra_';

    $table1 = $pro_table_prefix."testimonial_settings";
    $structure1 = "drop table if exists $table1";
    $wpdb->query($structure1);  
	
	
}


add_action('admin_menu','pra_admin_menu');

function pra_admin_menu() { 
	add_menu_page(
		"Awesome Testimonials",
		"Testimonial Settings",
		8,
		__FILE__,
		"pra_admin_menu_list",
		plugins_url( 'images/prakash.png' , __FILE__ )
	); 
}

/*******************************************************************************/
add_action('init', 'pra_awesome_testi_init');
	function pra_awesome_testi_init() 
	{
		/*----------------------------------------------------------------------
			testimonial Post Type Labels
		----------------------------------------------------------------------*/
		
		$labels = array(
			'name' => _x('All Testimonials', 'Post type general name'),
			'singular_name' => _x('Testimonials', 'Post type singular name'),
			'add_new' => _x('Add new testimonial', 'Testimonial Item'),
			'add_new_item' => __('Add New Testimonial'),
			'edit_item' => __('Edit testimonial'),
			'new_item' => __('New testimonial'),
			'all_items' => __('All Testimonials'),
			'view_item' => __('View'),
			'search_items' => __('Search'),
			'not_found' =>  __('No testimonials found.'),
			'not_found_in_trash' => __('No testimonials found.'), 
			'parent_item_colon' => '',
			'menu_name' => 'Testimonials'
		);
		
		/*----------------------------------------------------------------------
			testimonial Post Type Properties
		----------------------------------------------------------------------*/
		
		$args = array(
		'label'               => __( 'Testimonial', 'twentythirteen' ),
		'description'         => __( 'Testimonial Discriptions', 'twentythirteen' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'thumbnail'),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		
	);
		
		
register_post_type('pra_testimonials',$args);
	
		//Enabling Support for Post Thumbnails
		add_theme_support( 'post-thumbnails');
	}


/****************************** Prakash **********************************************/


add_action("admin_init", "admin_init");
    add_action('save_post', 'save_pra_ratings');  

    function admin_init(){
        add_meta_box("pra_testimonials_ratings", "Star(s)", "meta_options", "pra_testimonials", "advanced", "low");
		add_meta_box("pra_testimonials_designation", "Designation/Post", "designation_meta_options", "pra_testimonials", "advanced", "low");
    }  


    function meta_options(){
        global $post;
        $custom = get_post_custom($post->ID);
        $pra_ratings = $custom["pra_ratings"][0];
?>


<div class="pra_rating_section">

<div class="pra_stars">
  <?php if($pra_ratings==1)
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

?>
  <div class="rating"  <?php echo $pra_style; ?> ></div>
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
</div>
</div>
<?php
    } 
	
	
	 function designation_meta_options(){
        global $post;
        $custom = get_post_custom($post->ID);
        $pra_designation = $custom["pra_designation"][0];
?>

<input type="text" value="<?php echo $pra_designation; ?>" name="pra_designation"  />


<?php
    }  

	function save_pra_ratings(){
		global $post;
		update_post_meta($post->ID, "pra_ratings", $_POST["pra_ratings"]);
		update_post_meta($post->ID, "pra_designation", $_POST["pra_designation"]);
	}


/****************************** Prakash **********************************************/


/************************** Display all Testimonails *****************************************************/


function pra_admin_menu_list()
{
	 include 'testimonial_settings.php';
}



//Add ShortCode
//Short Code [pra_Testimonial]
include('testimonial.php');
add_shortcode("pra_testimonial","pra_testimonial_shortcode");

//Short Code [pra_alltestimonials]
include('alltestimonial.php');
add_shortcode("pra_alltestimonials","pra_alltestimonials_shortcode");



/************************** Create Widget *****************************************************/

// Creating the widget 
class pra_widget extends WP_Widget {
	function __construct() {
	parent::__construct(
		'pra_widget', 
		__('PRA Testimonial Widget', 'pra_widget_domain'), 
		array( 'description' => __( 'Widget to show testimoial on sidebar', 'pra_widget_domain' ), ) 
	);
	}
	
	// Creating widget front-end
	public function widget( $args, $instance ) {
	echo $args['before_widget'];
	
	// This is where you run the code and display the output
	echo do_shortcode( '[pra_testimonial]' );
	
	echo $args['after_widget'];
	}
} // Class pra_widget ends here

// Register and load the widget
function pra_load_widget() {
	register_widget( 'pra_widget' );
}
add_action( 'widgets_init', 'pra_load_widget' );
