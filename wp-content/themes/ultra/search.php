<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package ultra
 * @since ultra 0.9
 * @license GPL 2.0
 */
get_header(); ?>

<?php if ( have_posts() ) : ?>

	<?php if ( siteorigin_page_setting( 'page_title' ) ) : ?>
		<header class="page-header">
			<div class="container">
				<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'ultra' ), '<span>' . get_search_query() . '</span>' ); ?></h1><?php ultra_breadcrumb(); ?>
			</div><!-- .container -->
		</header><!-- .page-header -->
	<?php endif; ?>

	<div class="container">

		<div id="primary" class="content-area">
			<main id="main" class="site-main">

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */
					get_template_part( 'content', 'search' );
					?>

				<?php endwhile; ?>

				<?php the_posts_pagination(); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php get_sidebar(); ?> 
	<?php get_footer();	?>
