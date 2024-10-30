<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       escolhadigital.com
 * @since      1.0.0
 *
 * @package    Wpcpapi
 * @subpackage Wpcpapi/admin/partials
 * 
 *
 *  
 *    
 */

// Grab all options
$options = get_option($this->plugin_name);


// register jquery and style on initialization
wp_enqueue_script('bootstrap', plugins_url('../js/bootstrap.min.js', __FILE__), array('jquery'), '5.1.3', true);
wp_enqueue_script('scripts', plugins_url('../js/scripts.js', __FILE__), array('jquery'), '', true);
wp_enqueue_style('bootstrap', plugins_url('../css/bootstrap.min.css', __FILE__), false, '5.1.3', 'all', true);

?>
<div class="wrap_ed wrap_ed_settings pt-4" style="max-width: 95%;">
	<div class="row">
		<div class="col">
			<div class="col ms-4">
				<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
				<h6><?php _e('CONFIGURATIONS', 'hestia-integration'); ?></h6>
			</div>
		</div>
		<form method="post" name="webservice_options" id="webservice_options" action="options.php">

			<!-- SOFTWARE TYPE -->
			<?php
			$softwares = getWebserviceList();
			if (count($softwares) == 1) {
				$software = $softwares[0];
			?>
				<input type="hidden" name="<?php echo esc_attr($this->plugin_name); ?>[software]" value="<?php echo esc_attr($software); ?>" />
			<?php
			}
			?>

			<div class="card mb-3" style="max-width: 100%;">
				<?php
				if ($this->dev == true) {
					echo WPCP_API_DEV_URL;
				} else {
					echo WPCP_API_PROD_URL;
				}
				?>
			</div>

			<!-- TABS-->
			<div class="card mb-3" style="max-width: 100%;">

				<div class="col pt-3">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" data-bs-target="#wpcpapi-api" type="button" role="tab" aria-selected="true"><?php _e('API', 'hestia-integration'); ?></a>
						</li>
						<li class="nav-item display-conexao">
							<a class="nav-link" data-bs-toggle="tab" data-bs-target="#wpcpapi-invoices" type="button" role="tab" aria-selected="false" style="display: none"><?php _e('Invoices', 'hestia-integration'); ?></a>
						</li>
						<li class="nav-item display-conexao">
							<a class="nav-link" data-bs-toggle="tab" data-bs-target="#wpcpapi-statusmapping" type="button" role="tab" aria-selected="false"><?php _e('Status Mapping', 'hestia-integration'); ?></a>
						</li>
						<li class="nav-item display-conexao">
							<a class="nav-link" data-bs-toggle="tab" data-bs-target="#wpcpapi-products" type="button" role="tab" aria-selected="false" style="display: none"><?php _e('Products', 'hestia-integration'); ?></a>
						</li>
						<li class="nav-item display-conexao">
							<a class="nav-link" data-bs-toggle="tab" data-bs-target="#wpcpapi-cron" type="button" role="tab" aria-selected="false" style="display: none"><?php _e('CRON', 'hestia-integration'); ?></a>
						</li>
						<li class="nav-item ">
							<a class="nav-link" data-bs-toggle="tab" data-bs-target="#wpcpapi-dev" type="button" role="tab" aria-selected="false"><?php _e('Development', 'hestia-integration'); ?></a>
						</li>
					</ul>
				</div>

				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane fade show active" id="wpcpapi-api" role="tabpanel" aria-labelledby="v-pills-home-tab">
						<div class="ms-3 mt-4 pt-2 pe-2">

							<div class="row mb-3" STYLE="display: none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('URL', 'hestia-integration'); ?>" id="server" name="<?php echo esc_html($this->plugin_name); ?>[server]" value="<?php echo esc_attr($options['server']); ?>" />
										<span class="input-group-text">Example: https://xxxx.com/xxx?wsdl</span>
										<label for="server"><?php _e('URL', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display:none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Port', 'hestia-integration'); ?>" id="port" name="<?php echo esc_attr($this->plugin_name); ?>[port]" value="<?php echo (!empty($options['port'])) ? esc_attr($options['port']) : ""; ?>" />
										<label for="port"><?php _e('Port', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Username', 'hestia-integration'); ?>" id="user" name="<?php echo esc_attr($this->plugin_name); ?>[user]" value="<?php echo (!empty($options['user'])) ? esc_attr($options['user']) : ""; ?>" />
										<span class="input-group-text">Example: usrnet</span>
										<label for="user"><?php _e('Username', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="password" class="form-control" placeholder="<?php _e('Password', 'hestia-integration'); ?>" id="pass" name="<?php echo esc_attr($this->plugin_name); ?>[pass]" value="<?php echo (!empty($options['pass'])) ? esc_attr($options['pass']) : ""; ?>" />
										<span class="input-group-text">Example: testuser</span>
										<label for="pass"><?php _e('Password', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display:none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Key', 'hestia-integration'); ?>" id="key" name="<?php echo esc_attr($this->plugin_name); ?>[key]" value="<?php echo (!empty($options['key'])) ? esc_attr($options['key']) : ""; ?>" />
										<label for="key"><?php _e('Key', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display:none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Secret', 'hestia-integration'); ?>" id="secret" name="<?php echo esc_attr($this->plugin_name); ?>[secret]" value="<?php echo (!empty($options['secret'])) ? esc_attr($options['secret']) : ""; ?>" />
										<label for="secret"><?php _e('Secret', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display:none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Client ID', 'hestia-integration'); ?>" id="client_id" name="<?php echo esc_attr($this->plugin_name); ?>[client_id]" value="<?php echo (!empty($options['client_id'])) ? esc_attr($options['client_id']) : ""; ?>" />
										<label for="client_id"><?php _e('Client ID', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<?php if (function_exists('wc_get_pickup_points')) { ?>
								<?php $pickups = wc_get_pickup_points();
								if ($pickups['error']) {
									echo "<p style='color:red'>" . esc_html($pickups['error']) . "</p>";
									$displayNone = 'style="display: none"';
									echo "<style>.display-conexao{display:none}</style>";
								} else {
								?>
									<div class="row mb-3">
										<div class="ps-0 pe-0">
											<div class="form-floating">
												<select class="form-select" style="max-width: 100% !important;" id="point_pickup" name="<?php echo esc_attr($this->plugin_name); ?>[point_pickup]">
													<option></option>
													<?php

													foreach ($pickups as $entry => $value) {
														$selected = ($options['point_pickup'] == $value['id']) ? "selected=\"selected\"" : "";
													?>
														<option value="<?php echo esc_attr($value['id']); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($value['name']); ?></option>
													<?php
													}
													?>
												</select>
												<label for="point_pickup"><?php _e(' Pickup Points', 'hestia-integration'); ?></label>
											</div>
										</div>
									</div>
							<?php
								}
							} ?>

						</div>
					</div>
					<div class="tab-pane fade" id="wpcpapi-statusmapping" role="tabpanel" <?php echo esc_attr($displayNone); ?>>
						<div class="ms-3 mt-4 pe-2">

							<?php if ('WPCP_API_ORDER_STATUS_CHANGE' == true) { ?>
								<?php if (function_exists('wc_get_order_statuses')) { ?>
									<?php if ($order_status = wc_get_order_statuses()) { ?>
										<div class="row mb-3">
											<div class="ps-0 pe-0">
												<div class="form-floating">
													<select class="form-select" style="max-width: 100% !important;" id="order_status_waiting" name="<?php echo esc_attr($this->plugin_name); ?>[order_status_waiting]">
														<option></option>
														<?php

														foreach ($order_status as $order_status_id => $order_status_data) {
															$selected = ($options['order_status_waiting'] == $order_status_id) ? "selected=\"selected\"" : "";
														?>
															<option value="<?php echo esc_attr($order_status_id); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($order_status_data); ?></option>
														<?php
														}
														?>
													</select>
													<label for="order_status_waiting"><?php _e('Order Status - Waiting Fulfillment', 'hestia-integration'); ?></label>
												</div>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>

							<?php if ('WPCP_API_ORDER_STATUS_CHANGE' == true) { ?>
								<?php if (function_exists('wc_get_order_statuses')) { ?>
									<?php if ($order_status = wc_get_order_statuses()) { ?>
										<div class="row mb-3">
											<div class="ps-0 pe-0">
												<div class="form-floating">
													<select class="form-select" style="max-width: 100% !important;" id="order_status_fulfillment" name="<?php echo esc_attr($this->plugin_name); ?>[order_status_fulfillment]">
														<option></option>
														<?php

														foreach ($order_status as $order_status_id => $order_status_data) {
															$selected = ($options['order_status_fulfillment'] == $order_status_id) ? "selected=\"selected\"" : "";
														?>
															<option value="<?php echo esc_attr($order_status_id); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($order_status_data); ?></option>
														<?php
														}
														?>
													</select>
													<label for="order_status_fulfillment"><?php _e('Order Status - Fulfilled', 'hestia-integration'); ?></label>
												</div>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>

							<div class="row mb-3" style="margin-bottom: 20px">
								<div class="ps-0 pe-0">
									<h5><?php _e('Same Day Zones', 'hestia-integration'); ?></h5>
									<div class="form-floating">
										<?php
										$delivery_zones = WC_Shipping_Zones::get_zones();
										foreach ((array) $delivery_zones as $key => $the_zone) {
											echo '<h6>' . esc_html($the_zone['zone_name']) . '</h6>'

										?>
											<div class="list-group list-group-checkable">
												<?php
												foreach ($the_zone['shipping_methods'] as $shipping_method) {
													$shipping_method = (array) $shipping_method;
													$checked = '';
													$checked = (isset($options['samedayzone'][$the_zone['id'] . '_' . $shipping_method['instance_id']]) && $options['samedayzone'][$the_zone['id'] . '_' . $shipping_method['instance_id']] == $the_zone['id'] . '_' . $shipping_method['instance_id']) ? "checked=\"checked\"" : "";

												?>
													<div class="list-group-item list-group-item-action">
														<input type="checkbox" class="form-radui-input radiozones" id="samedayzone" name="<?php echo esc_attr($this->plugin_name); ?>[samedayzone][<?php echo esc_attr($the_zone['id']); ?>_<?php echo esc_attr($shipping_method['instance_id']); ?>]" value="<?php echo esc_attr($the_zone['id'] . '_' . $shipping_method['instance_id']); ?>" <?php echo esc_attr($checked); ?> />
														<label for="dev" class="form-check-label"><?php echo esc_html($shipping_method['instance_settings']['title']); ?></label>
													</div>
												<?php } ?>

											</div>
										<?php } ?>

									</div>
								</div>
							</div>

							<div class="row mb-3">
								<div class="ps-0 pe-0">
									<h5><?php _e('Standard Zones', 'hestia-integration'); ?></h5>
									<div class="form-floating">
										<?php
										$delivery_zones = WC_Shipping_Zones::get_zones();
										foreach ((array) $delivery_zones as $key => $the_zone) {
											echo '<h6>' . esc_html($the_zone['zone_name']) . '</h6>'

										?>
											<div class="list-group list-group-checkable">
												<?php
												foreach ($the_zone['shipping_methods'] as $shipping_method) {
													$shipping_method = (array) $shipping_method;
													$checked = (isset($options['standardzones'][$the_zone['id'] . '_' . $shipping_method['instance_id']]) && $options['standardzones'][$the_zone['id'] . '_' . $shipping_method['instance_id']] == $the_zone['id'] . '_' . $shipping_method['instance_id']) ? "checked=\"checked\"" : "";

												?>
													<div class="list-group-item list-group-item-action">
														<input type="checkbox" class="form-radio-input radiozones" id="standardzones" name="<?php echo esc_attr($this->plugin_name); ?>[standardzones][<?php echo esc_attr($the_zone['id']); ?>_<?php echo esc_attr($shipping_method['instance_id']); ?>]" value="<?php echo  esc_attr($the_zone['id'] . '_' . $shipping_method['instance_id']); ?>" <?php echo esc_attr($checked); ?> />
														<label for="dev" class="form-check-label"><?php echo esc_html($shipping_method['instance_settings']['title']); ?></label>
													</div>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="wpcpapi-invoices" role="tabpanel" <?php echo esc_attr($displayNone); ?> style="display: none">
						<div class="ms-3 mt-4 pe-2">

							<?php if ('WPCP_API_ORDER_STATUS_CHANGE' == true) { ?>
								<?php if (function_exists('wc_get_order_statuses')) { ?>
									<?php if ($order_status = wc_get_order_statuses()) { ?>
										<div class="row mb-3">
											<div class="ps-0 pe-0">
												<div class="form-floating">
													<select class="form-select" style="max-width: 100% !important;" id="order_status" name="<?php echo esc_attr($this->plugin_name); ?>[order_status]">
														<option></option>
														<?php

														foreach ($order_status as $order_status_id => $order_status_data) {
															$selected = ($options['order_status'] == $order_status_id) ? "selected=\"selected\"" : "";
														?>
															<option value="<?php echo esc_attr($order_status_id); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($order_status_data); ?></option>
														<?php
														}
														?>
													</select>
													<label for="order_status"><?php _e('Order Change Status', 'hestia-integration'); ?></label>
												</div>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>

							<!-- DOCUMENT TYPE -->
							<div class="row mb-3">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('Document type', 'hestia-integration'); ?>" id="document" name="<?php echo esc_attr($this->plugin_name); ?>[document]" value="<?php echo (!empty($options['document'])) ? esc_attr($options['document']) : ""; ?>" />
										<span class="input-group-text">Example: FS</span>
										<label for="document"><?php _e('Document type', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="tab-pane fade" id="wpcpapi-products" role="tabpanel" aria-labelledby="v-pills-messages-tab" <?php echo esc_attr($displayNone); ?>>
						<div class="ms-1 mt-4 pe-2">

							<h6>Stock Management</h6>

							<!-- CHECKOUT -->
							<div class="row mb-3">
								<label for="server" v class="mb-3">Check availability on checkout?</label>
								<div class="list-group list-group-checkable">
									<div class="col">
										<?php
										$check_checkout_stock = $options['check_checkout_stock'];
										$checked = ($check_checkout_stock == 1) ? 'checked="checked"' : '';
										?>
										<div class="list-group-item list-group-item-action">
											<input type="radio" class="form-check-input" id="stock-true" name="<?php echo esc_attr($this->plugin_name); ?>[check_checkout_stock]" value="1" <?php echo esc_attr($checked); ?>>
											<label class="form-check-label" for="stock-true"><?php _e('Active', 'hestia-integration'); ?></label>
										</div>
										<?php
										$checked = ($check_checkout_stock != 1) ? 'checked="checked"' : '';
										?>
										<div class="list-group-item list-group-item-action">
											<input type="radio" class="form-check-input" id="stock-false" name="<?php echo esc_attr($this->plugin_name); ?>[check_checkout_stock]" value="0" <?php echo esc_attr($checked); ?>>
											<label class="form-check-label" for="stock-false"><?php _e('Disabled', 'hestia-integration'); ?></label>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="tab-pane fade" id="wpcpapi-cron" role="tabpanel" aria-labelledby="v-pills-settings-tab" <?php echo esc_attr($displayNone); ?>>
						<div class="ms-3 mt-4 pe-2">
							<h6>Config automatic update</h6>

							<div class="row mb-3">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" class="form-control" placeholder="<?php _e('From x Time to x', 'hestia-integration'); ?>" id="cron_minutes" name="<?php echo esc_attr($this->plugin_name); ?>[cron_minutes]" value="<?php echo (!empty($options['cron_minutes'])) ? esc_attr($options['cron_minutes']) : ""; ?>" />
										<span class="input-group-text">minutes</span>
										<label for="cron_minutes"><?php _e('From x Time to x', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="wpcpapi-dev" role="tabpanel" aria-labelledby="v-pills-settings-tab">
						<div class="ms-3 mt-4 pe-2">

							<div class="list-group list-group-checkable">
								<div class="row mb-3">
									<div class="list-group-item list-group-item-action">
										<input type="checkbox" class="form-check-input" id="dev" name="<?php echo esc_attr($this->plugin_name); ?>[dev]" value="<?php echo (!empty($options['dev'])) ? 'true' : "false"; ?>" <?php echo (!empty($options['dev'])) ? 'checked="checked"' : ""; ?> />
										<label for="dev" class="form-check-label"><?php _e('Staging Active', 'hestia-integration'); ?></label>
									</div>
								</div>

								<div class="row mb-3">
									<div class="list-group-item list-group-item-action">
										<input type="checkbox" class="form-check-input" id="debug" name="<?php echo esc_attr($this->plugin_name); ?>[debug]" value="<?php echo (!empty($options['debug'])) ? esc_attr($options['debug']) : ""; ?>" />
										<label for="debug" class="form-check-label"><?php _e('Debug Active', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display: none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" placeholder="<?php _e('Import Limit', 'hestia-integration'); ?>" class="form-control" id="limit" name="<?php echo esc_attr($this->plugin_name); ?>[limit]" value="<?php echo (!empty($options['limit'])) ? esc_html($options['limit']) : ""; ?>" />
										<span class="input-group-text"><?php _e('minutes', 'hestia-integration'); ?></span>
										<label for="limit"><?php _e('Import Limit', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

							<div class="row mb-3" style="display: none">
								<div class="ps-0 pe-0">
									<div class="form-floating">
										<input type="text" placeholder="<?php _e('Send Debug To', 'hestia-integration'); ?>" class="form-control" id="debug-emails" name="<?php echo esc_attr($this->plugin_name); ?>[debug-emails]" value="<?php echo (!empty($options['debug-emails'])) ? esc_attr($options['debug-emails']) : ""; ?>" />
										<span class="input-group-text">Example: email@domain.com</span>
										<label for="debug-emails"><?php _e('Send Debug To', 'hestia-integration'); ?></label>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>

			</div>

			<?php
			settings_fields($this->plugin_name);
			do_settings_sections($this->plugin_name);
			?>

			<div class="row">
				<div class="btn-group" role="group">
					<?php // submit_button('Save all changes', 'btn btn-primary', 'submit', TRUE); 
					?>
					<button type="submit" class="btn btn-primary"><?php _e('SAVE ALL CHANGES', 'hestia-integration'); ?></button>
				</div>
			</div>

		</form>
	</div>
</div>