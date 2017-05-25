<?php
$url_settings      = Thim_For_Developer::get_url_download( 'settings' );
$url_theme_options = Thim_For_Developer::get_url_download( 'theme_options' );
$url_content       = Thim_For_Developer::get_url_download( 'content' );
$url_php_info      = Thim_For_Developer::get_url_download( 'php_info' );
$url_php_info .= '&TB_iframe=true&width=1024&height=600';

?>
<div class="tc-wrapper-developer">
    <div class="row">
        <div class="col-md-4">
            <div class="tc-box">
                <div class="tc-box-header">
                    <h2 class="box-title"><?php esc_html_e( 'Export content.xml', 'thim-core' ); ?></h2>
                </div>
                <div class="tc-box-body text-center">
                    <a type="button" class="button button-secondary tc-button"
                       href="<?php echo esc_url( $url_content ); ?>"><?php esc_html_e( 'Download', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tc-box">
                <div class="tc-box-header">
                    <h2 class="box-title"><?php esc_html_e( 'Export settings.dat', 'thim-core' ); ?></h2>
                </div>
                <div class="tc-box-body text-center">
                    <a type="button" class="button button-secondary tc-button" href="<?php echo esc_url( $url_settings ); ?>"
                       download="<?php echo esc_url( $url_settings ); ?>"><?php esc_html_e( 'Download', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tc-box">
                <div class="tc-box-header">
                    <h2 class="box-title"><?php esc_html_e( 'Export theme_options.dat', 'thim-core' ); ?></h2>
                </div>
                <div class="tc-box-body text-center">
                    <a type="button" class="button button-secondary tc-button" href="<?php echo esc_url( $url_theme_options ); ?>"
                       download="<?php echo esc_url( $url_theme_options ); ?>"><?php esc_html_e( 'Download', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tc-box">
                <div class="tc-box-header">
                    <h2 class="box-title"><?php esc_html_e( 'PHP\'s configuration', 'thim-core' ); ?></h2>
                </div>
                <div class="tc-box-body text-center">
                    <a type="button" class="button button-secondary tc-button thickbox" href="<?php echo esc_url( $url_php_info ); ?>"><?php esc_html_e( 'Show', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tc-box">
                <div class="tc-box-header">
                    <h2 class="box-title"><?php esc_html_e( 'Developer docs', 'thim-core' ); ?></h2>
                </div>
                <div class="tc-box-body text-center">
                    <a type="button" class="button button-primary tc-button" href="https://bitbucket.org/foobla/thim-core/wiki/Home" target="_blank"><?php esc_html_e( 'Show', 'thim-core' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>