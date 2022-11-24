<?php
/**
 * Template Name: Email stat block
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
					<tbody>
						<tr>
							<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 50px; padding-bottom: 20px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
								<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td style="text-align:center;width:100%;">
											<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 28px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
												<?php echo esc_html__( 'Some More Numbers', 'cartflows' ); ?></h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="5" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td>
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; font-size: 14px; text-align: center;">
														<span style="font-size:16px;"><?php echo esc_attr( $from_date ) . ' - ' . esc_attr( $to_date ); ?>
														</span>
													</p>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php

if ( _is_cartflows_pro() ) {
	?>

	<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
		<tbody>
			<tr>
				<td>
					<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
						<tbody>
							<tr>
								<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="25%">
									<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="text-align:center;width:100%;padding-top:5px;">
												<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
													<?php echo esc_attr( $total_orders ); ?></h1>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Order Placed',
																	'cartflows'
																)
																?>
															</span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-2" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="16.666666666666668%">
									<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="text-align:center;width:100%;padding-top:5px;">
												<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
													<?php echo esc_attr( $total_visits ); ?></h1>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Total Visits',
																	'cartflows'
																)
																?>
															</span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-3" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="33.333333333333336%">
									<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="text-align:center;width:100%;padding-top:5px;">
												<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
													<?php echo wc_price( $order_bump_revenue ); ?></h1>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p dir="ltr" style="margin: 0; font-size: 14px; text-align: center; letter-spacing: normal;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Order Bumps Revenue',
																	'cartflows'
																)
																?>
															</span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-4" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="25%">
									<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="text-align:center;width:100%;padding-top:5px;">
												<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
													<?php echo wc_price( $offers_revenue ); ?></h1>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Offers Revenue',
																	'cartflows'
																)
																?>
															</span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<?php
} else {
	?>
	<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
		<tbody>
			<tr>
				<td>
					<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
						<tbody>
							<tr>
								<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="25%">
									<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="text-align:center;width:100%;padding-top:5px;">
												<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
													<?php echo esc_attr( $total_orders ); ?></h1>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Order Placed',
																	'cartflows'
																)
																?>
															</span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-2" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="16.666666666666668%">
									<table border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="width:100%;padding-right:0px;padding-left:0px;padding-top:5px;">
												<div align="center" style="line-height:10px"><img src=<?php echo esc_url( $lock_icon ); ?> style="display: block; height: auto; border: 0; width: 35px; max-width: 100%;" width="35" /></div>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;"><?php echo esc_html__( 'Total Visits', 'cartflows' ); ?></span>
														</p>
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:12px;"><?php echo esc_html__( 'CartFlows Pro', 'cartflows' ); ?></span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-3" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="33.333333333333336%">
									<table border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="width:100%;padding-right:0px;padding-left:0px;padding-top:5px;">
												<div align="center" style="line-height:10px"><img src=<?php echo esc_url( $lock_icon ); ?> style="display: block; height: auto; border: 0; width: 35px; max-width: 100%;" width="35" /></div>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p dir="ltr" style="margin: 0; font-size: 14px; text-align: center; letter-spacing: normal;">
															<span style="font-size:16px;">
																<?php
																echo esc_html__(
																	'Order Bumps Revenue',
																	'cartflows'
																)
																?>
															</span>
														</p>
														<p dir="ltr" style="margin: 0; font-size: 14px; text-align: center; letter-spacing: normal;">
															<span style="font-size:12px;"><?php echo esc_html__( 'CartFlows Pro', 'cartflows' ); ?></span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td class="column column-4" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="25%">
									<table border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
										<tr>
											<td style="width:100%;padding-right:0px;padding-left:0px;padding-top:5px;">
												<div align="center" style="line-height:10px"><img src=<?php echo esc_url( $lock_icon ); ?> style="display: block; height: auto; border: 0; width: 35px; max-width: 100%;" width="35" /></div>
											</td>
										</tr>
									</table>
									<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
										<tr>
											<td style="padding-bottom:70px;padding-left:10px;padding-right:10px;padding-top:10px;">
												<div style="font-family: sans-serif">
													<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:16px;"><?php echo esc_html__( 'Offers Revenue', 'cartflows' ); ?></span>
														</p>
														<p style="margin: 0; font-size: 14px; text-align: center;">
															<span style="font-size:12px;"><?php echo esc_html__( 'CartFlows Pro', 'cartflows' ); ?></span>
														</p>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
