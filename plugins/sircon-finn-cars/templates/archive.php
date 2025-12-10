<?php

namespace sircon\finncars;

use sircon\Options;

$cars_page_id = intval(Options::get_option(FinnCars::OPTIONSPAGE_ID, 'cars_page_id'));
?>





<?php /* ?> 		
<div class="finn_postsfilters_head">
	<div class="finn_postsfilters_head_label">
		<h3>L&#229;nekalkulator</h3>				    
	</div>
	<div class="finn_postsfilters_head_slide_one">
		<div class="gb_rangeslider_new_container">
		<label>Kontantbel&#248;p:</label> <span id="amount_kontant">150000</span> <span>,-</span><br />
		<input id="finn_slider_one" type="range" min="0" max="10" value="150000" step="1">
		</div>
		<!--<div id="finn_slider_one"></div>-->
	</div>
	<div class="finn_postsfilters_head_slide_two">
		<div class="gb_rangeslider_new_container">
		<label>L&#248;petid:</label> <span id="amount_kontant">10</span> <span>&#229;r</span><br />
		<input id="finn_slider_two" type="range" min="0" max="10" value="10" step="1">
		</div>
	</div>
</div>
<?php */ ?> 

<div class="sfc-item-wrapper">
	<div class="sfc-items">
		<?php foreach ($result->entry as $ad) {

			$item = Finn::parseSingleArchive($ad);
			
			$finn_id = $item['id'];
			$Finn = new Finn();
			$ad = simplexml_load_string($Finn->getSingle($finn_id));
			$single_item = Finn::parseSingle($ad);
			$single_item_images = $single_item['images'];
			$single_item_images_length = 0;
			if( $single_item_images !== NULL){
				$single_item_images_length = count($single_item_images) - 1;
			}
			

			if($single_item_images_length < 0){
    			 $selected_image = $item['image'];
			}else{
			    $selected_image = $single_item_images[$single_item_images_length];
    			$selected_image = $selected_image['url'];
			}
			
// 			var_dump($selected_image);
			?>

			<div class="sfc-item">
				<a href="<?php echo untrailingslashit(get_permalink($cars_page_id)) . '/' . $item['id']?>">
					<figure style="background-image: url(<?php echo $selected_image; ?>)">
						<img alt="<?php echo htmlentities($item['title']); ?>" src="<?php echo $selected_image; ?>" />
						<?php if ($item['sold'] === 'true') { ?>
							<figcaption><?php _e('Sold', 'sircon-finn-cars'); ?></figcaption>
						<?php } ?>
					</figure>
					<h4 class="sfc-title"><?php echo $item['year']; ?> <?php echo $item['make']; ?> <?php echo $item['model']; ?></h4>
					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'summary_show_in_archive') == 'yes') { ?>
						<div class="sfc-summary">
							<?php echo $item['title']; ?>
						</div>
					<?php } ?>
					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'price_main_show_in_archive') == 'yes') { ?>
						<div class="gb_item_price_container">
							<h6 class="sfc-price" style="display: none;"><span class="sfc-price-label"><?php _e('Price', 'sircon-finn-cars'); ?>: kr </span><span class="gb_price"><?= number_format($item['price_main'], 0, ',', ' '); ?></span> ,-</h6>
							<h6 class="sfc-monthly-price" style="display: none;"><span class="sfc-price-label"><?php _e('M&#229;nedlig pris', 'sircon-finn-cars'); ?>: kr </span><span class="mnd_price"></span> ,-</h6>
						</div>
					<?php } ?>
				</a>
				<div class="sfc-info">
					<?php
						if( array_key_exists('year',$item) ){
							?>
							<div class="sfc-info-wrapper">
		
								<div class="sfc-info-label">
									<?php _e('&#197;rsmodell', 'sircon-finn-cars'); ?>
								</div>
		
								<div class="sfc-info-value">
									<?= $item['year']; ?>
								</div>
		
							</div>
							
							<?php
						}
					if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'mileage_show_in_archive') == 'yes') { ?>
						<div class="sfc-info-wrapper">

							<div class="sfc-info-label">
								<?php _e('Kilometer', 'sircon-finn-cars'); ?>
							</div>
	
							<div class="sfc-info-value">
								<?= number_format($item['mileage'], 0, ',', ' ') . ' km'; ?>
							</div>

						</div>
					<?php } ?>
					<div class="sfc-info-wrapper">

						<div class="sfc-info-label">
							<?php _e('Totalpris', 'sircon-finn-cars'); ?>
						</div>

						<div class="sfc-info-value">
							<?= number_format($item['price_main'], 0, ',', ' '); ?>
						</div>

					</div>
					
					<div class="sfc-info-wrapper">

						<div class="sfc-info-label">
							<?php _e('M&#229;nedlig', 'sircon-finn-cars'); ?>
						</div>

						<div class="sfc-info-value card_sfc_monthly_price">
							
						</div>

					</div>
					
					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'engine_effect_show_in_archive') == 'y') {
						if (isset($item['engine_effect'])) { ?>
							<div class="sfc-info-wrapper">

							<div class="sfc-info-label">
								<?php _e('Effect', 'sircon-finn-cars'); ?>
							</div>

							<div class="sfc-info-value">
								<?= number_format($item['engine_effect'], 0, ',', ' ') . ' ' . __('hp', 'sircon-finn-cars'); ?>
							</div>

							</div>
						<?php }
					} ?>

					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'engine_fuel_show_in_archive') == 'y') {
						if (isset($item['engine_fuel'])) { ?>
							<div class="sfc-info-wrapper">

							<div class="sfc-info-label">
								<?php _e('Fuel', 'sircon-finn-cars'); ?>
							</div>

							<div class="sfc-info-value">
								<?= $item['engine_fuel']; ?>
							</div>

							</div>
						<?php }
					} ?>

					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'city_show_in_archive') == 'y') { ?>
						<div class="sfc-info-wrapper">

							<div class="sfc-info-label">
								<?php _e('Location', 'sircon-finn-cars'); ?>
							</div>
	
							<div class="sfc-info-value">
								<?= $item['city']; ?>
							</div>

						</div>
					<?php } ?>

					<?php if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'dealer_show_in_archive') == 'y') { ?>
						<div class="sfc-info-wrapper">

						<div class="sfc-info-label">
							<?php _e('Dealer', 'sircon-finn-cars'); ?>
						</div>

						<div class="sfc-info-value">
							<?= $item['author']; ?>
						</div>

						</div>
					<?php } ?>
				</div>
				<div class="sfc-last-updated">
					<?php
					if (Options::get_option(FinnCars::OPTIONSPAGE_ID, 'updated_show_in_archive') == 'y') {
						echo __('Last updated', 'sircon-finn-cars') . ': ' . strftime('%d.%m.%Y kl %R', strtotime($item['updated']));
					}
					?>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clear"></div>
	<?php
	$limit = 20;
	$ns = $result->getNamespaces(true);
	$total_count = $result->children($ns['os'])->totalResults->__toString();
	$pages = ceil($total_count / $limit);
	if ($pages > 1) {
		?>
		<nav aria-label="Paginering">
			<ul class="sfc-pagination">
				<?php for ($page = 1; $page <= $pages; $page++) {
					if ($page === $current_page) { ?>
						<li class="page-item active" aria-current="page">
							<span class="page-link" data-page="<?php echo $page; ?>"><?php echo $page; ?></span>
						</li>
					<?php } else { ?>
						<li class="page-item">
							<a class="page-link sfc-pagination-link" href="#" data-page="<?php echo $page; ?>"><?php echo $page; ?></a>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		</nav>
	<?php } ?>
</div>
