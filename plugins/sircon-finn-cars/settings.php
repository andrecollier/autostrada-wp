<?php
    if( !function_exists('billink_cars_admin_menu') ){
        function billink_cars_admin_menu(){
            add_menu_page('Billink Cars', 'Billink Cars', 'manage_options', 'billink-cars', 'billink_cars_callback', 'dashicons-backup');
            //add_submenu_page( 'billink-cars', 'Set Billink Filters Switch', 'Billink Filters Switch', 'manage_options', 'gb_billink_filters_switch', 'gb_sultan_billink_filters_switch_function' );
            //add_submenu_page( 'billink-cars', 'Set Billink Links Settings', 'Billink Links Settings', 'manage_options', 'gb_billink_links_settings', 'gb_sultan_billink_links_settings_function' );
        }
        add_action( 'admin_menu', 'billink_cars_admin_menu' );
    }

    if( !function_exists('billink_cars_callback') ){
        function billink_cars_callback(){
            ?>
            <style>
                input, p.description{
                    width: 60%;
                }

                select,select option{
                    text-transform:capitalize;
                }

                .form-table td{
                    padding: 15px 0px;
                }
            </style>
            <h2 class="">Billink Set Interest Rate:</h2>
            <ul class="billink_errors">

            </ul>
            <table class="form-table" role="presentation">
                <?php
                    $interest_rate_option = get_option('gb_sultan_billink_interest_rate', true);
                    $slider_color_option = get_option('gb_sultan_billink_slider_color',true);
                    $calc_switch_option = get_option('gb_sultan_billink_calc_switch', true);
                ?>
                <tbody>
                    
                    
                    <tr >
                        <td>
                            <label style="font-size: 18px; margin-bottom: 5px;"><b>Interest Rate:</b></label>
                            <input type="number" value="<?php echo $interest_rate_option; ?>" min="0" max="100" class="billink_percentage" name="billink_percentage" id="billink_percentage" /> %
                            <p class="description">This field is for setting up the interest rate on car loan installments. "<b>This field is accept percentage number only Like 1%, 2% etc.</b>"</p>
                        </td>
                    </tr>

                    <tr>
                        <td scope="row" colspan="2" style="padding-left: 0px;">
                            <input type="button" class="button button-primary billink-percentage-btn" value="Set Percentage" style="width: 200px;"/>
                        </td>
                    </tr>
                    
                
                    
                    <tr>
                        <td>
                            <label style="font-size: 18px; margin-bottom: 5px; display: flex;"><b>Slider Color:</b></label>
                            <input type="text" value="<?php if($slider_color_option){ echo $slider_color_option; }else{ echo 'ed0526'; }; ?>" class="billink_slider_color" name="billink_slider_color" id="billink_slider_color" />
                            <p class="description">Set your slider color From here.</p>
                        </td>
                    </tr>

                    <tr>
                        <td scope="row" colspan="2" style="padding-left: 0px;">
                            <input type="button" class="button button-primary billink-slider-btn" value="Set Color" style="width: 200px;"/>
                        </td>
                    </tr>

                     <tr>
                        <td>
                            <label style="font-size: 18px; margin-bottom: 5px; display: block;"><b>Calculater Switch:</b></label>
                            <input type="checkbox" class="gb_calc_switch" id="gb_calc_switch" style="min-width: 10px; display: inline-block; vertical-align: top;" <?php if($calc_switch_option == 'yes'){ echo 'checked'; } ?>/>
                            <input type="hidden" value="<?php if($calc_switch_option){ echo $calc_switch_option; }else{ echo 'no'; }?>" class="billink_calc_switch" name="billink_calc_switch" id="billink_calc_switch" />
                            <p class="description" style="width: auto; display: inline-block; vertical-align: top; margin-top: -2px;">This Option is a switch of percentage calculater. Switch on if you want Calculater function.</p>
                        </td>
                    </tr>

                    <tr>
                        <td scope="row" colspan="2" style="padding-left: 0px;">
                            <input type="button" class="button button-primary billink-calc-btn" value="Update Calculater" style="width: 200px;"/>
                        </td>
                    </tr>

                </tbody>
            </table>

            <script type="text/javascript">
                jQuery(document).ready(function($){

                    $('.billink_slider_color').ColorPicker({
                        onSubmit: function(hsb, hex, rgb, el) {
                                $(el).val(hex);
                                $(el).ColorPickerHide();
                        },
                        onBeforeShow: function () {
                                $(this).ColorPickerSetColor(this.value);
                        }
                    })
                    .bind('keyup', function(){
                        $(this).ColorPickerSetColor(this.value);
                    });

                    $('.billink-slider-btn').click(function(){
                        //ed0526
                        var billink_slider_color = $('.billink_slider_color').val();
                        if(billink_slider_color != ''){
                            billink_slider_color = billink_slider_color;
                        }else{
                            billink_slider_color = 'ed0526';
                        }

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: "post",
                            dataType: 'json',
                            data: {'action': 'billink_slider_color_action', 'billink_slider_color': billink_slider_color },
                            success: function(response) {
                                if(response.success){
                                    $('.billink_slider_color').closest('tr').find('.slider_msg').remove();
                                    $('.billink_slider_color').before('<span style="color: #1ea94f; font-weight: 600; font-size: 15px;" class="slider_msg">The Slider Color is set to ' + response.success + '.</span><br class="slider_msg"/>');
                                }
                                //console.log(response);
                            },

                        });
                    });


                    $('.billink-percentage-btn').click(function(){
                        var percentage_value = $('.billink_percentage').val();
                        if(percentage_value >= 0 && percentage_value <= 100 && percentage_value != ''){
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: "post",
                                dataType: 'json',
                                data: {'action': 'billink_interest_rate_action', 'percentage_value': percentage_value },
                                success: function(response) {
                                    if(response.success){
                                        //console.log(response);
                                        $('.billink_errors').html('<li style="color: #1ea94f; font-weight: 600; font-size: 15px;">The Percentage is set to ' + percentage_value + '%.</li>');
                                    }
                                },

                            });
                        }else{
                            $('.billink_errors').html('<li style="color: #cb0d2a">The Number should be between 0-100</li>');
                        }
                    });

                    $('.gb_calc_switch').click(function(){
                        if( $(this).is(':checked') ){
                            $('#billink_calc_switch').val('yes');
                        }else{
                            $('#billink_calc_switch').val('no');
                        }
                    });

                    $('.billink-calc-btn').click(function(){
                        var billink_calc_switch = $('#billink_calc_switch').val();
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: "post",
                            dataType: 'json',
                            data: {'action': 'billink_calc_switch_action', 'billink_calc_switch': billink_calc_switch },
                            success: function(response) {
                                if(response.success){
                                    $('.gb_calc_switch').closest('tr').find('.slider_msg').remove();
                                    $('.gb_calc_switch').before('<span style="color: #1ea94f; font-weight: 600; font-size: 15px;" class="slider_msg">Calculater Settings is ' + response.success + ' Now.</span><br class="slider_msg"/>');
                                }
                                //console.log(response);
                            },

                        });

                    });


                   $('#slider-range_one, #slider-range_two, .ui-slider-handle').draggable();
                });


            </script>
            <?php
        }
    }

    if( !function_exists('gb_sultan_billink_slider_color_action') ){
        function gb_sultan_billink_slider_color_action(){
            $result = [];
            $billink_slider_color = $_POST['billink_slider_color'];

            update_option('gb_sultan_billink_slider_color', $billink_slider_color);

            $result['success'] = $billink_slider_color;
            echo json_encode($result);

            wp_die();
        }
        add_action( 'wp_ajax_billink_slider_color_action', 'gb_sultan_billink_slider_color_action' );
        add_action( 'wp_ajax_nopriv_billink_slider_color_action', 'gb_sultan_billink_slider_color_action' );

    }

    if( !function_exists('gb_sultan_billink_interest_rate_action') ){
        function gb_sultan_billink_interest_rate_action(){
            $result = [];
            $interest_rate = $_POST['percentage_value'];

            update_option('gb_sultan_billink_interest_rate', $interest_rate);

            $result['success'] = $interest_rate;
            echo json_encode($result);
            wp_die();
        }

        add_action( 'wp_ajax_billink_interest_rate_action', 'gb_sultan_billink_interest_rate_action' );
        add_action( 'wp_ajax_nopriv_billink_interest_rate_action', 'gb_sultan_billink_interest_rate_action' );
    }

    if( !function_exists('gb_sultan_billink_calc_switch_action') ){
        function gb_sultan_billink_calc_switch_action(){
            $result = [];
            $billink_calc_switch = $_POST['billink_calc_switch'];

            update_option('gb_sultan_billink_calc_switch', $billink_calc_switch);

            if($billink_calc_switch == 'yes'){
                $result['success'] = 'Enabled';
            }else{
                $result['success'] = 'Disabled';
            }

            echo json_encode($result);
            wp_die();
        }

        add_action( 'wp_ajax_billink_calc_switch_action', 'gb_sultan_billink_calc_switch_action' );
        add_action( 'wp_ajax_nopriv_billink_calc_switch_action', 'gb_sultan_billink_calc_switch_action' );
    }

    if( !function_exists('billink_admin_enqueue_function') ){
        function billink_admin_enqueue_function(){

            ?>
            <link rel="stylesheet" media="screen" type="text/css" href="<?php echo plugins_url() . '/sircon-finn-cars/dist'; ?>/colorpicker.css" />
            <script type="text/javascript" src="<?php echo plugins_url() . '/sircon-finn-cars/dist'; ?>/colorpicker.js"></script>

            <script type="text/javascript">
                jQuery(document).ready(function($){
                    console.log('Google');
                    $('.sircon-option-tabs legend').click(function(){
                        console.log('Google');
                        $('.sircon-option-tabs fieldset').each(function(){
                            $(this).removeClass('current-fieldset');
                        });

                        $(this).closest('fieldset').addClass('current-fieldset');

                    });
                });
            </script>
            <?php
        }
        add_action('admin_head','billink_admin_enqueue_function');
    }

    if( !function_exists('billink_checking_function') ){
        function billink_checking_function(){
            global $wp,$wpdb,$current_user;
            $slider_color_option = get_option('gb_sultan_billink_slider_color');
            $current_url = home_url('/') . $wp->request;
            $sircon_finn_cars_cars_page_id = get_option('sircon_finn_cars_cars_page_id',true);
            if(get_the_ID() == $sircon_finn_cars_cars_page_id){
                ?>
                <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <!--<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>-->
                <link rel="stylesheet" id="enzo-fonts-css" href="https://use.typekit.net/nvo8szq.css?ver=5.4.6" type="text/css" media="all">
                <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>dist/styles.min.css" />
                <!--<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>style.css" />-->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" defer/>

                <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                <script src="<?php echo plugin_dir_url(__FILE__); ?>dist/rangeslider-js.min.js"></script>
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>

                <?php
                    if(!$slider_color_option){
                        $slider_color_option  = '#cb0d2a';
                    }

                    $slider_color_option = str_ireplace('#','',$slider_color_option );
                ?>

                <style type="text/CSS">

                /*Ad in Roller Start*/
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad{
                  background: none !important;
                    padding-bottom: 0px !important;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a {
                    display: block;
                    height: 100%;
                    width: 100%;
                    font-family: ff-enzo-web;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background.back-image {
                    display: block;
                    height: 100%;
                    width: 100%;
                    position: relative;
                    display: flex;
                    align-content: center;
                    justify-items: center;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background .overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #f9f9f9;
                    opacity: .9;
                    backdrop-filter: blur(5px);
                    border-radius: 5px;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background .center-text {
                    text-align: center;
                    padding: 30px 15px;
                    max-width: 375px;
                    margin: 0 auto;
                    position: relative;
                    z-index: 9;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background .center-text .title {
                    font-size: 5rem;
                    line-height: 5.5rem;
                    color: #000;
                    font-weight: 700;
                    padding-bottom: 30px;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background .center-text .description {
                    font-size: 2.9rem;
                    line-height: normal;
                    color: #000;
                    padding-bottom: 50px;
                    max-width: 315px;
                    margin: 0 auto;
                }
                body.page-id-374 .sfc-item-wrapper .sfc-items .sfc-item.ad a .back-background .center-text .button {
                    font-size: 3rem;
					font-weight: 600;
					color: #fff;
					height: 55px;
					line-height: 55px;
					background-color: #b9051d;
					max-width: 255px;
					margin: 0 auto;

					padding: 0 25px;
                }
                /* ad in roller END*/


                .reset_filter,.sfc-archive .filter-btn-wrapper .show-filters{
                    border-radius: 130px;
                    padding: 10px;
                    width: 90%;
                    height: 60px;
                    text-align: center !important;
                    color: #fff;
                    font-weight: 500;
                    background: #<?php echo $slider_color_option; ?>;
                }

                .sfc-archive .filter-btn-wrapper .show-filters{
                    width: 100%;

                }

                @font-face {
                  font-family: GraphikBlack;
                  src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikBlack.otf");
                }

                @font-face {
                  font-family: GraphikBlackIt;
                  src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikBlackItalic.otf");
                }

                @font-face {
                  font-family: GraphikBold;
                  src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikBold.otf");
                }

                @font-face {
                  font-family: GraphikBoldIt;
                  src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikBoldItalic.otf");
                }

                @font-face {
                    font-family: GraphikExtralight;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikExtralight.otf");
                }

                @font-face {
                    font-family: GraphikExtralightIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikExtralightItalic.otf");
                }

                @font-face {
                    font-family: GraphikMedium;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikMedium.otf");
                }

                @font-face {
                    font-family: GraphikMediumIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikMediumItalic.otf");
                }

                @font-face {
                    font-family: GraphikRegular;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikRegular.otf");
                }

                @font-face {
                    font-family: GraphikRegularIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikRegularItalic.otf");
                }

                @font-face {
                    font-family: GraphikSemibold;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikSemibold.otf");
                }

                @font-face {
                    font-family: GraphikSemiboldIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikSemiboldItalic.otf");
                }

                @font-face {
                    font-family: GraphikSuper;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikSuper.otf");
                }

                @font-face {
                    font-family: GraphikSuperIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikSuperItalic.otf");
                }

                @font-face {
                    font-family: GraphikThin;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikThin.otf");
                }

                @font-face {
                    font-family: GraphikThinIt;
                    src: url("<?php echo plugin_dir_url(__FILE__); ?>fonts/GraphikThinItalic.otf");
                }

                @font-face{
                    font-family:'MyriadPro';
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Regular.eot");
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Regular.eot?#iefix") format("embedded-opentype"), url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Regular.woff") format("woff"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Regular.ttf") format("truetype"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Regular.svg#MyriadPro-Regular") format("svg");
                    font-weight:400;
                    font-style:normal
                }

                @font-face{
                    font-family:'MyriadPro-It';
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-It.eot");
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-It.eot?#iefix") format("embedded-opentype"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-It.woff") format("woff"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-It.ttf") format("truetype"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-It.svg#MyriadPro-It") format("svg");
                    font-weight:400;
                    font-style:italic
                }
                @font-face{
                    font-family:'MyriadPro';
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Bold.eot");
                    src:url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Bold.eot?#iefix") format("embedded-opentype"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Bold.woff") format("woff"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Bold.ttf") format("truetype"),url("<?php echo plugin_dir_url(__FILE__); ?>fonts/MyriadPro-Bold.svg#MyriadPro-Bold") format("svg");
                    font-weight:700;
                    font-style:normal
                }
				.sfc-fieldwrapper.prfxseo_show_flex > label[for="sfc_max_trailer_weight_to"],
				.sfc-fieldwrapper.prfxseo_show_flex > label[for="sfc_max_trailer_weight_from"] {
					width: 28px;
				}

                .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label h3{
                    border-bottom: 3px solid #<?php echo $slider_color_option; ?>;
                }

                .finn_postsfilters_sidebar{
                    width: 30%;
                    display: inline-block;
                    background: #fefcf7;
                    min-height: 100px;
                    vertical-align: top;
                    margin: 5px 5px 10px 0px!important;
                    font-family: Roboto, 'Segoe UI', Tahoma, font-family;
                    padding: 10px;
                    border-radius: 15px;
                }

                .finn_content{
                    width: 69% !important;
                    display: inline-block;
                    background: transparent;
                    padding: 10px;
                    min-height: 100px;
                    vertical-align: top;
                    font-family: Roboto, 'Segoe UI', Tahoma, font-family;
                    max-width: 1280px;
                    margin: 0 auto;
                    border-radius: 15px;
                    margin-top: 5px;
                    /*text-align: center;*/
                }

                .gb_finn_filter_slider_part_a{
                    width: 100%;
                }

                .rangeslider__handle{
                    background: #f9f8f8;
                    /*box-shadow: 0px 0px 5px #e5e5e5;*/
                }

                .gb_rangeslider_new_container{
                    width: 100%;
                }

                .finn_postsfilters_head > div.finn_postsfilters_head_slide_two{
                    width: 100%;
                }


                .rangeslider__fill, .rangeslider__fill__bg, .rangeslider__handle{
                    height: 2px;
                }

                .rangeslider__handle{
                    width: 20px;
                    height: 20px;
                    margin-top: 1px;
                    background: #fff;
                    left: 0px;
                }

                .ui-slider{
                    height: 3px;
                }

                .ui-slider-handle .ui-state-default, .ui-slider.ui-widget-content .ui-state-default{
                    cursor: pointer;
                    position: absolute;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    margin-top: -5px;
                    margin-left: -9px;
                    background: #fff;
                }

                .gb_chk_filters label input[type="checkbox"]{
                    display: inline-block;
                }

                .gb_chk_filters label i{
                    font-style: normal;
                    vertical-align: super;
                    position: relative;
                    top: -3px;
                    cursor: pointer;
                    font-size: 1.5rem;
                    line-height: 2.7rem;
                    letter-spacing: 0.7px;
                    color: #828282;
                    font-weight: 400;
                    margin-left: 2px;
                }

                .fin_search_filter_cont input[type="text"]{
                    width: 100%;
                    font-family: GraphikRegular;
                    background-color: #fff;
                    padding: 0px 20px;
                    border-radius: 20px;
                    font-size: 18px;
                    border: 1px solid #949494;
                    height: 38px;
                }

                .finn_postsfilters_sidebar #min_amount{
                    font-family: GraphikRegular;
                    color: #000;
                    font-size: 1.9rem;
                    line-height: normal;
                    letter-spacing: .02em;
                    font-weight: 400;
                    margin-bottom: 10px;
                    padding-left: 13px;
                }

                .finn_postsfilters_sidebar #max_amount{
                    width: 47%;
                    font-family: GraphikRegular;
                    color: #000;
                    font-size: 1.9rem;
                    line-height: normal;
                    letter-spacing: .02em;
                    font-weight: 400;
                    margin-bottom: 10px;
                    padding-right: 15px;
                }

                /* Hide the browser's default checkbox */
                .gb_chk_filters label input[type="checkbox"] {
                  position: absolute;
                  opacity: 0;
                  cursor: pointer;
                  height: 0;
                  width: 0;
                }

                /* Create a custom checkbox */
                .checkmark {
                    position: relative;
                    top: 0;
                    left: 0;
                    height: 20px;
                    width: 20px;
                    background-color: #fff !important;
                    border: 1px solid #949494;
                    border-bottom: 1px solid #949494 !important;
                    border-radius: 0px;
                }

                /* On mouse-over, add a grey background color */
                .gb_chk_filters label:hover input[type="checkbox"] ~ .checkmark {
                  background-color: #ccc;
                }

                /* When the checkbox is checked, add a blue background */
                .gb_chk_filters label input[type="checkbox"]:checked ~ .checkmark {
                  background-color: #<?php echo $slider_color_option; ?> !important;
                }

                /* Create the checkmark/indicator (hidden when not checked) */
                .checkmark:after {
                  content: "";
                  position: absolute;
                  display: none;
                }

                /* Show the checkmark when checked */
                .gb_chk_filters label input[type="checkbox"]:checked ~ .checkmark:after {
                  display: block;
                }

                /* Style the checkmark/indicator */
                .gb_chk_filters label .checkmark:after {
                  left: 7px;
                  top: 3px;
                  width: 5px;
                  height: 10px;
                  border: solid white;
                  border-width: 0 3px 3px 0;
                  -webkit-transform: rotate(45deg);
                  -ms-transform: rotate(45deg);
                  transform: rotate(45deg);
                }

                .finn_postsfilters_sidebar_btn{
                    display: none;
                    width: 100%;
                    margin: 0 auto 10px auto;
                    border: 2px solid #489071;
                    color: #fff;
                    background: transparent;
                    padding: 15px;
                    text-align: center !important;
                    cursor: pointer;
                    border-radius: 5px;
                    background-image: linear-gradient(#59ab88, #3f8063);
                    font-size: 17px;
                    font-family: GraphikRegular !important;
                }

                .finn_postsfilters_sidebar_cl{
                    display: inline-block;
                    background: #fefcf7;
                }

                .gb_finn_filter_slider_part_b{
                     width: 100%;
                }

                .finn_content .finn_posts_container .finn_post_container{
                    width: 43%;
                    background: #fefcf7;
                    margin: 0px 15px 15px 15px;
                    border-radius: 5px;
                    height: 500px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_image{
                    height: 220px;
                    margin-bottom: 15px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_image > .finn_post_image_src{
                    display: block;
                    width: 100%;
                    height: 100%;
                    background-size: cover !important;
                    background-position: center !important;
                    border-radius: 5px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_image > img{
                    height: 280px !important;
                    max-height: 250px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption{
                    padding: 0px 10px 25px 20px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_info h4{
                    width: 41%;
                    min-width: unset;
                    font-family: GraphikRegular;
                    font-size: 15px;
                    font-weight: 600;
                    letter-spacing: 0px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_info h4.gb_girkasse_c, .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_info h4.gb_driveoff_c{
                    margin-top: 5px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices{
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices .gb_price_label{
                    font-size: 13px;
                    letter-spacing: 0.5px;
                    color: #000;
                    font-family: GraphikRegular;
                    font-weight: 100;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices .gb_price, .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices .mnd_price{
                    font-weight: 700;
                    font-size: 14px;
                    color: #000;
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices .mnd_price_after{
                    font-size: 13px;
                    letter-spacing: 0.5px;
                    color: #000;
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_info{
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons{
                    display: block;
                    height: 50px;
                    width: 100%;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_a{
                    display: inline-block;
                    width: 45%;
                    height: 100%;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_a a{
                    display: inline-block;
                    background: #<?php echo $slider_color_option; ?>;
                    color: #fff;
                    letter-spacing: 0.5px;
                    padding: 6px 12px;
                    border-radius: 15px;
                    border: 1px solid #<?php echo $slider_color_option; ?>;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.2s linear 0s;
                    width: 90%;
                    text-align: center;
                    float: left;
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_a a:hover{

                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_b{
                    display: inline-block;
                    width: 45%;
                    height: 100%;
                    margin-top: 12px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_b a{
                    display: inline-block;
                    background: transparent;
                    color: #<?php echo $slider_color_option; ?>;
                    letter-spacing: 0.5px;
                    padding: 6px 12px;
                    border-radius: 15px;
                    border: 1px solid #<?php echo $slider_color_option; ?>;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.2s linear 0s;
                    width: 90%;
                    text-align: center;
                    float: right;
                    font-family: GraphikRegular;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_b a:hover{

                }

                .gb_single_page{
                    padding-left: 0px !important;
                    padding-bottom: 50px !important;
                }

                .gb_single_page .finn_postsfilters_sidebar{
                    width: 100%;
                    background: #fff;
                    padding-left: 0px;
                }

                .gb_single_page .finn_postsfilters_sidebar .title{
                    margin: 0rem auto 0rem;
                    padding-left: 4px;
                    position: relative;
                }

                .gb_single_page .finn_postsfilters_sidebar .title h2{
                    margin: 0rem auto 0rem;
                    font-family: GraphikRegular;
                    font-size: 45px;
                    letter-spacing: 0.5px;
                    font-weight: 700;
                    padding-left: 0px;
                    line-height: 1.2;
                    margin-bottom: 12px;
                }

                .gb_single_page .finn_postsfilters_sidebar .gb_sub_details{
                    font-family: 'GraphikRegular';
                    display: block;
                    font-size: 20px;
                    color: #424242;
                    font-weight: 500;
                    line-height: 1.4;
                    letter-spacing: 0.8px;
                    width: 70%;
                    margin-bottom: 15px;
                    padding-left: 6px;
                }

                .gb_single_page .bg_model, .gb_single_page .bg_km{
                    font-size: 14px;
                    color: #636161;
                    font-weight: 600;
                    line-height: 1.4;
                    letter-spacing: 0.3px;
                    margin-left: 5px;
                }

                .gb_single_page .slideshow-container{
                    max-width: unset;
                    width: 100%;
                    position: relative;
                    border: 0px solid #fff;
                    border-radius: 5px;
                    overflow: hidden;
                    display: inline-block;
                    vertical-align: top;

                }

                .gb_single_page .slideshow-container a.prev, .gb_single_page .slideshow-container a.next{
                    top: 40%;
                    opacity: 1;
                }

                .gb_single_page .slideshow-container:hover a.prev,.gb_single_page .slideshow-container:hover a.next{
                    opacity: 1;
                }

                .gb_single_page .calculator_information{
                    width: 72%;
                    position: relative;
                    display: inline-block;
                    vertical-align: top;
                    margin: 20px 0px 10px 0px !important;
                    background-color: #fefcf7;
                    padding: 20px 28px;
                    border-radius: 24px;

                }

                .gb_single_page .gb_contact_info_four{
                    display: inline-block;
                    width: auto;
                    padding: 20px 28px 40px 28px;
                    background-image: linear-gradient(#59ab88, #3f8063);
                    margin: 50px 0px 10px 0px !important;
                    border-radius: 24px;
                    float: right;
                    color: #fff;
                    font-family: GraphikRegular;
                }

                .gb_single_page .gb_contact_info_four .title h3{
                    color: #fff;
                }

                .gb_single_page .gb_each_entry{
                    border-bottom: 1px dotted #c1c1c1;
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                    height: 50px;
                }

                .gb_single_page .gb_each_entry .gb_each_entry_tag{
                    color: #9b9593;
                    font-size: 15px;
                    font-weight: 400;
                    line-height: 1;
                    letter-spacing: 0px;
                    margin-left: 5px;
                    flex: 0 40%;
                    display: flex;
                    align-items: center;
                    height: 100%;
                    margin-bottom: 0px;
                }
                .gb_single_page .gb_each_entry .gb_each_entry_value{
                    color: #000;
                    height: 100%;
                    flex: 0 50%;
                    display: flex;
                    align-items: center;
                    height: 100%;
                    margin-bottom: 0px;
                }

                .gb_single_page .gb_each_entry .gb_each_entry_value span{
                    font-size: unset;
                    font-weight: unset;
                    letter-spacing: 0px;
                }

                .gb_single_page .gb_each_entry .gb_each_entry_value.gb_single_price{
                    font-size: 28px;
                    font-weight: 700;
                    letter-spacing: -0.3px !important;
                }

                .gb_single_page .finn_postsfilters_head > div{
                    width: 50%;
                    text-align: center;
                }

                .gb_single_page .finn_postsfilters_head{
                    margin: 40px 0px 10px 0px !important;
                    background-color: #fefcf7;
                    padding: 20px;
                    border-radius: 24px;
                }

                .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_slide_one .gb_rangeslider_new_container{
                    margin: 0 0;
                    width: 100%;
                }

                .gb_single_page .finn_postsfilters_head_slide_two .gb_rangeslider_new_container{
                    margin: 0 0;
                }

                .gb_single_page .gb_single_entry{
                    padding: 0px 0px;
                    font-size: 18px;
                    margin: 5px 0px;
                    letter-spacing: 1.5px;
                }

                .gb_single_page .finn_posts_container{
                    padding: 12px;
                }

                .gb_single_page .single_container .description_section{
                    display: grid;
                    grid-template-areas:
                        'ciac ciac ciac ciac et et'
                        'd d d d et et';
                    grid-gap: 10px;
                }

                .gb_single_page .single_container .description_section .classified_info_and_contact{
                    grid-area: ciac;
                }

                .gb_single_page .single_container .description_section .equipment_term{
                    grid-area: et;
                }

                .gb_single_page .single_container .description_section .description{
                    grid-area: d;
                    width: auto;
                    font-size: 14px;
                }

                .gb_single_page .single_container .description_section .description .gb_description_container{
                    padding: 0px 15px;
                }

                .gb_single_page .single_container .description_section .classified_info_and_contact.gb_second_layout_new_changes{
                    /*width: 60%;*/
                }

                .gb_single_page .single_container .description_section .classified_info_and_contact .gb_each_entry{
                    width: 55%;
                }

                .gb_single_page .single_container .description_section .equipment_term{
                    display: inline-block;
                    margin-left: 10px;
                    /*width: 36%;*/
                }

                .gb_single_page .single_container .description_section .equipment_term ul{
                    display: inline-block;
                    height: 100%;
                    padding-left: 0px;
                }

                .gb_single_page .single_container .equipment_term ul li{
                    width: 45%;
                    display: inline-block;
                    font-size: 14px;
                    letter-spacing: 0.3px;
                    padding-left: 15px;
                    color: #9b9593;
                }

                .gb_single_page .single_container .equipment_term ul li:nth-child(even){

                }

                .gb_single_page .bg_back{
                    position: absolute;
                    right: 35px;
                    background-image: linear-gradient(#ececec, white);
                    padding: 10px 15px;
                    border: 1px solid #e5e5e5;
                    border-radius: 5px;
                    top: 0px
                }

                .finn_postsfilters_sidebar .fin_search_filter_cont{
                    width: 100%;
                }

                .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label h3{
                   font-family: GraphikRegular;
                   letter-spacing: 1.5px;
                   padding-bottom: 9px;
                }

                .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label{
                    width: 30%;
                    top: -7px;
                    text-align: left;
                }

                .gb_single_page .finn_postsfilters_head > div{
                    width: 30%;
                }

                .gb_single_page .rangeslider__fill, .rangeslider__fill__bg{
                    height: 2px;
                }

                .gb_single_page .gb_each_entries_container{

                }

                .gb_single_page .gb_each_entries_container ul.gb_each_entries_list{
                    list-style: none;
                    padding-left: 6px;
                }

                .gb_single_page .gb_each_entries_container ul.gb_each_entries_list li.gb_each_entry_li{
                    width: 30%;
                    display: inline-block;
                }

                .gb_single_page .gb_each_entries_container ul.gb_each_entries_list li.gb_each_entry_li span.gb_each_entry_li_tag{
                    font-family: 'GraphikRegular';
                    display: inline-block;
                    font-size: 16px;
                    color: #424242;
                    font-weight: 500;
                    line-height: 1.4;
                    letter-spacing: 0px;
                    width: auto;
                    margin-bottom: 15px;
                    vertical-align: top;
                }

                .gb_single_page .gb_each_entries_container ul.gb_each_entries_list li.gb_each_entry_li .gb_each_entry_li_value{
                    width: 50%;
                    display: inline-block;
                    vertical-align: top;
                    font-size: 16px;
                    line-height: 1.4;
                    font-weight: 600;
                    color: #000;
                    font-family: 'GraphikRegular';
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data{
                    margin-top: 30px;
                    padding-left: 6px;
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container{
                    width: 47%;
                    display: inline-block;
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container .gb_each_entry_li_tag{
                    font-family: 'GraphikRegular';
                    display: inline-block;
                    font-size: 20px;
                    color: #424242;
                    font-weight: 500;
                    line-height: 1.4;
                    letter-spacing: 0px;
                    width: auto;
                    margin-bottom: 15px;
                    vertical-align: top;
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container .gb_each_entry_li_value{
                    font-family: 'GraphikRegular';
                    width: 50%;
                    display: inline-block;
                    vertical-align: top;
                    font-size: 17px;
                    line-height: 1.1;
                    font-weight: 600;
                    color: #000;
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container .gb_each_entry_li_value.tp{
                    color: #51ca96;
                }

                .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container .gb_each_entry_li_value span{
                    font-weight: inherit;
                    letter-spacing: 2px;
                }

                .gb_single_page .title_with_trigger{
                    font-family: GraphikRegular;
                }

                .gb_single_page .title_with_trigger h2{
                    cursor: pointer;
                }

                .gb_single_page .title_with_trigger h2 i.fa{
                    float: right;
                    margin-right: 40px;
                    font-size: 30px;
                    margin-top: 10px;
                    transform: rotate(180deg);
                }

                .gb_single_page .title_with_trigger h3{
                    font-size: 35px;
                    margin-bottom: 0px;
                    cursor: pointer;
                }

                .gb_single_page .title_with_trigger h3 i.fa{
                    float: right;
                    margin-right: 40px;
                    font-size: 30px;
                    margin-top: 10px;
                    transform: rotate(0deg);
                    transition: all 0.2s linear 0s;
                }

                .gb_single_page .title_with_trigger .gb_trigger_now{
                    display: none;
                }

                .gb_single_page .title_with_trigger ul{
                    display: flex;
                    flex-wrap: wrap;
                    list-style: none;
                    padding-left: 6px;
                }

                .gb_single_page .title_with_trigger ul li{
                    flex-grow: 1;
                    width: 33%;
                    padding-bottom: 10px;
                    border-bottom: 1px dotted #e5e5e5;
                    line-height: 1.3;
                    font-size: 16px;
                    margin-bottom: 10px;
                }

                .finn_postsfilters_range_slider_one{
                    width: 80%;
                    margin: 0;
                    margin-left: 10px;
                }

                .finn_postsfilters_range_slider_one .yp_finn_postsfilters_range_slider_container #min_amount{
                    width: 48%;
                    display: inline-block;
                    position: relative;
                    bottom: 7px;
                }

                .finn_postsfilters_range_slider_one .yp_finn_postsfilters_range_slider_container #max_amount{
                    width: 48%;
                    display: inline-block;
                    text-align: right;
                    position: relative;
                    bottom: 7px;
                }

                .sfc-filter-group .sfc-filter-group-title{
                    margin: 15px 0px;
                    display: block;
                }

                .sfc-items .sfc-item{
                    border-radius: 0px !important;
                    padding-bottom: 30px !important;
                }

                .sfc-items .sfc-item .sfc-price{
                    font-family: GraphikRegular;
                    font-weight: 500;
                    font-size: 23px;
                }

                .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-label{
                    font-size: 14px !important;
                    line-height: 1.2;
                }

                .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-value{
                    padding: 0px;
                    font-size: 17px;
                    font-weight: 600;
                    opacity: 0.65;
                }

                @media only screen and (min-width: 1142px){
                    .gb_finn_filter_slider_part_b{
                        width: 100%;
                    }

                    .finn_postsfilters_sidebar .finn_postsfilters_range_slider_one label, .finn_postsfilters_sidebar .finn_postsfilters_range_slider_two label{
                        width: 100%;
                    }

                }

                @media only screen and (min-width: 1051px){
                    .finn_postsfilters_head > div{
                        width: 100%;
                    }

                    .gb_finn_filter_slider_part_a .yp_finn_postsfilters_range_slider_container{
                        width: 100%;
                    }

                    .finn_postsfilters_sidebar .ui-slider{
                        width: 88%;
                        margin: 0 auto;
                        position: relative;
                        left: 12px;
                    }

                }

                @media only screen and (min-width: 767px){
                    .gb_other_filters{
                        grid-template-areas: unset;
                        grid-gap: unset;
                        display: block;
                    }
                }

                @media only screen and (max-width: 1142px){
                    .gb_single_page{
                        padding-left: 20px !important;
                    }
                }

                @media only screen and (max-width: 1050px){
                    .finn_postsfilters_head > div{
                        width: 100%;
                    }

                    .finn_postsfilters_sidebar{
                        width: 100%;
                    }

                    .finn_content{
                        width: 100% !important;
                    }

                    .finn_postsfilters_sidebar .finn_postsfilters_range_slider_one label, .finn_postsfilters_sidebar .finn_postsfilters_range_slider_two label{
                        width: 100%;
                    }

                    .gb_finn_filter_slider_part_a .yp_finn_postsfilters_range_slider_container{
                        width: 95%;
                        position: relative;
                        left: 13px;
                    }

                    .finn_postsfilters_sidebar_cl{
                        display: none;
                    }

                    .finn_postsfilters_sidebar_btn{
                        display: block;
                    }

                    .finn_postsfilters_sidebar #max_amount{
                        width: 50%;
                        float: right;
                        padding-right: 0px;
                    }

                    .gb_single_page .slideshow-container{
                        width: 100%;
                        padding-left: 10px;
                    }

                    .gb_single_page .calculator_information{
                        width: 100%;
                        margin-top: 20px;
                        /*padding-left: 0px;*/
                    }

                    .gb_single_page .single_container .description_section{
                        display: grid;
                        grid-template-areas:
                            'ciac ciac ciac et et et'
                            'd d d et et et';
                        grid-gap: 10px;
                    }

                    .gb_single_page .single_container .description_section .classified_info_and_contact .gb_each_entry{
                        width: 100%;
                    }

                    .gb_single_page .gb_contact_info_four{
                        width: 100%;
                        margin: 25px 0px 10px 0px !important;
                    }
                }

                @media only screen and (max-width: 1024px){
                    .gb_finn_filter_slider_part_a .yp_finn_postsfilters_range_slider_container{
                        margin-left: 0px;
                    }
                }

                @media only screen and (max-width: 1005px){
                    .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                        width: 100%;
                    }
                }

                @media only screen and (max-width: 940px){
                    .finn_postsfilters_head{
                        width: 100%;
                        height: auto;
                        overflow: hidden;
                        min-height: unset;
                    }

                    .gb_single_page .finn_postsfilters_head > div{
                        width: 100%;
                        padding: 20px 10px;
                        text-align: left;
                    }
                }

                @media only screen and (max-width: 840px){
                    .gb_single_page .single_container .description_section{
                        display: grid;
                        grid-template-areas:
                            'ciac ciac ciac ciac ciac ciac'
                            'et et et et et et'
                            'd d d d d d';
                        grid-gap: 10px;
                    }

                    .gb_single_page .single_container .description_section .equipment_term{
                        margin-left: 0px;
                    }

                    .gb_single_page .title_with_trigger ul li{
                        width: 50%;
                    }
                }

                @media only screen and (max-width: 800px){
                    .gb_single_page .finn_postsfilters_sidebar .title h2{
                        font-size: 35px;
                    }

                    .gb_single_page .finn_postsfilters_sidebar .title .gb_sub_details{
                        width: 100%;
                        font-size: 16px;
                    }

                    .gb_single_page .gb_each_entries_container ul.gb_each_entries_list li.gb_each_entry_li{
                        width: 47%;
                    }

                }

                @media only screen and (min-width: 620px) and (max-width: 766px){
                    .finn_content .finn_posts_container .finn_post_container{
                        width: 100%;
                        height: 680px;
                        margin: 15px 15px 15px 0px;
                    }

                    .finn_content .finn_posts_container .finn_post_container .finn_post_image{
                        height: 420px;
                    }
                }

                @media only screen and (max-width: 706px){
                    .finn_postsfilters_sidebar #max_amount{
                        float: unset;
                    }

                    .gb_single_page .finn_postsfilters_sidebar span, .gb_single_page .finn_postsfilters_sidebar .finn_postsfilters_range_slider_one label, .gb_single_page .finn_postsfilters_sidebar .finn_postsfilters_range_slider_one span, .gb_single_page .finn_postsfilters_sidebar .finn_postsfilters_range_slider_two label, .gb_single_page .finn_postsfilters_sidebar .finn_postsfilters_range_slider_two span{
                        font-size: 23px;
                    }
                }

                @media only screen and (max-width: 624px){

                    .gb_single_page .gb_each_entries_container ul.gb_each_entries_list li.gb_each_entry_li{
                        width: 100%;
                    }

                    .gb_single_page .gb_each_entries_container .gb_entries_prices_data .gb_entry_price_container{
                        width: 100%;
                    }

                    .gb_single_page .title_with_trigger ul li{
                        width: 100%;
                    }

                }

                @media only screen and (max-width: 619px){
                    .finn_content .finn_posts_container .finn_post_container{
                        width: 100%;
                        margin: 15px 15px 15px 0px;
                    }

                    .gb_finn_filter_slider_part_a .yp_finn_postsfilters_range_slider_container{
                        left: 0px;
                    }
                }

                @media only screen and (max-width: 500px){
                    .gb_single_page .finn_postsfilters_sidebar .title h2{
                        font-size: 29px;
                    }
                }

                @media only screen and (max-width: 340px){
                    .finn_content .finn_posts_container .finn_post_container .finn_post_caption{
                        padding: 0px 5px 25px 10px;
                    }

                    .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_a a{
                        width: 100%;
                    }

                    .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_redirect_buttons .gb_redirect_button_b a{
                        width: 100%;
                    }
                }

                .main_title{
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                }

                .finn_postsfilters_head .finn_postsfilters_head_label h3{
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                    font-size: 2.2rem;
                    line-height: normal;
                    /* color: #f1f1f1; */
                    letter-spacing: .02em;
                    border: 0;
                    font-weight: 600;
                    margin-bottom: 8px;
                    font-family: GraphikRegular;
                }

                .ui-slider-handle .ui-state-default, .ui-slider.ui-widget-content .ui-state-default{
                    border: 3px solid #<?php echo $slider_color_option; ?> !important;
                    background: #<?php echo $slider_color_option; ?> !important;
                }

                .ui-slider-horizontal .ui-slider-range-min{
                    border: 4px solid #<?php echo $slider_color_option; ?> !important;
                }

                .ui-slider-range{
                    background-color: #<?php echo $slider_color_option; ?> !important;
                }

                .rangeslider__handle{
                        border: solid 3px #<?php echo $slider_color_option; ?>;
                        background: #<?php echo $slider_color_option; ?> !important;
                }

                .rangeslider__fill{
                        background: #<?php echo $slider_color_option; ?>;
                }

                .finn_postsfilters_sidebar .finn_postsfilters_range_slider_one label span, .finn_postsfilters_sidebar .finn_postsfilters_range_slider_two label span{
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                    font-family: GraphikRegular;
                    font-size: 2.2rem;
                    line-height: normal;
                    color: #000;
                    letter-spacing: .02em;
                    border: 0;
                    font-weight: 400;
                    margin-bottom: 8px;
                }

                .gb_chk_filters span:not(.checkmark){
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                    font-family: GraphikRegular;
                    font-size: 2.2rem;
                    line-height: normal;
                    /* color: #f1f1f1; */
                    letter-spacing: .02em;
                    border: 0;
                    font-weight: 600;
                    margin-bottom: 8px;
                }

                input[type="checkbox"]{
                        border: 2px solid #<?php echo $slider_color_option; ?>;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption h3{
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                    display: block;
                    margin-left: -2px;
                    font-family: GraphikRegular;
                    letter-spacing: 0.5px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_sub_details{
                        font-family: GraphikRegular;
                        display: block;
                        font-size: 15px;
                        color: #000;
                        font-weight: 500;
                        line-height: 1.4;
                        letter-spacing: 0.3px;
                }

                .finn_content .finn_posts_container .finn_post_container .finn_post_caption .gb_prices b span{
                    color: #<?php echo $slider_color_option; ?>;
                }

                .gb_single_page .title:not(.top_t) h2{
                    border-bottom: 0px solid #<?php echo $slider_color_option; ?>;
                    font-weight: 700;
                    letter-spacing: 0px;
                    text-align: left;
                    color: #000;
                    background-image: linear-gradient(#ececec, white);
                    width: 100%;
                    font-family: GraphikRegular;
                    font-size: 1.9rem;
                    padding: 10px 15px;
                    height: 50px;
                    display: flex;
                    align-items: center;
                    margin: 0rem auto 0rem;
                    border-radius: 5px;
                }

                .gb_single_page .title:not(.top_t){
                    padding-left: 0px;
                    margin-top: 15px;
                }

                .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                    text-align: left;
                }

                .finn_postsfilters_head .finn_postsfilters_head_slide_one label, .finn_postsfilters_head .finn_postsfilters_head_slide_one span, .finn_postsfilters_head .finn_postsfilters_head_slide_two label, .finn_postsfilters_head .finn_postsfilters_head_slide_two span{
                    font-size: 2.2rem;
                    line-height: normal;
                    letter-spacing: .02em;
                    font-weight: 400;
                    font-family: GraphikRegular;
                    color: #000;
                }

                .main_title_container{
                    display: none;
                }

                #main .fusion-row, #slidingbar-area .fusion-row, .fusion-footer-widget-area .fusion-row, .fusion-header-wrapper .fusion-row, .fusion-page-title-row, .layout-boxed-mode.side-header #boxed-wrapper, .layout-boxed-mode.side-header #slidingbar-area .fusion-row, .layout-boxed-mode.side-header .fusion-footer-parallax, .layout-boxed-mode.side-header>#lang_sel_footer, .tfs-slider .slide-content-container .slide-content{
                    max-width: 1250px;
                }

                .container{
                    display: block;
                    position: relative;
                    padding-left: 35px;
                    margin-bottom: 12px;
                    cursor: pointer;
                    /*font-size: 22px;*/
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                  }

                  /* Hide the browser's default checkbox */
                  .container input{
                    position: absolute;
                    opacity: 0;
                    cursor: pointer;
                    height: 0;
                    width: 0;
                  }

                  /* Create a custom checkbox */
                  .checkmark {
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 25px;
                    width: 25px;
                    background-color: #eee;
                  }

                  /* On mouse-over, add a grey background color */
                  .container:hover input ~ .checkmark {
                    background-color: #ccc;
                  }

                  /* When the checkbox is checked, add a blue background */
                  .container input:checked ~ .checkmark {
                    background-color: #<?php echo $slider_color_option; ?> !important;
                    border-color: #<?php echo $slider_color_option; ?> !important;
                  }

                  /* Create the checkmark/indicator (hidden when not checked) */
                  .checkmark:after {
                    content: "";
                    position: absolute;
                    display: none;
                  }

                  /* Show the checkmark when checked */
                  .container input:checked ~ .checkmark:after {
                    display: block;
                  }

                  /* Style the checkmark/indicator */
                  .container .checkmark:after {
                    left: 8px;
                    top: 4px;
                    width: 7px;
                    height: 13px;
                    border: solid white;
                    border-width: 0 2.5px 2.5px 0;
                    -webkit-transform: rotate(45deg);
                    -ms-transform: rotate(45deg);
                    transform: rotate(45deg);
                  }

                    .finn_postsfilters_head{
                        width: 100%;
                        min-height: 100px;
                        background: tranparent;
                        margin: 0px 0px 10px 0px !important;
                        max-width: unset !important;
                        width: calc(75% - 20px);
                        float: right;
                    }

                    .finn_postsfilters_head > div{
                        width: 30%;
                        display: inline-block;
                        padding: 10px;
                        vertical-align: top;

                    }

                    .finn_postsfilters_head > div.finn_postsfilters_head_slide_two{
                        width: 34%;
                    }

                    .finn_postsfilters_head .finn_postsfilters_head_label h3{
                        margin: 15px 0 10px 0;
                        font-weight: 700;
                        font-size: 30px;
                        letter-spacing: 0.7px;
                        display: inline-block;
                        padding-bottom: 5px;
                        text-align: left;
                    }
                    .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                        font-size: 15px;
                        text-align: center;
                    }

                    .finn_postsfilters_head .finn_postsfilters_head_slide_one label, .finn_postsfilters_head .finn_postsfilters_head_slide_one span, .finn_postsfilters_head .finn_postsfilters_head_slide_two label, .finn_postsfilters_head .finn_postsfilters_head_slide_two span{
                        font-size: 21px;
                        font-weight: 400;
                        display: inline-block;
                        margin-bottom: 6px;
                    }

                    .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label .single_monthly_price{
                        font-size: 18px;
                        letter-spacing: 0.5px;
                    }

                    .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label .single_monthly_price span{
                        font-size: 18px;
                        font-weight: 400;
                        letter-spacing: 0.7px;
                        display: inline-block;
                        margin-bottom: 6px;
                    }

                    .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label h3{
                        font-size: 27px;
                    }

                    .sfc-archive div.finn_postsfilters_head{
                        display:none;
                        padding: 40px 20px;
                        background: #f9f9f9;
                        border-radius: 4px;
                        text-align: center;
                    }

                    .open_once{
                        display:block !important;
                    }

                    .sfc-items .sfc-item .sfc-title{
                        font-family: GraphikRegular;
                        font-weight: 600;
                        font-size: 23px;
                        margin: 23px 0px 0px 0px;
                    }

                    .gb_item_price_container{

                    }

                    .sfc-items .sfc-item .sfc-monthly-price{

                    }

                    .sfc-items .sfc-item .sfc-monthly-price{
                        font-family: GraphikRegular;
                        font-weight: 500;
                        font-size: 23px;
                        padding: 0px 30px;
                    }

                    
                    
                    .sfc-items .sfc-item figure{
                        padding-bottom: 55% !important;
                        background-position: center center;
                        background-repeat: no-repeat;
                        background-size: cover;
                    }

                    .sfc-items .sfc-item figure img{
                        display: none;
                    }

                    @media only screen and (min-width: 800px){
                        .finn_postsfilters_head{
                            width: calc(65% - 20px);
                        }
                    }

                    @media only screen and (min-width: 1142px){
                        .finn_postsfilters_head > div{
                            width: 34%;
                        }

                        .finn_postsfilters_head > div.finn_postsfilters_head_label{
                            width: 25%;
                            text-align: left;
                        }

                        .gb_single_page .finn_postsfilters_head > div{
                            width: 33%;
                        }

                        .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_slide_one .gb_rangeslider_new_container{
                            width: 80%;
                        }

                        .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label{
                            position: relative;
                            top: -20px;
                        }

                        .finn_postsfilters_head_slide_two .gb_rangeslider_new_container{
                            margin: 0px auto;
                        }
                    }

                    @media only screen and (min-width: 1200px){
                        .finn_postsfilters_head{
                            width: calc(75% - 20px);
                        }
                    }

                    @media only screen and (max-width: 1300px) and (min-width: 799px){
                        .sfc-item .sfc-info,.sfc-item .sfc-title,.sfc-item .sfc-price,.sfc-item .sfc-summary,.sfc-items .sfc-item .sfc-monthly-price{
                            padding: 0 15px !important;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-label{
                            font-size: 13px !important;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-value{
                            font-size: 14px
                        }

                        .sfc-items .sfc-item .sfc-summary{
                            font-size: 15px;
                        }

                        .sfc-items .sfc-item .sfc-price,.sfc-items .sfc-item .sfc-monthly-price{
                            font-size: 18px;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper{
                            width: 47%;
                        }

                        .finn_postsfilters_head .finn_postsfilters_head_slide_one label, .finn_postsfilters_head .finn_postsfilters_head_slide_one span, .finn_postsfilters_head .finn_postsfilters_head_slide_two label, .finn_postsfilters_head .finn_postsfilters_head_slide_two span{
                            font-size: 18px;
                        }

                    }

                    @media only screen and (max-width: 1200px) and (min-width: 799px){
                        .finn_postsfilters_head .finn_postsfilters_head_label h3{
                            font-size: 20px;
                        }
                    }

                    @media only screen and (max-width: 1000px) and (min-width: 799px){
                        .sfc-items .sfc-item{
                            width: calc((200% / 2) - 30px) !important;
                        }
                    }

                    @media only screen and (max-width: 1300px){
                        .finn_postsfilters_head .finn_postsfilters_head_label h3{
                            font-size: 24px;
                        }
                    }

                    @media only screen and (max-width: 1200px){
                        .sfc-items{
                            column-gap: 0px !important;
                            padding: 10px 0px !important;
                        }

                        .sfc-items .sfc-item{
                            margin: 0 10px 20px;
                        }
                    }

                    @media only screen and (max-width: 1005px){
                        .finn_postsfilters_head .finn_postsfilters_head_label{
                            width: 100%;
                        }

                        .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                            width: 50%;
                        }

                        .finn_postsfilters_head > div.finn_postsfilters_head_slide_two{
                            width: 50%;
                        }

                    }

                    @media only screen and (max-width: 799px){
                        .reset_filter{
                            margin: 0 4%;
                        }

                        .finn_postsfilters_head{
                            width: 100%;
                        }

                        .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                            width: 90%;
                        }

                        .finn_postsfilters_head > div.finn_postsfilters_head_slide_two{
                            width: 90%;
                        }

                        .sfc-items{
                            margin: 0px 0px 0px 0px;
                            width: 100%;
                        }


                    }

                    @media only screen and (max-width: 767px){
                        .finn_postsfilters_head .finn_postsfilters_head_slide_one, .finn_postsfilters_head .finn_postsfilters_head_slide_two{
                            width: 100%;
                        }

                        .finn_postsfilters_head > div.finn_postsfilters_head_slide_two{
                            width: 100%;
                        }

                        .sfc-item .sfc-info,.sfc-item .sfc-title,.sfc-item .sfc-price,.sfc-item .sfc-summary,.sfc-items .sfc-item .sfc-monthly-price{
                            padding: 0 15px !important;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-label{
                            font-size: 13px !important;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-value{
                            font-size: 14px
                        }

                        .sfc-items .sfc-item .sfc-summary{
                            font-size: 15px;
                        }

                        .sfc-items .sfc-item .sfc-price,.sfc-items .sfc-item .sfc-monthly-price{
                            font-size: 18px;
                        }

                        body.page .sfc-single{
                            margin-top: 90px !important;
                        }

                    }

                    @media only screen and (max-width: 706px){
                        .finn_postsfilters_head .finn_postsfilters_head_label h3{
                            font-size: 23px;
                            letter-spacing: 0.3px;
                        }

                        .finn_postsfilters_head .finn_postsfilters_head_slide_one label, .finn_postsfilters_head .finn_postsfilters_head_slide_one span, .finn_postsfilters_head .finn_postsfilters_head_slide_two label, .finn_postsfilters_head .finn_postsfilters_head_slide_two span{
                            font-size: 18px;
                        }

                    }

                    @media only screen and (max-width: 560px){
                        .gb_single_page .finn_postsfilters_head .finn_postsfilters_head_label h3{
                            font-size: 23px;
                        }

                        .sfc-item .sfc-info .sfc-info-wrapper{
                            width: 48%;
                        }

                        .sfc-items .sfc-item .sfc-info .sfc-info-wrapper .sfc-info-value{
                            font-size: 12px;
                        }

                        .sfc-items .sfc-item .sfc-summary{
                            font-size: 13px;
                        }
                    }

                    @media only screen and (max-width: 640px) {
                        .finn_postsfilters_head{
                            padding-bottom: 30px;
                        }
                        .finn_postsfilters_head > div{
                            width: 100%;
                            padding: 0px 0px;
                            margin: 10px 0px;
                        }

                        .finn_postsfilters_head .ui-slider{
                            width: 100%;
                        }

                        .sfc-items .sfc-item{
                            width: calc(200% / 2) !important;
                        }
                    }

                    @media only screen and (max-width: 478px) {
                        body.page .sfc-single{
                            margin-top: 70px !important;
                        }
                    }

                    @media only screen and (max-width: 400px) {
                        .sfc-single .related_informations .related_information_item{
                            width: 100%;
                        }

                        .sfc-single .related_informations .related_information_item .related_information_item_icon{
                            width: auto;
                            min-width: 15%;
                        }
                    }


                </style>
                <input type="hidden" class="gb_interest_rate" value="<?php echo get_option('gb_sultan_billink_interest_rate', true); ?>"/>

                <?php

                    $post_id = get_the_ID();
                    $post = get_post($post_id);
                    $slug = $post->post_name;

                    $split_url = explode('/',$current_url);
                    $last_arg = count($split_url) - 1;
                    $split_url = $split_url[$last_arg];

                    if($slug != $split_url){
                        //Single
                        //echo 'Worked';
                    }else{
                        //Archive
                        //echo 'Not Worked';
                    }


                //var_dump($split_url);
                ?>

                <?php
                    //$current_url = home_url() . $wp->request;
                ?>
                <script type="text/javascript">
                    //console.log('<?php echo get_post_type();?>');

                    jQuery(document).ready(function($){
                        function calculate_monthly_price(buy_price=100000,equity=0,yearly_interest=1,years_duration=1,fee=0,monthly_fee=0){
                            var result = 0;
                            var N = years_duration * 12;
                            console.log(years_duration);
                            N = N - (N*2);
                            var MF = monthly_fee;
                            var YR = yearly_interest / 100;
                            
                            
                            //MR = YR/12;
                            var MR = YR/12;
                            
                            //L = B - E + F
                            var L = ( buy_price - equity ) + fee;
                            
                            //M = L*(MR/(1-(1+MR)^-N))
                            var find_M = (1 + MR);
                            find_M = Math.pow(find_M,N);
                            var M = L * ( MR / (1-find_M) );
                            
                            //TM=M+MF
                            var TM = M + MF;
                            TM = Math.round(TM);
                            
                            result = TM;
                            
                            
                            
                            return result;
                        }
                        
                        $('footer .sfc-archive').remove();

						$('.sfc-single .sfc_side_meta').on('click',function(){
							$(this).find('ul').toggle();
						});

                        $('.reset_filter').click(function(){
                            $('.sfc-filter input[type="checkbox"]').each(function(){
                                $(this).prop('checked',false);
                            });

                            $('.sfc-filter input[type="number"]').each(function(){
                                $('.sfc-filter input[type="number"]').val('');
                            });

                            $('.sfc-filter').trigger('submit');

                        });


                        var finn_slider_one = $('#finn_slider_one');
                        var item_cost = $('.sfc-price').first().find('.item_amount').text();
                        item_cost = item_cost.replace(/\s/g, '');
                        item_cost = item_cost.replace(/,/g, '');
                        item_cost = item_cost.replace('.', '');

                        item_cost = parseInt(item_cost);


                        var last_percentatge_amount = (item_cost * 99) / 100;
                        var round_figure = (last_percentatge_amount / 5000);

                        round_figure = Math.round(round_figure);
                        last_percentatge_amount = 5000 * round_figure;

                        //console.log(last_percentatge_amount + '\n');

                        rangesliderJs.create(finn_slider_one,{
                            min: 0,
                            <?php
                                if($slug != $split_url){
                                ?>
                                    max: last_percentatge_amount,
                                <?php
                                }else{
                                    ?>
                                    max: 1000000,
                                    <?php
                                }
                            ?>
                            step: 5000,
                            value: 150000,
							onInit: (value, percent, position) => {
                                var amount_left = item_cost - value;
                                $('.gb_rangeslider_new_container #amount_left').text(amount_left);
							},
                            onSlide: (value, percent, position) => {
                                $( ".finn_postsfilters_head_slide_one #amount_kontant" ).text( value );

                                var amount_slider = value;

                                amount_in_percent = (amount_slider/item_cost) * 100;
                                amount_in_percent = Math.round(amount_in_percent);

                                $('.calculate_percentage #amount_percentage').text(amount_in_percent);

                                var amount_left = item_cost - amount_slider;
                                $('.gb_rangeslider_new_container #amount_left').text(amount_left);

                                //amount_left

                                var year_slider = $('.finn_postsfilters_head_slide_two #amount_kontant').first().text();
                                //var ys = $('.finn_postsfilters_head_slide_two #amount_kontant').text();
                                

                                year_slider = parseInt(year_slider);
                                //year_slider = year_slider * 12;
                                var interest_rate = <?php echo get_option('gb_sultan_billink_interest_rate', true); ?>;

                                $('.sfc-items .sfc-item').each(function () {
                                    var product_price = $(this).find('.sfc-price .gb_price').text();
                                    product_price = product_price.replace(/\s/g, '');
                                    product_price = product_price.replace(/,/g, '');
                                    product_price = product_price.replace('.', '');

                                    product_price = parseFloat(product_price);
                                    var loan_price = product_price - amount_slider;
                                    if (loan_price > 0) {
                                        var monthly_loan_price = loan_price / year_slider;
                                        var monthly_loan_interest = (monthly_loan_price * interest_rate) / 100;
                                        var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                        
                                        monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                        
                                        $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                                        $(this).find('.card_sfc_monthly_price').text(monthly_installment.toFixed(0));
                                    } else {
                                        $(this).find('.sfc-monthly-price .mnd_price').text('0');
                                        $(this).find('.card_sfc_monthly_price').text('0');
                                    }
                                });

                                $('.sfc-single').each(function(){
                                    var product_price = $(this).find('.sfc-price .item_amount').text();
                                    product_price = product_price.replace(/\s/g, '');
                                    product_price = product_price.replace(/,/g, '');
                                    product_price = product_price.replace('.', '');

                                    product_price = parseFloat(product_price);
                                    
                                    
                                    var loan_price = product_price - amount_slider;
                                    if(loan_price > 0){
                                        var monthly_loan_price = loan_price / year_slider;

                                        var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                        var monthly_installment = monthly_loan_interest + monthly_loan_price;

                                        //console.log(year_slider);
                                        monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                        console.log(year_slider);
                                        console.log( product_price + ' | ' + amount_slider + ' | ' + interest_rate + ' | ' + year_slider + ' | ' + monthly_installment );
                                        
                                        $(this).find('.sfc-price .item_month_amount').text(monthly_installment.toFixed(0));
                                    }else{
                                        $(this).find('.sfc-price .item_month_amount').text('0');
                                    }
                                });

                            },

                        });

                        var finn_slider_two = $('#finn_slider_two');
                        rangesliderJs.create(finn_slider_two,{
                            min: 1,
                            max: 10,
                            step: 1,
                            value: 10,
                            onSlide: (value, percent, position) => {
                                $( ".finn_postsfilters_head_slide_two #amount_kontant" ).text( value );

                                var amount_slider = $('.finn_postsfilters_head_slide_one #amount_kontant').first().text();
                                var year_slider = value;
                                amount_slider = parseInt(amount_slider);
                                //year_slider = year_slider * 12;
                                var interest_rate = <?php echo get_option('gb_sultan_billink_interest_rate', true); ?>;

                                $('.sfc-items .sfc-item').each(function(){
                                    var product_price = $(this).find('.sfc-price .gb_price').text();
                                    product_price = product_price.replace(/\s/g, '');
                                    product_price = product_price.replace(/,/g, '');
                                    product_price = product_price.replace('.', '');

                                    product_price = parseFloat(product_price);
                                    var loan_price = product_price - amount_slider;
                                    if(loan_price > 0){
                                        var monthly_loan_price = loan_price / year_slider;
                                        var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                        var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                        
                                        monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                        
                                        $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                                        $(this).find('.card_sfc_monthly_price').text(monthly_installment.toFixed(0));
                                    }else{
                                        $(this).find('.sfc-monthly-price .mnd_price').text('0');
                                        $(this).find('.card_sfc_monthly_price').text('0');
                                    }
                                });

                                $('.sfc-single').each(function(){
                                    var product_price = $(this).find('.sfc-price .item_amount').text();
                                    product_price = product_price.replace(/\s/g, '');
                                    product_price = product_price.replace(/,/g, '');
                                    product_price = product_price.replace('.', '');

                                    product_price = parseFloat(product_price);
                                    var loan_price = product_price - amount_slider;
                                    if(loan_price > 0){
                                        var monthly_loan_price = loan_price / year_slider;
                                        var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                        var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                        
                                        monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                        
                                        $(this).find('.sfc-price .item_month_amount').text(monthly_installment.toFixed(0));
                                    }else{
                                        $(this).find('.sfc-price .item_month_amount').text('0');
                                    }
                                });
                            },
                        });

                        setTimeout(function(){

                            var amount_slider = $('.finn_postsfilters_head_slide_one #amount_kontant').first().text();
                            var year_slider = $('.finn_postsfilters_head_slide_two #amount_kontant').first().text();
                            amount_slider = parseInt(amount_slider);
                            //year_slider = year_slider * 12;
                            var interest_rate = <?php echo get_option('gb_sultan_billink_interest_rate', true); ?>;

                            $('.sfc-items .sfc-item').each(function(){
                                var product_price = $(this).find('.sfc-price .gb_price').text();
                                product_price = product_price.replace(/\s/g, '');
                                product_price = product_price.replace(/,/g, '');
                                product_price = product_price.replace('.', '');

                                product_price = parseFloat(product_price);
                                var loan_price = product_price - amount_slider;
                                if(loan_price > 0){
                                    var monthly_loan_price = loan_price / year_slider;
                                    var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                    var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                    
                                    monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                    
                                    $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                                    $(this).find('.card_sfc_monthly_price').text(monthly_installment.toFixed(0));
                                }else{
                                    $(this).find('.sfc-monthly-price .mnd_price').text('0');
                                    $(this).find('.card_sfc_monthly_price').text('0');
                                }
                            });

                            $('.sfc-single').each(function(){
                                var product_price = $(this).find('.sfc-price .item_amount').text();
                                product_price = product_price.replace(/\s/g, '');
                                product_price = product_price.replace(/,/g, '');
                                product_price = product_price.replace('.', '');

                                product_price = parseFloat(product_price);
                                var loan_price = product_price - amount_slider;
                                if(loan_price > 0){
                                    var monthly_loan_price = loan_price / year_slider;
                                    var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                    var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                    
                                    monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                    
                                    $(this).find('.sfc-price .item_month_amount').text(monthly_installment.toFixed(0));
                                }else{
                                    $(this).find('.sfc-price .item_month_amount').text('0');
                                }
                            });
                            
                            if($('.sfc-single').length > 0){
                                var item_cost_out = $('.sfc-single .sfc-price .item_amount').text();
                                item_cost_out = item_cost_out.replace(/\s/g, '');
                                item_cost_out = item_cost_out.replace(/,/g, '');
                                item_cost_out = item_cost_out.replace('.', '');
                                
                                var amount_slider_out = $('.finn_postsfilters_head_slide_one #amount_kontant').first().text();
                                amount_in_percent_out = (amount_slider_out/item_cost_out) * 100;
                                amount_in_percent_out = Math.round(amount_in_percent_out);
                                
                                console.log(amount_in_percent_out);
                                
                                $('.calculate_percentage #amount_percentage').text(amount_in_percent_out);
                            }
                        },1000);

                        <?php
                            if($slug != $split_url){
                                ?>
                                var finn_slider_one = $('#finn_slider_one_new');
                                var item_cost = $('.sfc-price').first().find('.item_amount').text();
                                item_cost = item_cost.replace(/\s/g, '');
                                item_cost = item_cost.replace(/,/g, '');
                                item_cost = item_cost.replace('.', '');

                                item_cost = parseInt(item_cost);
                                var last_percentatge_amount = (item_cost * 99) / 100;
                                var round_figure = (last_percentatge_amount / 5000);

                                round_figure = Math.round(round_figure);
                                last_percentatge_amount = 5000 * round_figure;

                                //console.log(last_percentatge_amount + '\n');

                                rangesliderJs.create(finn_slider_one,{
                                    min: 0,
                                    <?php
                                        if($slug != $split_url){
                                        ?>
                                            max: last_percentatge_amount,
                                        <?php
                                        }else{
                                            ?>
                                            max: 1000000,
                                            <?php
                                        }
                                    ?>
                                    step: 5000,
                                    value: 150000,
									onInit: (value, percent, position) => {
										var amount_left = item_cost - value;
                                        $('.gb_rangeslider_new_container #amount_left').text(amount_left);
                                        $('.lab #amount_left').text(amount_left);
									},
                                    onSlide: (value, percent, position) => {
                                        $( ".finn_postsfilters_head_slide_one #amount_kontant" ).text( value );

                                        var amount_slider = value;
                                        amount_in_percent = (amount_slider/item_cost) * 100;
                                        amount_in_percent = Math.round(amount_in_percent);

                                        $('.calculate_percentage #amount_percentage').text(amount_in_percent);

                                        var amount_left = item_cost - amount_slider;
                                        //console.log(amount_left);
                                        $('.gb_rangeslider_new_container #amount_left').text(amount_left);
                                        $('.lab #amount_left').text(amount_left);

                                        //amount_left

                                        var year_slider = $('#finn_slider_two_new').closest('.gb_rangeslider_new_container').find('#amount_kontant').text();
                                        //console.log(year_slider);

                                        year_slider = parseInt(year_slider);
                                        //year_slider = year_slider * 12;
                                        var interest_rate = <?php echo get_option('gb_sultan_billink_interest_rate', true); ?>;

                                        $('.sfc-items .sfc-item').each(function(){
                                            var product_price = $(this).find('.sfc-price .gb_price').text();
                                            product_price = product_price.replace(/\s/g, '');
                                            product_price = product_price.replace(/,/g, '');
                                            product_price = product_price.replace('.', '');

                                            product_price = parseFloat(product_price);
                                            var loan_price = product_price - amount_slider;
                                            if(loan_price > 0){
                                                var monthly_loan_price = loan_price / year_slider;
                                                var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                                var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                                
                                                monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                                
                                                $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                                                $(this).find('.card_sfc_monthly_price').text(monthly_installment.toFixed(0));
                                            }else{
                                                $(this).find('.sfc-monthly-price .mnd_price').text('0');
                                                $(this).find('.card_sfc_monthly_price').text('0');
                                            }
                                        });

                                        $('.sfc-single').each(function(){
                                            var product_price = $(this).find('.sfc-price .item_amount').text();
                                            product_price = product_price.replace(/\s/g, '');
                                            product_price = product_price.replace(/,/g, '');
                                            product_price = product_price.replace('.', '');

                                            product_price = parseFloat(product_price);
                                            var loan_price = product_price - amount_slider;
                                            if(loan_price > 0){
                                                var monthly_loan_price = loan_price / year_slider;
                                                var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                                var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                                
                                                monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                                
                                                $(this).find('.sfc-price .item_month_amount').text(monthly_installment.toFixed(0));
                                            }else{
                                                $(this).find('.sfc-price .item_month_amount').text('0');
                                            }
                                        });

                                    },

                                });

                                var finn_slider_two = $('#finn_slider_two_new');
                                rangesliderJs.create(finn_slider_two,{
                                    min: 1,
                                    max: 10,
                                    step: 1,
                                    value: 10,
                                    onSlide: (value, percent, position) => {
                                        $( ".finn_postsfilters_head_slide_two #amount_kontant" ).text( value );


                                        //var amount_slider = $('.finn_postsfilters_head_slide_one #amount_kontant').text();
                                        var amount_slider = $('#finn_slider_one_new').closest('.gb_rangeslider_new_container').find('#amount_kontant').text();
                                        var year_slider = value;
                                        amount_slider = parseInt(amount_slider);
                                        //year_slider = year_slider * 12;
                                        var interest_rate = <?php echo get_option('gb_sultan_billink_interest_rate', true); ?>;

                                        $('.sfc-items .sfc-item').each(function(){
                                            var product_price = $(this).find('.sfc-price .gb_price').text();
                                            product_price = product_price.replace(/\s/g, '');
                                            product_price = product_price.replace(/,/g, '');
                                            product_price = product_price.replace('.', '');

                                            product_price = parseFloat(product_price);
                                            var loan_price = product_price - amount_slider;
                                            if(loan_price > 0){
                                                var monthly_loan_price = loan_price / year_slider;
                                                var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                                var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                                
                                                monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                                
                                                $(this).find('.sfc-monthly-price .mnd_price').text(monthly_installment.toFixed(0));
                                                $(this).find('.card_sfc_monthly_price').text(monthly_installment.toFixed(0));
                                            }else{
                                                $(this).find('.sfc-monthly-price .mnd_price').text('0');
                                                $(this).find('.card_sfc_monthly_price').text('0');
                                            }
                                        });

                                        $('.sfc-single').each(function(){
                                            var product_price = $(this).find('.sfc-price .item_amount').text();
                                            product_price = product_price.replace(/\s/g, '');
                                            product_price = product_price.replace(/,/g, '');
                                            product_price = product_price.replace('.', '');

                                            product_price = parseFloat(product_price);
                                            var loan_price = product_price - amount_slider;
                                            if(loan_price > 0){
                                            var monthly_loan_price = loan_price / year_slider;
                                            var monthly_loan_interest = ( monthly_loan_price * interest_rate ) / 100;
                                            var monthly_installment = monthly_loan_interest + monthly_loan_price;
                                            
                                            monthly_installment = calculate_monthly_price(product_price,amount_slider,interest_rate,year_slider,5041,95);
                                                
                                            $(this).find('.sfc-price .item_month_amount').text(monthly_installment.toFixed(0));
                                            }else{
                                            $(this).find('.sfc-price .item_month_amount').text('0');
                                            }
                                        });
                                    },
                                });
                                <?php
                            }
                        ?>

						$('#sfc_sort').on('change', function(){
							$('form.sfc-filter').submit();
						});

                    });

                </script>
                <?php
            }

            if( get_post_type() == 'finn_post'){
                ?>
                <pre class="me_checking_stuff" style="display: none;"></pre>
                <meta name="robots" content="noindex,nofollow" />
                <?php
            }

        }
        add_action('wp_head','billink_checking_function');
    }

?>
