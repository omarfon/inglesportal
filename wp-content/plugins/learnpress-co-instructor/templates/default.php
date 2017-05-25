<?php
if ( !defined( 'ABSPATH' ) || !defined( 'LP_ADDON_CO_INSTRUCTOR_PATH' ) ) {
	die(); // Exit if accessed directly
}

if ( $instructors ) {
	foreach ( $instructors as $instructor ) {
		$lp_info = get_the_author_meta( 'lp_info', $instructor );
		$link    = learn_press_user_profile_link( $instructor );
		?>
		<div class="thim-about-author thim-co-instructor" itemprop="contributor" itemscope itemtype="http://schema.org/Person">
			<div class="author-wrapper">
				<div class="author-avatar">
					<?php echo get_avatar( $instructor, 110 ); ?>
				</div>
				<div class="author-bio">
					<div class="author-top">
						<a itemprop="url" class="name" href="<?php echo esc_url( $link ); ?>">
							<span itemprop="name"><?php echo get_the_author_meta( 'display_name', $instructor ); ?></span>
						</a>
						<?php if ( isset( $lp_info['major'] ) && $lp_info['major'] ) : ?>
							<p class="job" itemprop="jobTitle"><?php echo esc_html( $lp_info['major'] ); ?></p>
						<?php endif; ?>
					</div>

				</div>
				<?php
				?>
				<div class="author-description" itemprop="description">
					<?php echo get_the_author_meta( 'description', $instructor ); ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<?php
	}
}