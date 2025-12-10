<?php
/**
 * Plugin Name: Prefixseo Loopify Form
 * Plugin URI: http://www.prefixseo.com
 * Description: place loopify form with [prfx-loopify-form] using shortcode
 * Version: 1.0
 * Author: Prefixseo
 * Author URI: http://www.prefixseo.com
 * License: GPL2
 */

function prefxs_shortcode_loopify_forms_callback() {
    ob_start();
    ?>
<style>
.nyhetsbrevform-wrapper {
    position: relative;
	padding-right: 10px;
	margin-top: 40px;
	margin-bottom: 40px;
}
.nyhetsbrevform-wrapper .blockk > label {
    margin: 0;
	cursor: pointer;
	color: black !important;
}

.nyhetsbrevform-wrapper .blockk {
    display: flex;
    align-items: center;
    column-gap: 5px;
	flex-wrap: wrap;
	margin-top: 10px;
}
.nyhetsbrevform-wrapper .blockk > .bricks-button{
		    padding-right: 30px;
		padding-left: 30px;
		border-color: #cb0d2a;
		background-color: #cb0d2a;
		color: #fff;
	}
	.prfxso-error-text{
		color: #cb0d2a;
	}
	.prfxso-success-text{
		color: green;
	}
	.nyhetsbrevform-wrapper .blockk > .bricks-button:hover{
		background: black;
	}
	.blockk.mb-5 {
    margin-bottom: 30px;
}
	.nyhetsbrevform-wrapper .blockk #Email{
		    width: 49.5%;
    padding: 5px 5px;
    border-radius: 0px;
    text-indent: 15px;
		background:#fafafa;
	}
	.nyhetsbrevform-wrapper .blockk #Email::placeholder{
		color:grey;
	}
	.nyhetsbrevform-wrapper #signupform > div.blockk:first-child{
		flex-wrap: nowrap;
	}
	@media (min-width: 992px){
		.nyhetsbrevform-wrapper .blockk > .blockk{
			width: 40%;
		}
	}
	@media (max-width: 991px){
		.nyhetsbrevform-wrapper .blockk > .blockk{
			width: 100%;
		}
		.nyhetsbrevform-wrapper .blockk #Email{
			width: 70%;
			line-height: 25px;
		}
		.nyhetsbrevform-wrapper .blockk > .bricks-button {
			padding-right: 20px;
			padding-left: 20px;
			font-size: 15px;
			line-height: 36px;
		}
	}
</style>
    <div class="nyhetsbrevform-wrapper">
        <form class="signupform" name="signupform" id="signupform" method="post">
			<div class="blockk mb-5">
				<input id="Email" type="text" required="" name="Email" placeholder="Din e-post">
				<input type="hidden" name="redirectUrl" id="redirectUrl" value="<?=get_home_url()?>">
				<input id="submit" class="bricks-button lg circle bricks-link-type-external" type="submit" value="Meld deg på">
			</div>
			<p style="font-size:14px;" class="blockk">
				<input class="checkbox" type="checkbox" id="concentletter" name="concentletter" value="yes" >
				<label for="concentletter">Bekrefter samtykke til personvernerklæring</label>
			</p>
			<div id="output"></div>
			<br>

				<div class="blockk">
					<input class="checkbox" type="checkbox" id="porsgrunn" name="porsgrunn" value="porsgrunn" >
					<label for="porsgrunn">Autostrada Porsgrunn</label>
				</div>
				<div class="blockk">
					<input class="checkbox" type="checkbox" id="arendal" name="arendal" value="arendal" >
					<label for="arendal">Autostrada Arendal</label>
				</div>
				<div class="blockk">
					<input class="checkbox" type="checkbox" id="porsche" name="porsche" value="porsche" >
					<label for="porsche">Porsche Center Porsgrunn</label>
				</div>
				<div class="blockk">
					<input class="checkbox" type="checkbox" id="notodden" name="notodden" value="notodden">
					<label for="notodden">Autostrada Notodden</label>
				</div>
				<div class="blockk">
					<input class="checkbox" type="checkbox" id="seljord" name="seljord" value="seljord">
					<label for="seljord">Autostrada Seljord</label>
				</div>
				<div class="blockk">
					<input class="checkbox" type="checkbox" id="kongsberg" name="kongsberg" value="kongsberg">
					<label for="kongsberg">Autostrada Kongsberg</label>
				</div>
			</div>
        </form>
    </div>
    <script>
    jQuery( document ).ready(function() {
        jQuery("#signupform").submit(function(e) {
            
            var err = '0';
            var sendt = '0';
            e.preventDefault(); // avoid to execute the actual submit of the form.
			
			// -- Newsletter
			if(!jQuery('#concentletter').is(':checked')){
				jQuery('#output').text('Du må bekrefte samtykke før du kan melde deg på nyhetsbrevet.').addClass('prfxso-error-text').removeClass('prfxso-success-text');
				return false;
			}
        
            var form = jQuery(this);
        
            if (jQuery('#arendal').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/5f3e6c1eab7498e2548936c7/external-forms/dd24aa25-4d1d-4897-83c6-021dcd051e19',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });
                
            }
        
            if (jQuery('#notodden').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/6169873ce3111848e4747710/external-forms/f15395be-1cda-4963-bb66-dea8f1dc3ed0',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });
                
            }
            
            if (jQuery('#skien').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/5f3e2d509944c742431df564/external-forms/b4d36daf-b374-4014-8a19-cdff3f2bc320',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });		 
            }
        
            if (jQuery('#porsgrunn').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/5f3e6cc4ab7498e2548936c8/external-forms/f2f82923-8b0a-4347-a8fa-4215ea75e877',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });		 
            }
			
			if (jQuery('#seljord').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/6204fe6a9c3304d6d5375c33/external-forms/4d1ac955-be7d-4550-9483-8d4b55f7185c',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });		 
            }
			
        
            if (jQuery('#porsche').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/5f3e7c2cab7498e254893999/external-forms/91a76dac-d85c-4d7a-bf5c-40bfd2f45207',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });		 
            }
			
			            if (jQuery('#kongsberg').is(":checked")) {
            jQuery.ajax({
                type: "POST",
                url:'https://api.loopify.com/flows/6553835a3536aa6eaf405c56/external-forms/b21bdbd0-1090-4fc5-999f-c54501eb5a8d',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    jQuery('#output').text('Din påmelding er sendt'); 
                    sendt = '1';
                },
                    error: function(data)
                { 
                    err = '1';
                }
            });		 
            }
            
            if (sendt == '0') {
                jQuery('#output').text('Du må velge minst en av avdelingene.').removeClass('prfxso-error-text').addClass('prfxso-success-text');
            }
            if (err == '1') {
                jQuery('#output').text('Det oppstod en feil under sendingen av påmelding.').addClass('prfxso-error-text').removeClass('prfxso-success-text');
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('prfx-loopify-form', 'prefxs_shortcode_loopify_forms_callback');


function prfxso_plusfix_script_to_fotter_callback() {
	?>
<script>
jQuery(document).ready(function($){
 window.setTimeout(function(){
    var appendListelsbilers = '';
   	var eleslistgetter = document.querySelectorAll("#prefxso-sorter-grid div.wpgb-metro > article");
    $("#prefxso-sorter-grid div.wpgb-metro").html("");
    prfx_costomeordres.forEach(function(keyword) {
        eleslistgetter.forEach(function(node) {
            if($(node).find('h3>a').text().toLowerCase().includes(keyword.toLowerCase())){
                $("#prefxso-sorter-grid div.wpgb-metro").append(node);
            }
        });
    });
  }, 1000);
});
</script>
	<?php
}
add_action('wp_footer', 'prfxso_plusfix_script_to_fotter_callback', 99);
?>