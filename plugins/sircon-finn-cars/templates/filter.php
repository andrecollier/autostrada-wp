<?php

namespace sircon\finncars;

$ns = $result->getNamespaces(true);
?>
<div class="filter-btn-wrapper">
	<button class="show-filters"><?php _e('Show/Hide filters', 'sircon-finn-cars') ?></button>
</div>
<form class="sfc-filter">
	<button type="button" class="reset_filter">Nullstill Filtering</button>
	
	

    <fieldset class="sfc-filter-group">
		<b class="sfc-filter-group-title-2">Lånekalkulator</b>
        <b class="sfc-filter-group-title">Kontantbeløp</b>
        <div class="sfc-2-col">
            <div class="finn_postsfilters_head_slide_one">
                <div class="gb_rangeslider_new_container">
                    <span id="amount_kontant">150000</span>
                    <span>,-</span>
                    <br />
                    <input id="finn_slider_one" type="range" min="0" max="10" value="150000" step="1">
                </div>

            </div>

        </div>
    </fieldset>

    <fieldset class="sfc-filter-group">
        <b class="sfc-filter-group-title">Løpetid</b>
        <div class="sfc-2-col">
            <div class="finn_postsfilters_head_slide_two">
                <div class="gb_rangeslider_new_container">
                    <span id="amount_kontant">10</span>
                    <span>år</span>
                    <br />
                    <input id="finn_slider_two" type="range" min="0" max="10" value="10" step="1">
                </div>
            </div>
        </div>
    </fieldset>
	
	<?php
	$id_prefix = 'sfc_';
	do_action('sircon_finn_cars_before_filter_output');

	if (\sircon\Options::get_option(FinnCars::OPTIONSPAGE_ID, 'filter_sortable') !== 'hide') {
		?>
		<fieldset class="sfc-filter-group">
			<b class="sfc-filter-group-title">Sortering</b>
			<div class="sfc-fieldwrapper prfxseo_show_flex">

				<select name="sort" id="<?= $id_prefix; ?>sort">
					<?php foreach ($result->children($ns['f'])->sort->children($ns['os'])->Query as $sorter) { ?>
						<option value="<?= $sorter->attributes($ns['f'])->sort; ?>"><?= $sorter->attributes()->title; ?></option>
					<?php } ?>
				</select>
			</div>
		</fieldset>
		<?php
	}

	$dealers = json_decode(\sircon\Options::get_option(FinnCars::OPTIONSPAGE_ID, 'dealers'), true) ?? [];
	if ($dealers) {
		?>
		<fieldset class="sfc-filter-group">
			<b class="sfc-filter-group-title"><?php _e('Dealer', 'sircon-finn-cars'); ?></b>
			<?php foreach ($dealers as $index => $dealer) { ?>
				<div class="sfc-fieldwrapper prfxseo_show_flex">
					<label class="container">
						<label for="<?= $id_prefix . '_orgid_' . $index; ?>"><?= $dealer['name']; ?></label>
						<input type="checkbox" id="<?= $id_prefix . '_orgid_' . $index; ?>" name="orgId" value="<?= $dealer['orgId']; ?>" />
						<span class="checkmark"></span>
					</label>
				</div>
			<?php } ?>
		</fieldset>
		<?php
	}
		

	foreach ($result->children($ns['f'])->filter as $filter) {
		if (\sircon\Options::get_option(FinnCars::OPTIONSPAGE_ID, 'filter_' . $filter->attributes()->name) === 'hide') {
			continue;
		}

		$show_total_results = \sircon\Options::get_option(FinnCars::OPTIONSPAGE_ID, 'show_total_results') === 'yes';
		$show_total_results_subfilter = \sircon\Options::get_option(FinnCars::OPTIONSPAGE_ID, 'show_total_results_subfilter') === 'yes';

		ob_start(); ?>
		<fieldset class="sfc-filter-group">
			<b class="sfc-filter-group-title"><?= $filter->attributes()->title; ?></b>
			<?php if ($filter->attributes()->range->__toString() === 'true') {
				$range_from_name = $filter->attributes()->name . '_from';
				$range_from_id = $id_prefix . $range_from_name;
				$range_to_name = $filter->attributes()->name . '_to';
				$range_to_id = $id_prefix . $range_to_name;

                $id = $filter->attributes()->name;

                $start = 0;
                $end = 100000000;

                $range_slider_terms = ['year', 'price', 'mileage', 'number_of_seats'];

                $sign = '';

                if ($id == 'year') {
                    $start = 1960;
                    $end = date("Y");
                }

                if ($id == 'price') {
                    $start = 2000;
                    $end = 2500000;
                    $sign = ' kr';
                }

                if ($id == 'mileage') {
                    $start = 15;
                    $end = 154000;
                    $sign = ' km';
                }

                if ($id == 'number_of_seats') {
                    $start = 1;
                    $end = 10;
                }

				if( in_array($id,$range_slider_terms) ){
					?>

						<div class="sfc-2-col">
							<div class="sfc-fieldwrapper prfxseo_show_flex">
								<div class="finn_postsfilters_range_slider_one <?php echo 'gb_' . $id . '_slider_data';?>">
									<!--<label for="amount"><span><?= $filter->attributes()->title; ?></span></label>-->
									<div class="yp_finn_postsfilters_range_slider_container">
									    <div id="min_amount">
										<?php
											echo $start;
											echo '<span> ' . $sign  . '</span>';
										?>
									    </div>
									    <div id="max_amount">
										<?php
											echo $end;
											echo '<span> ' . $sign  . '</span>';
										?>
									    </div>
									    <div id="<?php echo 'gb_' . $id . '_slider';?>"></div>
									</div>
								</div><br />
							</div>
							<div class="sfc-fieldwrapper prfxseo_show_flex" style="display: none !important;">
								<label for="<?= $range_from_id; ?>">Fra</label>
								<input type="number" name="<?= $range_from_name; ?>">
							</div>
							<div class="sfc-fieldwrapper prfxseo_show_flex" style="display: none !important;">
								<label for="<?= $range_to_id; ?>">Til</label>
								<input type="number" name="<?= $range_to_name; ?>">
							</div>
							<input type="hidden" class="<?php echo 'gb_' . $id . '_slider';?>_start" value="<?php echo $start; ?>" />
							<input type="hidden" class="<?php echo 'gb_' . $id . '_slider';?>_end" value="<?php echo $end; ?>" />
						</div>

						<script type="text/javascript">
							jQuery(document).ready(function($){
								var start = $('.<?php echo 'gb_' . $id . '_slider';?>_start');
								var end = $('.<?php echo 'gb_' . $id . '_slider';?>_end');
								jQuery( "#<?php echo 'gb_' . $id . '_slider';?>" ).slider({
									range: true,
									min: parseInt(start.val()),
									max: parseInt(end.val()),
									values: [ parseInt(start.val()), parseInt(end.val()) ],
									slide: function( event, ui ) {
										//var search_val = $('.fin_search_filter').val();
										//search_val = search_val.toLowerCase();

										$( ".<?php echo 'gb_' . $id . '_slider_data';?> #min_amount" ).html(ui.values[0] + '<?php echo '<span> ' . $sign  . '</span>'; ?>');
										$("input[name='<?php echo $range_from_name; ?>']").val(ui.values[0]);
										$( ".<?php echo 'gb_' . $id . '_slider_data';?> #max_amount" ).html(ui.values[1] + '<?php echo '<span> ' . $sign  . '</span>'; ?>');
										$("input[name='<?php echo $range_to_name; ?>']").val(ui.values[1]);

									},
									stop: function(event, ui){
										$('.sfc-filter').trigger('submit');
										//$('.sfc-filter input[type="checkbox"]:first-child').trigger('change');
										console.log( $('.sfc-filter input[type="checkbox"]:first-child').val() );
									}
								});

								$('.reset_filter').click(function(){
									var start = <?php echo $start; ?>;
									var end = <?php echo $end; ?>;

									$( ".<?php echo 'gb_' . $id . '_slider_data';?> #min_amount" ).html(start + '<?php echo '<span> ' . $sign  . '</span>'; ?>');
									$( ".<?php echo 'gb_' . $id . '_slider_data';?> #max_amount" ).html(end + '<?php echo '<span> ' . $sign  . '</span>'; ?>');

									$("input[name='<?php echo $range_to_name; ?>']").val(end);
									jQuery( "#<?php echo 'gb_' . $id . '_slider';?>" ).slider({
										range: true,
										min: start,
										max: end,
										values: [ start, end],
										slide: function( event, ui ) {
											//var search_val = $('.fin_search_filter').val();
											//search_val = search_val.toLowerCase();

											$( ".<?php echo 'gb_' . $id . '_slider_data';?> #min_amount" ).html(ui.values[0] + '<?php echo '<span> ' . $sign  . '</span>'; ?>');
											$("input[name='<?php echo $range_from_name; ?>']").val(ui.values[0]);
											$( ".<?php echo 'gb_' . $id . '_slider_data';?> #max_amount" ).html(ui.values[1] + '<?php echo '<span> ' . $sign  . '</span>'; ?>');
											$("input[name='<?php echo $range_to_name; ?>']").val(ui.values[1]);

										},
										stop: function(event, ui){
											$('.sfc-filter').trigger('submit');
											//$('.sfc-filter input[type="checkbox"]:first-child').trigger('change');
											console.log( $('.sfc-filter input[type="checkbox"]:first-child').val() );
										}
									});
								});
							});
						</script>
					<?php
				}else{
					?>

<!-- FILTER ID <?php /* <?= $id ?> */ ?> -->
<div class="sfc-2-col">
  <div class="sfc-fieldwrapper prfxseo_show_flex">
    <label for="<?php /* <?= $range_from_id; ?> */ ?>"></label>
    <input type="number" name="<?php /* <?= $range_from_name; ?> */ ?>" style="display: none;">
  </div>
  <div class="sfc-fieldwrapper prfxseo_show_flex">
    <label for="<?php /* <?= $range_to_id; ?> */ ?>"></label>
    <input type="number" name="<?php /* <?= $range_to_name; ?> */ ?>" style="display: none;">
  </div>
</div>
					<?php
				}
				?>
			<?php } else {
				$option_index = 0; ?>
				<?php foreach ($filter->children($ns['f'])->Query as $option) {
					$option_index++;
					?>
					<div class="sfc-fieldwrapper prfxseo_show_flex">
						<?php
						/*
							echo '<pre>';
								var_dump($option->attributes());
							echo '</pre>';
						*/
						?>
						<label class="container">
							<label for="<?= $id_prefix . $filter->attributes()->name . '_' . $option_index; ?>"><?= $option->attributes()->title; ?><?= $show_total_results ? '<span class="sfc-total-results"> (<span class="count">' . $option->attributes()->totalResults . '</span>)</span>' : ''; ?></label>
							<input type="checkbox" id="<?= $id_prefix . $filter->attributes()->name . '_' . $option_index; ?>" name="<?= $filter->attributes()->name; ?>" value="<?= $option->attributes($ns['f'])->filter; ?>" />
							<span class="checkmark"></span>
						</label>
					<?php
					foreach ($option->children($ns['f'])->filter as $subfilter) {
						$suboption_index = 0; ?>
						<div class="sfc-subfilter" style="padding-left: 20px">
							<?php foreach ($subfilter->children($ns['f'])->Query as $suboption) {
								$suboption_index++;
								?>
								<div class="sfc-fieldwrapper prfxseo_show_flex">
									<label class="container">
										<label for="<?= $id_prefix . $subfilter->attributes()->name . '_' . $option_index . '_' . $suboption_index; ?>"><?= $suboption->attributes()->title; ?><?= $show_total_results_subfilter ? '<span class="sfc-total-results"> (<span class="count">' . $suboption->attributes()->totalResults . '</span>)</span>' : ''; ?></label>
										<input type="checkbox" id="<?= $id_prefix . $subfilter->attributes()->name . '_' . $option_index . '_' . $suboption_index; ?>" name="<?= $subfilter->attributes()->name; ?>" value="<?= $suboption->attributes($ns['f'])->filter; ?>" />
										<span class="checkmark"></span>
									</label>
								</div>
								<?php
							} ?>
						</div>
					<?php } ?>
					</div>
				<?php }

				if ($option_index === 0) {
					ob_end_clean();
					continue;
				}
			}

			?>
		</fieldset>
		<?php
		do_action('sircon_finn_cars_after_filter_output');
		echo ob_get_clean();
	}
?>



	<div class="search-btn-wrapper">
		<input type="submit" value="Søk" />
	</div>
</form>


