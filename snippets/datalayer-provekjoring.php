add_action("wp_footer", function() {
    $provekjoring_pages = [
        'provekjoring',
        'bestill-provekjoring-volvo',
        'bestill-provekjoring-mercedes-benz',
        'bestill-provekjoring-peugeot',
        'bestill-provekjoring-xpeng',
        'bestill-provekjoring-kia'
    ];

    $is_provekjoring = false;
    foreach ($provekjoring_pages as $slug) {
        if (is_page($slug)) {
            $is_provekjoring = true;
            break;
        }
    }
    if (!$is_provekjoring) return;
    ?>
    <script>
    (function() {
        if (typeof jQuery === 'undefined') return;

        var formConfig = {
            '47': {
                merke: null,
                merkeFieldId: '677',
                avdelingFields: {
                    'Volvo': '701',
                    'Mercedes-Benz': '704',
                    'Kia': '707',
                    'XPENG': '709',
                    'Peugeot': '712',
                    'Polestar': '713'
                },
                telefonKey: 'yei32957d5e0273',
                epostKey: 'vojo37ebe05407a3'
            },
            '9': {
                merke: 'Volvo',
                avdelingFieldId: '119',
                avdelingType: 'radio',
                stripPrefix: 'Volvo ',
                telefonKey: 'yei3d5d139f71b',
                epostKey: 'vojo351a9a3a936'
            },
            '42': {
                merke: 'Mercedes-Benz',
                avdelingKey: 'w4pi43',
                avdelingType: 'select',
                stripPrefix: 'Autostrada ',
                telefonKey: 'yei32957d5e0272',
                epostKey: 'vojo37ebe05407a2'
            },
            '32': {
                merke: 'Peugeot',
                avdelingKey: 'velgforhandler',
                avdelingType: 'select',
                stripPrefix: 'Autostrada ',
                telefonKey: 'yei30265a32f05d1d953a46c',
                epostKey: 'vojo3048498715cb959897bd0'
            },
            '41': {
                merke: 'XPENG',
                avdelingFieldId: '669',
                avdelingType: 'radio',
                stripPrefix: 'XPENG ',
                telefonKey: 'yei3aae92fa99c2',
                epostKey: 'vojo38146d3a5202'
            },
            '44': {
                merke: 'Kia',
                avdelingKey: 'lh1lq',
                avdelingType: 'select',
                stripPrefix: 'Autostrada ',
                telefonKey: 'yei3aae92fa99c4',
                epostKey: 'vojo38146d3a5204'
            }
        };

        function getFieldValueById(fieldId) {
            var hidden = document.querySelector('input[name="item_meta[' + fieldId + ']"][type="hidden"]');
            if (hidden && hidden.value) return hidden.value;
            var checked = document.querySelector('input[name="item_meta[' + fieldId + ']"]:checked');
            return checked ? checked.value : '';
        }

        function getSelectValue(fieldKey) {
            var sel = document.getElementById('field_' + fieldKey);
            return sel ? sel.value : '';
        }

        function getTextValue(fieldKey) {
            var el = document.getElementById('field_' + fieldKey);
            return el ? el.value : '';
        }

        function formatPhone(phone) {
            phone = phone.replace(/[\s\-()]/g, '');
            phone = phone.replace(/^(\+47|0047)/, '');
            if (phone) {
                phone = '+47' + phone;
            }
            return phone;
        }

        var pushed = {};

        function captureAndPush(formEl) {
            var formIdInput = formEl.querySelector('input[name="form_id"]');
            var formId = formIdInput ? formIdInput.value : '';
            var config = formConfig[formId];
            if (!config) return;
            if (pushed[formId]) return;

            var merke = '';
            var lokasjon = '';

            if (config.merke) {
                merke = config.merke;
            } else if (config.merkeFieldId) {
                merke = getFieldValueById(config.merkeFieldId);
            }

            if (config.avdelingFields) {
                var avdFieldId = config.avdelingFields[merke];
                if (avdFieldId) {
                    lokasjon = getFieldValueById(avdFieldId);
                }
            } else if (config.avdelingType === 'radio') {
                lokasjon = getFieldValueById(config.avdelingFieldId);
            } else if (config.avdelingType === 'select') {
                lokasjon = getSelectValue(config.avdelingKey);
            }

            if (config.stripPrefix && lokasjon.indexOf(config.stripPrefix) === 0) {
                lokasjon = lokasjon.substring(config.stripPrefix.length);
            }

            var telefon = formatPhone(getTextValue(config.telefonKey));
            var epost = getTextValue(config.epostKey);

            if (!epost && !telefon) return;

            pushed[formId] = true;

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'provekjoring_submit',
                merke: merke,
                lokasjon: lokasjon,
                epost: epost,
                telefon: telefon
            });

            console.log('dataLayer push: provekjoring_submit', {merke: merke, lokasjon: lokasjon, epost: epost, telefon: telefon});
        }

        // Fang submit-event før skjemaet sendes (fungerer med både AJAX og page reload)
        jQuery(document).on('submit', 'form.frm-show-form', function() {
            captureAndPush(this);
        });

        // Backup: lytt også på frmFormComplete for AJAX-skjemaer
        jQuery(document).on('frmFormComplete', function(event, form) {
            captureAndPush(form);
        });

        // Volvo/Polestar redirect-klikk (disse sender aldri skjemaet)
        jQuery(document).on('click', 'a[href*="volvocars.com/no/test-drive"], a[href*="polestar.com/no/test-drive"]', function(e) {
            var link = this;
            var href = link.href || '';
            var merke = href.indexOf('polestar.com') !== -1 ? 'Polestar' : 'Volvo';
            var lokasjon = '';

            if (merke === 'Volvo') {
                if (href.indexOf('AD4PK') !== -1) lokasjon = 'Porsgrunn';
                else if (href.indexOf('FC3JA') !== -1) lokasjon = 'Arendal';

                if (!lokasjon) {
                    var avd = getFieldValueById('119') || getFieldValueById('701');
                    if (avd) lokasjon = avd.replace(/^Volvo\s+/, '');
                }
            } else {
                lokasjon = 'Porsgrunn';
            }

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'provekjoring_redirect',
                merke: merke,
                lokasjon: lokasjon,
                redirect_url: href,
                eventCallback: function() {
                    // Naviger etter GTM har fanget eventet
                    if (link.target === '_blank') {
                        window.open(href, '_blank');
                    } else {
                        document.location = href;
                    }
                },
                eventTimeout: 500
            });

            console.log('dataLayer push: provekjoring_redirect', {merke: merke, lokasjon: lokasjon, redirect_url: href});

            // Stopp default navigering - eventCallback håndterer det
            e.preventDefault();
        });
    })();
    </script>
    <?php
});
