<div class="wrap">
	<?php if ( isset($save_result) && $save_result === true ) : ?>
		<div class="updated settings-error notice is-dismissible"> 
			<p><strong>TACDIS settings updated.</strong></p>
		</div>
	<?php endif; ?>
	<?php if ( isset($save_result) && $save_result === false ) : ?>
		<div class="updated settings-error error is-dismissible"> 
			<p><strong>Something went wrong, settings not updated.</strong></p>
		</div>
	<?php endif; ?>
	<h1>TACDIS E-com settings</h1>
	<form name="form" action="<?php $form_action ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="tacdis_remote_host">Remote Host</label>
					</th>
					<td>
						<input
							name="tacdis_remote_host"
							id="tacdis_remote_host"
							type="text"
							value="<?php echo @$tacdis_remote_host ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_wheelchange_ver">Resource Url</label>
					</th>
					<td>
						<input
							name="tacdis_ecom_resource_url"
							id="tacdis_ecom_resource_url"
							type="text"
							value="<?php echo @$tacdis_ecom_resource_url ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_api_key">API Key</label>
					</th>
					<td>
						<input
							name="tacdis_api_key"
							id="tacdis_api_key"
							type="text"
							value="<?php echo @$tacdis_api_key ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_tenant_id">Tenant ID</label>
					</th>
					<td>
						<input
							name="tacdis_tenant_id"
							id="tacdis_tenant_id"
							type="text"
							value="<?php echo @$tacdis_tenant_id ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_tenant_id_2">Tenant ID (2)</label>
					</th>
					<td>
						<input
							name="tacdis_tenant_id_2"
							id="tacdis_tenant_id_2"
							type="text"
							value="<?php echo @$tacdis_tenant_id_2 ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_tenant_id_3">Tenant ID (3)</label>
					</th>
					<td>
						<input
							name="tacdis_tenant_id_3"
							id="tacdis_tenant_id_3"
							type="text"
							value="<?php echo @$tacdis_tenant_id_3 ?>"
							class="regular-text code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="tacdis_version_cache_expiretime">Version cache expiretime (in minutes)</label>
					</th>
					<td>
						<input
							name="tacdis_version_cache_expiretime"
							id="tacdis_version_cache_expiretime"
							type="text"
							value="<?php echo @$tacdis_version_cache_expiretime ?>"
							class="regular-text code">
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="tacdis-settings-submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>