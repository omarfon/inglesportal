<?php
$demo_data     = $args['$demo_data'];
$least_value   = $args['$least_value'];
$current_value = $args['$current_value'];
$qualified     = $args['$qualified'];

$demo_installed = Thim_Importer::get_key_demo_installed();
?>

<div class="tc-importer-wrapper" data-template="thim-importer">
</div>

<script type="text/html" id="tmpl-thim-importer">
	<?php if ( ! Thim_Importer::is_qualified() ): ?>
		<div class="requirements">
			<h3><?php esc_html_e( 'Requirements', 'thim-core' ); ?></h3>
			<table>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Directive', 'thim-core' ); ?></th>
					<th><?php esc_html_e( 'Least Suggested Value', 'thim-core' ); ?></th>
					<th><?php esc_html_e( 'Current Value', 'thim-core' ); ?></th>
				</tr>
				</thead>

				<tbody class="directives">
				<tr>
					<td><?php esc_html_e( 'memory_limit', 'thim-core' ); ?></td>
					<td><?php echo esc_html( $least_value['memory_limit'] ); ?></td>
					<td class="bold <?php echo $qualified['memory_limit'] ? 'qualified' : 'unqualified' ?>"><?php echo esc_html( $current_value['memory_limit'] ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'max_execution_time', 'thim-core' ); ?></td>
					<td><?php echo esc_html( $least_value['max_execution_time'] ); ?></td>
					<td class="bold <?php echo $qualified['max_execution_time'] ? 'qualified' : 'unqualified' ?>"><?php echo esc_html( $current_value['max_execution_time'] ); ?></td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<div class="theme-browser rendered">
		<div class="themes wp-clearfix">
			<# if ( _.size(data.demos) > 0 ) { #>
				<# _.each(data.demos, function(demo) { #>
					<div class="theme thim-demo {{demo.key == data.installed ? 'installed' : ''}}" data-thim-demo="{{demo.key}}">
						<span class="status btn-uninstall" data-text="<?php esc_attr_e( 'Uninstall', 'thim-core' ); ?>" data-install="<?php esc_attr_e( 'Installed', 'thim-core' ); ?>"></span>

						<div class="theme-screenshot thim-screenshot">
							<img src="{{demo['screenshot']}}" alt="{{demo['title']}}">
						</div>

						<h2 class="theme-name">{{demo['title']}}</h2>

						<div class="theme-actions">
							<button class="button button-primary action-import"><?php esc_html_e( 'Install', 'thim-core' ); ?></button>
							<a class="button button-secondary" href="{{demo['demo_url']}}" target="_blank"><?php esc_html_e( 'Preview', 'thim-core' ); ?></a>
						</div>
					</div>
				<# }); #>
			<# } else { #>
				<h3 class="text-center"><?php esc_html_e( 'No demo content.', 'thim-core' ); ?></h3>
			<# } #>
		</div>
	</div>
</script>