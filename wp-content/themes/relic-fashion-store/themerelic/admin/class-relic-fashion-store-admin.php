<?php
/**
 * Relic Fashion Store
 *
 * @author  Themerelic
 * @package Relic Fashion Store
 * @since   
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Relic_Fashion_Store_Admin' ) ) :

/**
 * Relic_Fashion_Store_Admin Class.
 */
class Relic_Fashion_Store_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );

		$page = add_theme_page( esc_html__( 'About', 'relic-fashion-store' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'relic-fashion-store' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'relic-fashion-store-welcome', array( $this, 'welcome_screen' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {
		$relic_fashion_store_theme = wp_get_theme();
		$relic_fashion_store_version = $relic_fashion_store_theme->get( 'Version' );
		
		wp_enqueue_style( 'relic-fashion-store-welcome', get_template_directory_uri() . '/themerelic/admin/css/admin-welcome.css', array(), $relic_fashion_store_version );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $relic_fashion_store_version, $pagenow;

		wp_enqueue_style( 'relic-fashion-store-message', get_template_directory_uri() . '/inc/admin/css/admin-welcome.css', array(), $relic_fashion_store_version );

		// Let's bail on theme activation.
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			update_option( 'Relic_Fashion_Store_notice_welcome', 1 );

		// No option? Let run the notice wizard again..
		} elseif( ! get_option( 'Relic_Fashion_Store_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['relic-fashion-store-hide-notice'] ) && isset( $_GET['relic_fashion_store_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['relic_fashion_store_notice_nonce'], 'relic_fashion_store_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'relic-fashion-store' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'relic-fashion-store' ) );
			}

			$hide_notice = sanitize_text_field( $_GET['relic-fashion-store-hide-notice'] );
			update_option( 'Relic_Fashion_Store_notice_welcome' . $hide_notice, 1 );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div id="message" class="updated relic-fashion-store-message">
			<a class="relic-fashion-store-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'relic-fashion-store-hide-notice', 'welcome' ) ), 'relic_fashion_store_hide_notices_nonce', 'relic_fashion_store_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'relic-fashion-store' ); ?></a>
			<p><?php printf( esc_html__( 'Welcome! Thank you for choosing relic fashion store! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'relic-fashion-store' ), '<a href="' . esc_url( admin_url( 'themes.php?page=relic-fashion-store-welcome' ) ) . '">', '</a>' ); ?></p>
			<p class="submit">
				<a class="button-secondary" href="<?php echo esc_url( admin_url( 'themes.php?page=relic-fashion-store-welcome' ) ); ?>"><?php esc_html_e( 'Get started with Relic Fashion Store', 'relic-fashion-store' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		global $relic_fashion_store_version;
		$theme = wp_get_theme( get_template() );

		// Drop minor version if 0
		$major_version = substr( $relic_fashion_store_version, 0, 3 );
		?>
		<div class="relic-fashion-store-theme-info">
				<h1>
					<?php esc_html_e('About', 'relic-fashion-store'); ?>
					<?php echo $theme->display( 'Name' ); ?>
					<?php printf( '%s', $major_version ); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo $theme->display( 'Description' ); ?></div>

				<div class="relic-fashion-store-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
				</div>
			</div>
		</div>

		<p class="relic-fashion-store-actions">
			<!-- Theme Demo -->
			<a href="<?php echo esc_url( 'http://demo.themerelic.com/relic-fashion-store' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Demo', 'relic-fashion-store' ); ?></a>

			<!-- Theme Details -->
			<a href="<?php echo esc_url('https://themerelic.com/wordpress-themes/relic-fashion-store/'); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'Theme Details', 'relic-fashion-store' ); ?></a>

			<!-- Theme Documentaion  -->
			<a href="<?php echo esc_url( 'https://themerelic.github.io/' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Documentation', 'relic-fashion-store' ); ?></a>

			<!-- Theme Suppoert  -->
			<a href="<?php echo esc_url( 'https://themerelic.com/support' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Support', 'relic-fashion-store' ); ?></a>

			<!-- Go To Pro -->
			<a href="<?php echo esc_url( 'https://themerelic.com/wordpress-themes/relic-fashion-store-pro' ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'Relic Fashion Store Pro', 'relic-fashion-store' ); ?></a>
		

			<!-- Review -->
			<a href="<?php echo esc_url( 'https://wordpress.org/support/theme/relic-fashion-store/reviews/#new-post' ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'Please Give Reviews', 'relic-fashion-store' ); ?></a>
		

			
		
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'relic-fashion-store-welcome' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'relic-fashion-store-welcome' ), 'themes.php' ) ) ); ?>">
				<?php echo $theme->display( 'Name' ); ?>
			</a>
			
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'relic-fashion-store-welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs Pro', 'relic-fashion-store' ); ?>
			</a>

			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'more_themes' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'relic-fashion-store-welcome', 'tab' => 'more_themes' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'More Themes', 'relic-fashion-store' ); ?>
			</a>

			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'relic-fashion-store-welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'relic-fashion-store' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
		$current_tab = empty( $_GET['tab'] ) ? 'about' : sanitize_title( $_GET['tab'] );

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases">
				<div class="under-the-hood two-col">
               
					<div class="col">
						<h3><?php esc_html_e( 'Theme Customizer', 'relic-fashion-store' ); ?></h3>
						<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'relic-fashion-store' ) ?></p>
						<p><a href="<?php echo admin_url( 'customize.php' ); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'relic-fashion-store' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Documentation', 'relic-fashion-store' ); ?></h3>
						<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'relic-fashion-store' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://themerelic.github.io/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Documentation', 'relic-fashion-store' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Got theme support question?', 'relic-fashion-store' ); ?></h3>
						<p><?php esc_html_e( 'Please put it in our dedicated support forum.', 'relic-fashion-store' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://themerelic.com/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support', 'relic-fashion-store' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Need more features?', 'relic-fashion-store' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'relic-fashion-store' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://themerelic.com/wordpress-themes/relic-fashion-store-pro/' ); ?>" class="button button-secondary"><?php esc_html_e( 'View PRO version', 'relic-fashion-store' ); ?></a></p>
					</div>

					<div class="col">
						<h3>
							<?php
							esc_html_e( 'Translate', 'relic-fashion-store' );
							echo ' ' . $theme->display( 'Name' );
							?>
						</h3>
						<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'relic-fashion-store' ) ?></p>
						<p>
							<a href="<?php echo esc_url( 'https://translate.wordpress.org/projects/wp-themes/relic-fashion-store' ); ?>" class="button button-secondary">
								<?php
								esc_html_e( 'Translate', 'relic-fashion-store' );
								echo ' ' . $theme->display( 'Name' );
								?>
							</a>
						</p>
					</div>

				</div>
			</div>

			<div class="return-to-dashboard">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates', 'relic-fashion-store' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'relic-fashion-store' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'relic-fashion-store' ) : esc_html_e( 'Go to Dashboard', 'relic-fashion-store' ); ?></a>
			</div>

		</div>
		<?php
	}

		/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'relic-fashion-store' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'relic_fashion_store_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 * @param  string $content
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}


	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'relic-fashion-store' ); ?></p>

			<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e('Features', 'relic-fashion-store'); ?></h3></th>
						<th><h3><?php esc_html_e('Relic Fashion Store', 'relic-fashion-store'); ?></h3></th>
						<th><h3 class="relic-fashion-store-pro-header"><a href="<?php echo esc_url('https://themerelic.com/wordpress-themes/relic-fashion-store-pro/'); ?>"><?php esc_html_e('Relic Fashion Store Pro', 'relic-fashion-store'); ?></a></h3></th>
					</tr>
					
					<!-- Header Section -->
					<tr>
						<td><h3><?php esc_html_e('Import Demo Data.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Pre Loaders.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Typography Options.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Typography Options.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Breadcrumbs.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Header Section', 'relic-fashion-store'); ?></h3></td>
						<td><?php echo esc_html_e('1','relic-fashion-store') ?></td>
						<td><?php echo esc_html_e('4','relic-fashion-store') ?></td>
					</tr>


					<tr>
						<td><h3><?php esc_html_e('Fonts , Fonts Size , Text Color', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('600+', 'relic-fashion-store'); ?></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('3+Different Style Slider Layout.', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Custom Archive Page Layout', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('7+', 'relic-fashion-store'); ?></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Header Mega Menu', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('4+ Different Layout Posts Slider Widget Sections', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('2+ Different Type Theme Layout', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('12+ Different Widget Layout', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Category Color Control In Customizer', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Viewer Counter  Options', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('Edit The Footer Copyright Text', 'relic-fashion-store'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( 'https://themerelic.com/wordpress-themes/relic-fashion-store-pro/' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e('View Pro','relic-fashion-store'); ?></a>
						</td>
					</tr>
		
				</tbody>
			</table>

		</div>
		<?php
	}

	/**
	 * Output the more themes screen
	 */
	public function more_themes_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>
			<div class="theme-browser rendered">
				<div class="themes wp-clearfix">
					<?php
						// Set the argument array with author name.
						$args = array(
							'author' => 'themerelic',
						);
						// Set the $request array.
						$request = array(
							'body' => array(
								'action'  => 'query_themes',
								'request' => serialize( (object)$args )
							)
						);
						$themes = $this->themerelic_get_themes( $request );
						$active_theme = wp_get_theme()->get( 'Name' );
						$counter = 1;

						// For currently active theme.
						foreach ( $themes->themes as $theme ) {
							if( $active_theme == $theme->name ) { ?>

								<div id="<?php echo $theme->slug; ?>" class="theme active">
									<div class="theme-screenshot">
										<img src="<?php echo $theme->screenshot_url ?>"/>
									</div>
									<h3 class="theme-name" ><strong><?php _e( 'Active', 'relic-fashion-store' ); ?></strong>: <?php echo $theme->name; ?></h3>
									<div class="theme-actions">
										<a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo get_site_url(). '/wp-admin/customize.php' ?>"><?php _e( 'Customize', 'relic-fashion-store' ); ?></a>
									</div>
								</div><!-- .theme active -->
							<?php
							$counter++;
							break;
							}
						}

						// For all other themes.
						foreach ( $themes->themes as $theme ) {
							if( $active_theme != $theme->name ) {
								// Set the argument array with author name.
								$args = array(
									'slug' => $theme->slug,
								);
								// Set the $request array.
								$request = array(
									'body' => array(
										'action'  => 'theme_information',
										'request' => serialize( (object)$args )
									)
								);
								$theme_details = $this->themerelic_get_themes( $request );
							?>
								<div id="<?php echo $theme->slug; ?>" class="theme">
									<div class="theme-screenshot">
										<img src="<?php echo $theme->screenshot_url ?>"/>
									</div>

									<h3 class="theme-name"><?php echo $theme->name; ?></h3>

									<div class="theme-actions">
										<?php if( wp_get_theme( $theme->slug )->exists() ) { ?>											
											<!-- Activate Button -->
											<a  class="button button-secondary activate"
												href="<?php echo wp_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet=' . urlencode( $theme->slug ) ), 'switch-theme_' . $theme->slug );?>" ><?php _e( 'Activate', 'relic-fashion-store' ) ?></a>
										<?php } else {
											// Set the install url for the theme.
											$install_url = add_query_arg( array(
													'action' => 'install-theme',
													'theme'  => $theme->slug,
												), self_admin_url( 'update.php' ) );
										?>
											<!-- Install Button -->
											<a data-toggle="tooltip" data-placement="bottom" title="<?php echo 'Downloaded ' . number_format( $theme_details->downloaded ) . ' times'; ?>" class="button button-secondary activate" href="<?php echo esc_url( wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ); ?>" ><?php _e( 'Install Now', 'relic-fashion-store' ); ?></a>
										<?php } ?>

										<a class="button button-primary load-customize hide-if-no-customize" target="_blank" href="<?php echo $theme->preview_url; ?>"><?php _e( 'Live Preview', 'relic-fashion-store' ); ?></a>
									</div>
								</div><!-- .theme -->
								<?php
							}
						}


					?>
				</div>
			</div><!-- .end div -->
		</div><!-- .ena wrapper -->
		<?php
	}

	/** 
	 * Get all our themes by using API.
	 */
	private function themerelic_get_themes( $request ) {

		// Generate a cache key that would hold the response for this request:
		$key = 'relic_fashion_store_' . md5( serialize( $request ) );

		// Check transient. If it's there - use that, if not re fetch the theme
		if ( false === ( $themes = get_transient( $key ) ) ) {

			// Transient expired/does not exist. Send request to the API.
			$response = wp_remote_post( 'http://api.wordpress.org/themes/info/1.0/', $request );

			// Check for the error.
			if ( !is_wp_error( $response ) ) {

				$themes = unserialize( wp_remote_retrieve_body( $response ) );

				if ( !is_object( $themes ) && !is_array( $themes ) ) {

					// Response body does not contain an object/array
					return new WP_Error( 'theme_api_error', 'An unexpected error has occurred' );
				}

				// Set transient for next time... keep it for 24 hours should be good
				set_transient( $key, $themes, 60 * 60 * 24 );
			}
			else {
				// Error object returned
				return $response;
			}
		}
		return $themes;
	}


}

endif;

return new Relic_Fashion_Store_Admin();
