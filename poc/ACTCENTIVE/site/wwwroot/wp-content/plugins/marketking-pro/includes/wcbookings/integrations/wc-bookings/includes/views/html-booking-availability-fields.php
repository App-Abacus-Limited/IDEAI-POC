<?php
/**
 * Display global availability fields.
 *
 * @package Woocommerce/Bookings
 * @var $availability WC_Global_Availability
 * @var boolean $show_google_event
 */

$intervals = array();

$intervals['months'] = array(
	'1'  => __( 'January', 'marketking' ),
	'2'  => __( 'February', 'marketking' ),
	'3'  => __( 'March', 'marketking' ),
	'4'  => __( 'April', 'marketking' ),
	'5'  => __( 'May', 'marketking' ),
	'6'  => __( 'June', 'marketking' ),
	'7'  => __( 'July', 'marketking' ),
	'8'  => __( 'August', 'marketking' ),
	'9'  => __( 'September', 'marketking' ),
	'10' => __( 'October', 'marketking' ),
	'11' => __( 'November', 'marketking' ),
	'12' => __( 'December', 'marketking' ),
);

$intervals['days'] = array(
	'1' => __( 'Monday', 'marketking' ),
	'2' => __( 'Tuesday', 'marketking' ),
	'3' => __( 'Wednesday', 'marketking' ),
	'4' => __( 'Thursday', 'marketking' ),
	'5' => __( 'Friday', 'marketking' ),
	'6' => __( 'Saturday', 'marketking' ),
	'7' => __( 'Sunday', 'marketking' ),
);

/* translators: 1: week number */
$week_string = __( 'Week %s', 'marketking' );
for ( $i = 1; $i <= 53; $i ++ ) {
	$intervals['weeks'][ $i ] = sprintf( $week_string, $i );
}

if ( ! isset( $availability['type'] ) ) {
	$availability['type'] = 'custom';
}
if ( ! isset( $availability['priority'] ) ) {
	$availability['priority'] = 10;
}
$availability_title = ! empty( $availability['title'] ) ? $availability['title'] : '';
$gcal_event_id      = ! empty( $availability['gcal_event_id'] ) ? $availability['gcal_event_id'] : '';
$availability_id    = ! empty( $availability['ID'] ) ? $availability['ID'] : '';

$is_google         = ! empty( $availability['gcal_event_id'] );
$is_rrule          = 'rrule' === $availability['type'];
$show_google_event = isset( $_GET['show'] ) && 'google-events' === $_GET['show'];
?>
<tr data-id="<?php echo esc_attr( $availability_id ); ?>" <?php echo $is_google ? 'class="google-event"' : ''; ?> >
<?php


if ( ! $show_google_event ) : ?>
	<td class="sort">&nbsp;</td>
<?php endif; ?>

<td><input type="hidden" name="wc_booking_availability_id[]" value="<?php echo esc_attr(
		$availability_id ); ?>">
	<div class="select wc_booking_availability_type">

		<?php
		if ( $is_google ) {
			?>
			<div class="bookings-to-label-row">
				<p>
					<strong>
						<?php
						if ( $is_rrule ) {
							esc_html_e( 'Google Recurring Event', 'marketking' );
						} else {
							esc_html_e( 'Google Event', 'marketking' );
						}
						?>
					</strong>
				</p>
			</div>
			<?php
		}
		?>
		<select name="wc_booking_availability_type[]" <?php echo $is_google ? 'style="display: none;"' : ''; ?>>
			<?php if ( $is_rrule ) : ?>
				<option value="rrule" selected></option>
			<?php elseif ( $is_google ) : ?>
				<option value="<?php echo esc_attr( $availability['type'] ); ?>" selected></option>
			<?php else : ?>
			<option value="custom" <?php selected( $availability['type'], 'custom' ); ?>><?php esc_html_e( 'Date range', 'marketking' ); ?></option>
			<option value="custom:daterange" <?php selected( $availability['type'], 'custom:daterange' ); ?>><?php esc_html_e( 'Date range with time', 'marketking' ); ?></option>
			<option value="months" <?php selected( $availability['type'], 'months' ); ?>><?php esc_html_e( 'Range of months', 'marketking' ); ?></option>
			<option value="weeks" <?php selected( $availability['type'], 'weeks' ); ?>><?php esc_html_e( 'Range of weeks', 'marketking' ); ?></option>
			<option value="days" <?php selected( $availability['type'], 'days' ); ?>><?php esc_html_e( 'Range of days', 'marketking' ); ?></option>
			<optgroup label="<?php esc_html_e( 'Time Ranges', 'marketking' ); ?>">
				<option value="time" <?php selected( $availability['type'], 'time' ); ?>><?php esc_html_e( 'Time Range (all week)', 'marketking' ); ?></option>
				<option value="time:range" <?php selected( $availability['type'], 'time:range' ); ?>><?php esc_html_e( 'Date Range with recurring time', 'marketking' ); ?></option>
				<?php foreach ( $intervals['days'] as $key => $label ) : ?>
					<option value="time:<?php echo esc_attr( $key ); ?>" <?php selected( $availability['type'], 'time:' . $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</optgroup>
		</select>
	<?php endif; ?>
	</div>
</td>
<td <?php echo $is_rrule ? 'colspan="3"' : 'style="border-right:0;"'; ?>>
	<div class="bookings-datetime-select-from">
		<div class="rrule">
			<?php
			if ( $is_rrule ) {
				$is_all_day  = false === strpos( $availability['from'], ':' );
				$date_format = $is_all_day ? 'Y-m-d' : 'Y-m-d g:i A';
				$from_date   = new WC_DateTime( $availability['from'] );
				$to_date     = new WC_DateTime( $availability['to'] );
				$timezone    = new DateTimeZone( wc_booking_get_timezone_string() );
				$from_date->setTimezone( $timezone );
				$to_date->setTimezone( $timezone );
				$human_readable_options = array(
					'date_formatter' => function ( $date ) use ( $date_format ) {
						return $date->format( $date_format );
					},
				);

				if ( $is_all_day ) {
					$to_date->sub( new DateInterval( 'P1D' ) );
				}

				try {
					$rset = new \RRule\RSet( $availability['rrule'], $is_all_day ? $from_date->format( $date_format ) : $from_date );
					?>
					<strong>
						<?php echo esc_html( $from_date->format( $date_format ) ); ?>
						<?php esc_html_e( 'to', 'marketking' ); ?>
						<?php echo esc_html( $to_date->format( $date_format ) ); ?>
					</strong>
					<br/>
					<?php
					esc_html_e( 'Repeating ', 'marketking' );
					foreach ( $rset->getRRules() as $rrule ) {
						echo esc_html( $rrule->humanReadable( $human_readable_options ) );
					}
					if ( $rset->getExDates() ) {
						esc_html_e( ', except ', 'marketking' );
						echo esc_html(
							join(
								' and ',
								array_map(
									function ( $date ) use ( $date_format ) {
										return $date->format( $date_format );
									},
									$rset->getExDates()
								)
							)
						);
					}
				} catch ( Exception $e ) {
					?>
					<strong><?php esc_html_e( 'Invalid recurring rule', 'marketking' ); ?></strong>
					<br/>
					<?php
					echo esc_html( $e->getMessage() );
				}
			}
			?>
		</div>
		<div class="select from_day_of_week">
			<select name="wc_booking_availability_from_day_of_week[]">
				<?php foreach ( $intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_month">
			<select name="wc_booking_availability_from_month[]">
				<?php foreach ( $intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_week">
			<select name="wc_booking_availability_from_week[]">
				<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="from_date">
			<?php
			$from_date = '';
			$from_time = '';
			if ( 'custom' === $availability['type'] && ! empty( $availability['from'] ) ) {
				$from_date = $availability['from'];
			} elseif ( in_array( $availability['type'], array(
					'time:range',
					'custom:daterange'
				), true ) && ! empty( $availability['from_date'] ) ) {
				$from_date = $availability['from_date'];
			}

			if ( strrpos( $availability['type'], 'time' ) === 0 || 'custom:daterange' === $availability['type'] ) {
				$from_time = $availability['from'];
			}
			if ( $is_google ) {
				?>
				<div class="bookings-to-label-row">
					<p>
						<strong>
							<?php echo esc_html( $from_date ); ?>
						</strong>
					</p>
				</div>
				<?php
			}
			?>
			<input type="text" <?php echo $is_google ? 'style="display: none;"' : 'class="date-picker"'; ?>
			       name="wc_booking_availability_from_date[]"
			       value="<?php echo esc_attr( $from_date ); ?>">
		</div>
		<div class="from_time">
			<?php
			if ( $is_google && $from_time ) {
				?>
				<div class="bookings-to-label-row">
					<p>
						<strong>
							<?php echo esc_html( date_i18n( wc_bookings_time_format(), strtotime( $from_time ) ) ); ?>
						</strong>
					</p>
				</div>
				<?php
			}
			?>
			<label>
				<input type="time" <?php echo $is_google ? 'style="display: none;"' : 'class="time-picker"'; ?>
				       name="wc_booking_availability_from_time[]"
				       value="<?php echo esc_attr( $from_time ); ?>" placeholder="HH:MM"/>
			</label>
		</div>
	</div>
</td>
<td style="<?php echo $is_rrule ? 'display:none;' : 'border-right:0;'; ?>"
    class="bookings-to-label-row">
	<p><?php esc_html_e( 'to', 'marketking' ); ?></p>
	<p class="bookings-datetimerange-second-label"><?php esc_html_e( 'to', 'marketking' ); ?></p>
</td>
<td style="<?php echo $is_rrule ? 'display:none;' : ''; ?>">
	<div class='bookings-datetime-select-to'>
		<div class="select to_day_of_week">
			<select name="wc_booking_availability_to_day_of_week[]">
				<?php foreach ( $intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_month">
			<select name="wc_booking_availability_to_month[]">
				<?php foreach ( $intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_week">
			<select name="wc_booking_availability_to_week[]">
				<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] === (string) $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="to_date">
			<?php
			$to_date = '';
			$to_time = '';
			if ( 'custom' === $availability['type'] && ! empty( $availability['to'] ) ) {
				$to_date = $availability['to'];
			} elseif ( in_array( $availability['type'], array(
					'time:range',
					'custom:daterange'
				), true ) && ! empty( $availability['to_date'] ) ) {
				$to_date = $availability['to_date'];
			}

			if ( strrpos( $availability['type'], 'time' ) === 0 || 'custom:daterange' === $availability['type'] ) {
				$to_time = $availability['to'];
			}

			if ( $is_google ) {
				?>
				<div class="bookings-to-label-row">
					<p>
						<strong>
							<?php echo esc_html( $to_date ); ?>
						</strong>
					</p>
				</div>
				<?php
			}
			?>
			<input type="text" <?php echo $is_google ? 'style="display: none;"' : 'class="date-picker"'; ?>
			       name="wc_booking_availability_to_date[]"
			       value="<?php echo esc_attr( $to_date ); ?>"/>
		</div>

		<div class="to_time">
			<?php
			if ( $is_google && $to_time ) {
				?>
				<div class="bookings-to-label-row">
					<p>
						<strong>
							<?php echo esc_html( date_i18n( wc_bookings_time_format(), strtotime( $to_time ) ) ); ?>
						</strong>
					</p>
				</div>
				<?php
			}
			?>
			<input type="time" <?php echo $is_google ? 'style="display: none;"' : 'class="time-picker"'; ?>
			       name="wc_booking_availability_to_time[]"
			       value="<?php echo esc_attr( $to_time ); ?>" placeholder="HH:MM"/>
		</div>
	</div>
</td>
<td>
	<div class="select">
		<?php if ( $is_google ) : ?>
			<div class="bookings-to-label-row">
				<p>
					<?php esc_html_e( 'No', 'marketking' ); ?>
				</p>
			</div>
		<?php endif; ?>
		<select <?php echo $is_google ? 'style="display: none;"' : ''; ?>
				name="wc_booking_availability_bookable[]">
			<option value="no" <?php selected( isset( $availability['bookable'] ) && 'no' === $availability['bookable'], true ); ?>><?php esc_html_e( 'No', 'marketking' ); ?></option>
			<?php if ( ! $is_google ) : ?>
				<option value="yes" <?php selected( isset( $availability['bookable'] ) && 'yes' === $availability['bookable'], true ); ?>><?php esc_html_e( 'Yes', 'marketking' ); ?></option>
			<?php endif; ?>
		</select>
	</div>
</td>
<?php if ( ! empty( $show_title ) ) : ?>
	<td>
		<div class="title">
			<?php if ( $is_google ) : ?>
				<div class="bookings-to-label-row">
					<p>
						<?php echo esc_html( $availability_title ); ?>
					</p>
				</div>
			<?php endif; ?>
			<label>
				<input <?php echo $is_google ? 'style="display: none;"' : ''; ?>
						name="wc_booking_availability_title[]"
						value="<?php echo esc_attr( $availability_title ); ?>"
						style="border:1px solid #ddd;background-color:#fff;"/>
			</label>
		</div>
	</td>
<?php endif; ?>
<td>
	<div class="priority">
		<?php if ( $is_google ) : ?>
			<div class="bookings-to-label-row">
				<p>
					<?php echo esc_html( $availability['priority'] ); ?>
				</p>
			</div>

		<?php endif; ?>
		<p>
			<input <?php echo $is_google ? 'style="display: none;"' : ''; ?> type="number" name="wc_booking_availability_priority[]" value="<?php echo esc_attr( $availability['priority'] ); ?>" placeholder="10"/>
		</p>

		<input type="hidden" name="wc_booking_availability_gcal_event_id[]"
		       value="<?php echo esc_attr( $gcal_event_id ); ?>"/>

	</div>
</td>
<?php if ( ! empty( $show_title ) ) : ?>
	<?php do_action( 'woocommerce_bookings_extra_global_availability_fields', $availability ); ?>
<?php endif; ?>
<td class="remove">&nbsp;</td>
</tr>
