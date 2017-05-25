<?php
global $thim_dashboard;
$links = $thim_dashboard['theme_data']['links'];
?>
<div class="tc-box-body">
    <div class="tc-documentation-wrapper">
        <div class="list-boxes">
            <div class="box">
                <div class="left">
                    <span class="dashicons dashicons-admin-site"></span>
                </div>
                <div class="right">
                    <h3><?php esc_html_e( 'Knowledge Base', 'thim-core' ); ?></h3>
                    <p class="description"><?php esc_html_e( 'You can find detailed answers to almost all common issues regarding theme and plugins usage here.', 'thim-core' ); ?></p>
                    <a href="<?php echo esc_url( $links['knowledge'] ); ?>" target="_blank"><?php esc_html_e( 'Read more', 'thim-core' ); ?></a>
                </div>
            </div>
            <div class="box">
                <div class="left">
                    <span class="dashicons dashicons-book"></span>
                </div>
                <div class="right">
                    <h3><?php esc_html_e( 'Theme Documentation', 'thim-core' ); ?></h3>
                    <p class="description"><?php esc_html_e( 'A collection of step-by-step guides to help you install, customize and work effectively with the theme.', 'thim-core' ); ?></p>
                    <a href="<?php echo esc_url( $links['docs'] ); ?>" target="_blank"><?php esc_html_e( 'Read more', 'thim-core' ); ?></a>
                </div>
            </div>
            <div class="box">
                <div class="left">
                    <span class="dashicons dashicons-sos"></span>
                </div>

                <div class="right">
                    <h3><?php esc_html_e( 'Forum Support', 'thim-core' ); ?></h3>
                    <p class="description"><?php esc_html_e( 'If any problem arise while using the theme, this is where you can ask our technical supporters so that we can help you out.', 'thim-core' ); ?></p>
                    <a href="<?php echo esc_url( $links['support'] ); ?>" target="_blank"><?php esc_html_e( 'Read more', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>