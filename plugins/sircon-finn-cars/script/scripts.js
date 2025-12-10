function inject_advertise_into_items($) {
    var ad_item = `
			<div class="sfc-item ad">
			  <a href="/avdeling/">
				<div class="back-background back-image">
				  <div class="overlay"></div>
				  <div class="center-text">
					<div class="inner-container">
					  <div class="title">Finner du ikke drømmebilen? </div>
					  <div class="description">Vi skaffer deg den bilen du egentlig ønsker deg!</div>
					  <div class="button">Kontakt oss</div>
					</div>
				  </div>
				</div>
			  </a>
			</div>
	`;
    $("body.page-id-374 .sfc-item-wrapper .sfc-items > .sfc-item:nth-child(3)").after(ad_item);
}

(function ($) {
    inject_advertise_into_items($);

    $(document).on('submit', '.sfc-filter', function (event) {
        event.preventDefault();
        let form = $(this);

        $('.sfc-items').css('opacity', '0.3');

        $.ajax({
            url: sfc.ajax_url,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'sfc_filter',
                data: form.serializeArray(),
            },
            success: function (res) {
                if (res.content === "") {
                    console.log('empty');
                    $('.sfc-items').css('opacity', '1');
                    return;
                }
                $('.sfc-item-wrapper').replaceWith(res.content);
                updateResultCount(res.filter.results);

                $('.finn_postsfilters_head').each(function (i, v) {
                    if (i == 0) {
                        if ($(this).hasClass('open_once')) {

                        } else {
                            $(this).addClass('open_once');
                        }
                    } else {
                        $(this).remove();
                    }
                });

                var amount_slider = $('.finn_postsfilters_head_slide_one #amount_kontant').text();
                var year_slider = $('.finn_postsfilters_head_slide_two #amount_kontant').text();
                amount_slider = parseInt(amount_slider);
                year_slider = year_slider * 12;
                var interest_rate = $('.gb_interest_rate').val();
                interest_rate = parseFloat(interest_rate);

                $('.sfc-items .sfc-item').each(function () {
                    var product_price = $(this).find('.sfc-price .gb_price').text();
                    product_price = product_price.replace(' ', '');
                    product_price = parseFloat(product_price);
                    var loan_price = product_price - amount_slider;
                    if (loan_price > 0) {
                        var monthly_loan_price = loan_price / year_slider;
                        var monthly_loan_interest = (monthly_loan_price * interest_rate) / 100;
                        var monthly_installment = monthly_loan_interest + monthly_loan_price;
                        $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                    } else {
                        $(this).find('.sfc-monthly-price .mnd_price').text('0');
                    }
                });

                // -- Run Advertise
                inject_advertise_into_items($);
            }
        });
    });


    $('.sfc-archive .finn_postsfilters_head').each(function (i, v) {
        if (i == 0) {
            if ($(this).hasClass('open_once')) {

            } else {
                $(this).addClass('open_once');
            }
        }
    });


    $(document).on('change', '.sfc-filter input[type="checkbox"]', function () {
        if ($(this).prop('checked')) {
            $(this).parent().siblings('.sfc-subfilter').addClass('active')
        } else {
            $(this).parent().siblings('.sfc-subfilter').removeClass('active')
            $(this).parent().siblings('.sfc-subfilter').find('input[type="checkbox"]').prop('checked', false);
        }

        $('.sfc-filter').trigger('submit');
    });

    $(document).on('click', '.sfc-archive .sfc-pagination-link', function (event) {
        event.preventDefault();
        let form = $('.sfc-filter');

        $.ajax({
            url: sfc.ajax_url,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'sfc_filter',
                data: form.serializeArray(),
                page: $(this).attr('data-page'),
            },
            success: function (res) {
                $('.sfc-item-wrapper').replaceWith(res.content);
                updateResultCount(res.filter.results);
                $('html, body').animate({ scrollTop: $('.sfc-archive').offset().top - 100 });
            }
        });
    });

    $(document).on('click', '.show-filters', function (event) {
        $('.sfc-filter').slideToggle(200);
    });

    /**
     * Slideshow navigation
     */
    $(document).on('click', '.slideshow-nav .nav-btn', function (event) {
        let target = $(event.target);
        let direction = target.data('direction');
        let currentSlide = $('.sfc-slideshow .sfc-slide.current');
        let max = $('.sfc-slideshow').data('slides');
        let first = $('.sfc-slideshow').find('.sfc-slide[data-index="1"]');
        let last = $('.sfc-slideshow').find('.sfc-slide[data-index="' + max + '"]');

        $('.sfc-slideshow .sfc-slide').removeClass('current');

        switch (direction) {
            case 'prev':
                if (currentSlide.data('index') === 1) {
                    last.addClass('current');
                } else {
                    currentSlide.prev('.sfc-slide').addClass('current');
                }
                break;

            case 'next':
                if (currentSlide.data('index') === max) {
                    first.addClass('current');
                } else {
                    currentSlide.next('.sfc-slide').addClass('current');
                }
                break;
        }
    });

    function updateResultCount(filters) {
        $('.sfc-filter .sfc-total-results .count').html('0');
        let values;
        Object.keys(filters).forEach(function (key) {
            values = filters[key];
            Object.keys(values).forEach(function (i) {
                //$('.sfc-filter [name="'+key+'"][value="'+i+'"] + label .sfc-total-results .count').html(values[i]);
                $('.sfc-filter [name="' + key + '"][value="' + i + '"]').closest('label').find('.sfc-total-results .count').html(values[i]);
                //console.log('[name="'+key+'"][value="'+i+'"]');
            });
        });
    }

    $('#registrationNumber').each(function () {

    });
})(jQuery);
