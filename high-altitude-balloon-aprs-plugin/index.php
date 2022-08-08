<?php
/*
Plugin Name:    High Altitude Balloon APRS Tracker Plugin
Plugin URI:     https://github.com/mkbodanu4/high-altitude-balloon-aprs-plugin
Description:    Add map with High Altitude Balloons to your WordPress site with shortcodes.
Version:        1.0
Author:         UR5WKM
Author URI:     https://diy.manko.pro
Text Domain:    high-altitude-balloon-aprs-plugin
*/

class High_Altitude_Balloon_APRS_Tracker_Plugin
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_shortcode('hab_tracker_map', array($this, 'map_shortcode'));

        add_action('wp_ajax_habat_data', array($this, 'ajax_data'));
        add_action('wp_ajax_nopriv_habat_data', array($this, 'ajax_data'));

        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'setting_page'));

        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array($this, 'deactivate'));

        load_plugin_textdomain('high-altitude-balloon-aprs-plugin', FALSE, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function deactivate()
    {
        delete_option('habat_frontend_url');
        delete_option('habat_api_key');

        unregister_setting('habat_options_group', 'habat_frontend_url');
        unregister_setting('habat_options_group', 'habat_api_key');
    }

    public function register_settings()
    {
        register_setting('habat_options_group', 'habat_frontend_url');
        register_setting('habat_options_group', 'habat_api_key');
    }

    public function setting_page()
    {
        add_options_page(
            __('High Altitude Balloon APRS Tracker Plugin Settings', 'high-altitude-balloon-aprs-plugin'),
            __('High Altitude Balloon APRS Tracker Plugin', 'high-altitude-balloon-aprs-plugin'),
            'manage_options',
            'habat-setting',
            array($this, 'html_form')
        );
    }

    public function html_form()
    {
        ?>
        <style>
            .habat_table {
                border: 1px solid #d3d3d3;
                border-collapse: collapse;
                width: 100%;
            }

            .habat_table td, .habat_table th {
                border: 1px solid #d3d3d3;
                padding: 5px;
                background-color: #fbfbfb;
            }

            .habat_shortcode {
                padding: 24px 10px;
                background-color: #fbfbfb;
                font-size: 17px;
                text-align: center
            }
        </style>
        <div class="wrap">
            <h2><?= __('Plugin Settings', 'high-altitude-balloon-aprs-plugin'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('habat_options_group'); ?>
                <h3><?= __('High Altitude Balloon APRS Tracker Monitor', 'high-altitude-balloon-aprs-plugin'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="habat_frontend_url">
                                <?= __('URL to High Altitude Balloon APRS Tracker API', 'high-altitude-balloon-aprs-plugin') . ":"; ?>
                            </label>
                        </th>
                        <td>
                            <input type='text' class="regular-text" id="habat_frontend_url" name="habat_frontend_url"
                                   placeholder="<?= __('E.g.', 'high-altitude-balloon-aprs-plugin'); ?> https://demo.com/folder/"
                                   value="<?= get_option('habat_frontend_url'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="habat_api_key">
                                <?= __('API Key', 'high-altitude-balloon-aprs-plugin') . ":"; ?>
                            </label>
                        </th>
                        <td>
                            <input type='text' class="regular-text" id="habat_api_key" name="habat_api_key"
                                   placeholder="<?= __('E.g.', 'high-altitude-balloon-aprs-plugin'); ?> xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                   value="<?= get_option('habat_api_key'); ?>">
                        </td>
                    </tr>
                </table>

                <h3><?= __('Map', 'high-altitude-balloon-aprs-plugin') . ":"; ?></h3>
                <div>
                    <div class="habat_shortcode">
                        [<b>hab_tracker_map</b>
                        map_header="<i><?= __('High Altitude Balloon APRS Tracker', 'high-altitude-balloon-aprs-plugin'); ?></i>"
                        show_filters="<i>Yes</i>"
                        only_last_point="<i>No</i>"
                        from="<i>1 hour ago</i>"
                        map_height="<i>480</i>"
                        map_zoom="<i>1</i>"
                        map_center="<i>49.0139,31.2858</i>"]
                    </div>
                    <table class="habat_table">
                        <tr>
                            <th>
                                <?= __('Attribute', 'high-altitude-balloon-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Explanation', 'high-altitude-balloon-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Mandatory?', 'high-altitude-balloon-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Example', 'high-altitude-balloon-aprs-plugin'); ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <i>map_header</i>
                            </td>
                            <td>
                                <?= __('Map header', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('High Altitude Balloon APRS Tracker', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>show_filters</i>
                            </td>
                            <td>
                                <?= __('Show filters above map, Yes or No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                Yes
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>only_last_point</i>
                            </td>
                            <td>
                                <?= __('Show only last point on map, Yes or No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                No
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>from</i>
                            </td>
                            <td>
                                <?= __('Filter data starting from this date (also accept relative date like "1 hour ago", "6 days ago" etc)', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                1 hour ago
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>to</i>
                            </td>
                            <td>
                                <?= __('Filter data till this date (also accept relative date like "1 hour ago", "6 days ago" etc)', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                1 hour ago
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_height</i>
                            </td>
                            <td>
                                <?= __('Map height (px)', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                480
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_zoom</i>
                            </td>
                            <td>
                                <?= __('Map zoom', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                1
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_center</i>
                            </td>
                            <td>
                                <?= __('Map center coordinates', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'high-altitude-balloon-aprs-plugin'); ?>
                            </td>
                            <td>
                                49.0139,31.2858
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>

        </div>
        <?php
    }

    public function map_shortcode($attributes)
    {
        $guid = substr(md5(mt_rand()), 0, 7);

        $args = shortcode_atts(array(
            'map_header' => '',
            'show_filters' => 'Yes',
            'only_last_point' => 'No',
            'from' => '',
            'to' => '',
            'map_height' => 480,
            'map_zoom' => 1,
            'map_center' => '',
        ), $attributes);

        if (!$args['map_zoom'] || !$args['map_center'] || !$args['map_center']) {
            return __('Missing mandatory attributes, check shortcode', 'high-altitude-balloon-aprs-plugin');
        }

        ob_start();
        ?>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/leaflet/leaflet.css"/>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/pikaday2-datetimepicker/pikaday.css"/>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/font-awesome/css/font-awesome.min.css"/>
        <style>
            #habat_map_<?= $guid; ?> {
                height: <?= intval($args['map_height']).'px'; ?>;
            }

            #habat_map_overlay_<?= $guid; ?> {
                position: relative;
                margin-top: <?= '-'.intval($args['map_height']).'px'; ?>;
                height: <?= intval($args['map_height']).'px'; ?>;
                background-color: #fff;
                z-index: 999999;
                opacity: 0.4;
                display: none;
            }

            .habat_text_bold {
                font-weight: bold;
            }

            <?php if (strtolower($args['show_filters']) === "yes") { ?>
            #habat_map_filters_<?= $guid; ?> {
                padding: 5px 0;
                text-align: right;
            }

            #call_sign_<?= $guid; ?>,
            #from_<?= $guid; ?>,
            #to_<?= $guid; ?> {
                max-width: 140px;
                text-align: center;
            }

            #refresh_<?= $guid; ?> {
                font-family: "Roboto", "Open Sans", sans-serif;
                font-weight: 400;
                padding: 10px 12px;
                min-height: 37px;
                background: #185345;
                color: #fff;
                border: solid 1px #185345;
                border-radius: 0;
                -webkit-appearance: none;
                -webkit-transition: background 0.2s;
                transition: background 0.2s;
            }

            #refresh_<?= $guid; ?>:hover {
                background: #1d362e;
            }

            #refresh_<?= $guid; ?>:disabled {
                background: #788680;
                border: solid 1px #788680;
            }

            #reset_<?= $guid; ?> {
                font-family: "Roboto", "Open Sans", sans-serif;
                font-weight: 400;
                padding: 10px 12px;
                min-height: 37px;
                background: #531818;
                color: #fff;
                border: solid 1px #531818;
                border-radius: 0;
                -webkit-appearance: none;
                -webkit-transition: background 0.2s;
                transition: background 0.2s;
            }

            #reset_<?= $guid; ?>:hover {
                background: #3f2121;
            }

            #reset_<?= $guid; ?>:disabled {
                background: #703f3f;
                border: solid 1px #703f3f;
            }

            .pika-time th,
            .pika-time td,
            .pika-table th,
            .pika-table td {
                padding: 0;
                border: none;
            }

            <?php } ?>
        </style>
        <?= $args['map_header'] ? "<h4>" . $args['map_header'] . "</h4>" : "" ?>

        <?php
        if (strtolower($args['show_filters']) === "yes") {
            ?>
            <div id="habat_map_filters_<?= $guid; ?>">
                <div>
                    <label id="track_call_sign_box_<?= $guid; ?>" style="display: none">
                        <input type="checkbox" id="track_call_sign_<?= $guid; ?>"
                               title="<?= __('Call Sign', 'high-altitude-balloon-aprs-plugin'); ?>" value="1">
                        <?= __('Track', 'high-altitude-balloon-aprs-plugin'); ?>
                    </label>
                    <input type="text" id="call_sign_<?= $guid; ?>"
                           placeholder="<?= __('Call Sign', 'high-altitude-balloon-aprs-plugin'); ?>" value="">
                    <input type="text" id="from_<?= $guid; ?>"
                           placeholder="<?= __('From date', 'high-altitude-balloon-aprs-plugin'); ?>" value="">
                    <input type="text" id="to_<?= $guid; ?>"
                           placeholder="<?= __('To date', 'high-altitude-balloon-aprs-plugin'); ?>" value="">
                    <button onclick="habat_map_reset_<?= $guid; ?>();" id="reset_<?= $guid; ?>"
                            title="<?= __('Reset filters', 'high-altitude-balloon-aprs-plugin'); ?>">
                        <i class="fa fa-times"></i>
                    </button>
                    <button onclick="habat_map_reload_data_<?= $guid; ?>();" id="refresh_<?= $guid; ?>"
                            title="<?= __('Refresh map', 'high-altitude-balloon-aprs-plugin'); ?>">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
            <?php
        }
        ?>
        <div id="habat_map_box_<?= $guid; ?>">
            <div id="habat_map_<?= $guid; ?>"></div>
            <div id="habat_map_overlay_<?= $guid; ?>"></div>
        </div>

        <script src="<?= plugin_dir_url(__FILE__); ?>modules/moment/moment-with-locales.min.js"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>modules/leaflet/leaflet.js"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>modules/pikaday2-datetimepicker/pikaday.js"></script>
        <script>
            var habat_map_<?= $guid; ?>,
                habat_map_markers_<?= $guid; ?> = [],
                habat_map_polylines_<?= $guid; ?> = [],
                habat_map_from_<?= $guid; ?> = '<?= $args['from'] ? date('Y-m-d H:i:00', strtotime($args['from'])) : ''; ?>',
                habat_map_to_<?= $guid; ?> = '<?= $args['to'] ? date('Y-m-d H:i:00', strtotime($args['to'])) : ''; ?>',
                habat_updating_<?= $guid; ?> = false;

            function prevent_scroll_<?= $guid; ?>(e) {
                e.preventDefault();
                e.stopPropagation();

                return false;
            }

            function habat_map_reload_data_<?= $guid; ?>() {
                if (habat_updating_<?= $guid; ?>)
                    return

                habat_updating_<?= $guid; ?> = true;

                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                document.getElementById('refresh_<?= $guid; ?>').setAttribute('disabled', 'disabled');
                <?php } ?>
                document.getElementById('habat_map_overlay_<?= $guid; ?>').style.display = 'block';
                document.getElementById('habat_map_overlay_<?= $guid; ?>').addEventListener('wheel', prevent_scroll_<?= $guid; ?>);

                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "<?= admin_url('admin-ajax.php');?>", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        var json = JSON.parse(this.response);

                        if (json.data !== undefined) {
                            var marker, polyline;

                            habat_map_markers_<?= $guid; ?>.forEach(function (marker) {
                                habat_map_<?= $guid; ?>.removeLayer(marker);
                            });
                            habat_map_markers_<?= $guid; ?> = [];

                            habat_map_polylines_<?= $guid; ?>.forEach(function (polyline) {
                                habat_map_<?= $guid; ?>.removeLayer(polyline);
                            });
                            habat_map_polylines_<?= $guid; ?> = [];

                            for (const [call_sign, history] of Object.entries(json.data)) {
                                var polyline_coordinates = [], img, iconSize, iconAnchor;

                                history.forEach(function (packet, idx) {
                                    var latlng = new L.LatLng(packet.lat, packet.lng);
                                    polyline_coordinates.push(latlng);

                                    if (idx === history.length - 1) {
                                        img = '<?= plugin_dir_url(__FILE__); ?>images/balloon.svg';
                                        iconSize = [24, 24];
                                        iconAnchor = [12, 24]

                                        <?php if (strtolower($args['show_filters']) === "yes") { ?>
                                        if (document.getElementById('call_sign_<?= $guid; ?>').value && document.getElementById('track_call_sign_<?= $guid; ?>').checked)
                                            habat_map_<?= $guid; ?>.setView(latlng, 9);
                                        <?php } ?>
                                    } else {
                                        img = '<?= plugin_dir_url(__FILE__); ?>images/dot.svg';
                                        iconSize = [16, 16];
                                        iconAnchor = [8, 8];
                                    }
                                    marker = L.marker(latlng, {
                                        title: call_sign,
                                        icon: L.icon({
                                            iconUrl: img,
                                            iconSize: iconSize,
                                            iconAnchor: iconAnchor
                                        })
                                    }).addTo(habat_map_<?= $guid; ?>);
                                    var packet_date = +new Date(packet.t * 1000);
                                    marker.bindPopup('<div>' + moment(packet_date).fromNow() + '</div>' +
                                        '<div class="habat_text_bold">' + call_sign + '</div>' +
                                        '<div>' + moment(packet_date).format("LLL") + '</div>' +
                                        (packet.s !== undefined && packet.s ? '<div><b><?= __('Speed', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.s + '</div>' : '') +
                                        (packet.a !== undefined && packet.a ? '<div><b><?= __('Altitude', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.a + ' m</div>' : '') +
                                        (packet.c !== undefined && packet.c ? '<div><b><?= __('Comment', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.c + '</div>' : ''));
                                    habat_map_markers_<?= $guid; ?>.push(marker);
                                });

                                if (polyline_coordinates.length > 1) {
                                    polyline = L.polyline(polyline_coordinates, {
                                        color: '#175a95'
                                    }).addTo(habat_map_<?= $guid; ?>);
                                    habat_map_polylines_<?= $guid; ?>.push(polyline);
                                }
                            }
                        }
                    }

                    habat_updating_<?= $guid; ?> = false;
                    <?php if (strtolower($args['show_filters']) === "yes") { ?>
                    document.getElementById('refresh_<?= $guid; ?>').removeAttribute('disabled');
                    <?php } ?>
                    document.getElementById('habat_map_overlay_<?= $guid; ?>').style.display = 'none';
                    document.getElementById('habat_map_overlay_<?= $guid; ?>').removeEventListener('wheel', prevent_scroll_<?= $guid; ?>);
                };
                var xhttp_params = {
                    _ajax_nonce: "<?= wp_create_nonce('nonce-name');?>",
                    action: 'habat_data',
                    get: 'history',
                };
                /*
                // Load only part of data from shown part of map. Works slower than loading less points
                xhttp_params.south_west_lat = habat_map_<?= $guid; ?>.getBounds().getSouthWest().lat;
                xhttp_params.south_west_lng = habat_map_<?= $guid; ?>.getBounds().getSouthWest().lng;
                xhttp_params.north_east_lat = habat_map_<?= $guid; ?>.getBounds().getNorthEast().lat;
                xhttp_params.north_east_lng = habat_map_<?= $guid; ?>.getBounds().getNorthEast().lng;
                */
                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                if (document.getElementById('from_<?= $guid; ?>').value)
                    xhttp_params.from = document.getElementById('from_<?= $guid; ?>').value;
                if (document.getElementById('to_<?= $guid; ?>').value)
                    xhttp_params.to = document.getElementById('to_<?= $guid; ?>').value;
                if (document.getElementById('call_sign_<?= $guid; ?>').value)
                    xhttp_params.call_sign = document.getElementById('call_sign_<?= $guid; ?>').value;
                <?php } else { ?>
                xhttp_params.from = habat_map_from_<?= $guid; ?>;
                xhttp_params.to = habat_map_to_<?= $guid; ?>;
                <?php } ?>
                <?php if (strtolower($args['only_last_point']) === "yes") { ?>
                xhttp_params.only_last_point = true;
                <?php } ?>
                xhttp.send(new URLSearchParams(xhttp_params).toString());
            }

            <?php if (strtolower($args['show_filters']) === "yes") { ?>
            function habat_map_reset_<?= $guid; ?>() {
                habat_map_from_<?= $guid; ?> = '<?= $args['from'] ? date('Y-m-d H:i', strtotime($args['from'])) : ''; ?>';
                habat_map_to_<?= $guid; ?> = '<?= $args['to'] ? date('Y-m-d H:i', strtotime($args['to'])) : ''; ?>';

                document.getElementById('call_sign_<?= $guid; ?>').value = '';
                document.getElementById('track_call_sign_<?= $guid; ?>').checked = false;
                document.getElementById('track_call_sign_box_<?= $guid; ?>').style.display = 'none';
                window.location.hash = '';
                document.getElementById('from_<?= $guid; ?>').value = habat_map_from_<?= $guid; ?> && habat_map_from_<?= $guid; ?>.length > 0 ? habat_map_from_<?= $guid; ?> : '';
                document.getElementById('to_<?= $guid; ?>').value = habat_map_to_<?= $guid; ?> && habat_map_to_<?= $guid; ?>.length > 0 ? habat_map_to_<?= $guid; ?> : '';
            }
            <?php } ?>

            function get_hash_parts_<?= $guid; ?>() {
                var hash = location.hash.substring(1),
                    dict = {};

                if (hash.length > 0) {
                    hash.split('&').map(function (item) {
                        var pair = item.split('=');
                        if (pair.length === 2) {
                            dict[pair[0]] = pair[1];
                        }
                    });
                }

                return dict;
            }

            function get_hash_<?= $guid; ?>(dict) {
                var hash = '#', parts = [],
                    keys = Object.keys(dict);

                if (keys.length > 0) {
                    keys.forEach(function (key) {
                        parts.push([key, dict[key]].join('='))
                    });
                }

                return hash + parts.join('&');
            }

            document.addEventListener("DOMContentLoaded", function (event) {
                moment.locale("<?=get_locale();?>");

                habat_map_<?= $guid; ?> = L.map('habat_map_<?= $guid; ?>').setView(JSON.parse("<?= json_encode(array_map(function ($float) {
                    return floatval($float);
                }, explode(",", $args['map_center']))); ?>"), <?= $args['map_zoom'] && is_numeric($args['map_zoom']) ? $args['map_zoom'] : 5; ?>);

                var southWest = new L.latLng(-90, -180),
                    northEast = new L.latLng(90, 180);
                var bounds = new L.latLngBounds(southWest, northEast);

                habat_map_<?= $guid; ?>.setMaxBounds(bounds);
                habat_map_<?= $guid; ?>.setMinZoom(1);
                habat_map_<?= $guid; ?>.on('drag', function () {
                    habat_map_<?= $guid; ?>.panInsideBounds(bounds, {animate: false});
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(habat_map_<?= $guid; ?>);

                /*
                // Load only part of data from shown part of map. Works slower than loading less points
                habat_map_<?= $guid; ?>.on('moveend', function (e) {
                    habat_map_reload_data_<?= $guid; ?>();
                });
                */
                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                var hash_dict = get_hash_parts_<?= $guid; ?>();

                if (hash_dict['call_sign'] !== undefined) {
                    document.getElementById('call_sign_<?= $guid; ?>').value = hash_dict['call_sign'];
                    document.getElementById('track_call_sign_box_<?= $guid; ?>').style.display = 'inline-block';
                    if (hash_dict['track'] !== undefined) {
                        document.getElementById('track_call_sign_<?= $guid; ?>').checked = (hash_dict['track'] === '1');
                    }
                } else {
                    document.getElementById('track_call_sign_box_<?= $guid; ?>').style.display = 'none';
                    document.getElementById('track_call_sign_<?= $guid; ?>').checked = false;
                }

                document.getElementById('from_<?= $guid; ?>').value = habat_map_from_<?= $guid; ?> && habat_map_from_<?= $guid; ?>.length > 0 ? habat_map_from_<?= $guid; ?> : '';
                var from_date = new Pikaday({
                    field: document.getElementById('from_<?= $guid; ?>')
                });
                document.getElementById('to_<?= $guid; ?>').value = habat_map_to_<?= $guid; ?> && habat_map_to_<?= $guid; ?>.length > 0 ? habat_map_to_<?= $guid; ?> : '';
                var to_date = new Pikaday({
                    field: document.getElementById('to_<?= $guid; ?>')
                });

                document.getElementById('call_sign_<?= $guid; ?>').onkeyup = function (e) {
                    var hash_dict = get_hash_parts_<?= $guid; ?>();
                    var call_sign = document.getElementById('call_sign_<?= $guid; ?>').value.trim();
                    if (call_sign.length > 0) {
                        document.getElementById('track_call_sign_box_<?= $guid; ?>').style.display = 'inline-block';
                        hash_dict['call_sign'] = call_sign
                    } else {
                        document.getElementById('track_call_sign_box_<?= $guid; ?>').style.display = 'none';
                        if (hash_dict['call_sign'] !== undefined) {
                            delete hash_dict['call_sign'];
                        }
                    }
                    window.location.hash = get_hash_<?= $guid; ?>(hash_dict);
                };
                document.getElementById('track_call_sign_<?= $guid; ?>').onchange = function (e) {
                    var hash_dict = get_hash_parts_<?= $guid; ?>();
                    var track = document.getElementById('track_call_sign_<?= $guid; ?>').checked;
                    if (track) {
                        hash_dict['track'] = '1';
                    } else {
                        if (hash_dict['track'] !== undefined) {
                            delete hash_dict['track'];
                        }
                    }
                    window.location.hash = get_hash_<?= $guid; ?>(hash_dict);
                };
                <?php } ?>

                habat_map_reload_data_<?= $guid; ?>();
                setInterval(habat_map_reload_data_<?= $guid; ?>, 60000);
            });
        </script>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    public function ajax_data()
    {
        header("Content-type:application/json");

        $frontend_url = get_option('habat_frontend_url');
        $api_url = trim($frontend_url, '/') . '/api.php';
        $api_key = get_option('habat_api_key');

        $get = filter_var($_POST['get'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $call_sign = filter_var($_POST['call_sign'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $from = filter_var($_POST['from'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $to = filter_var($_POST['to'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $only_last_point = filter_var($_POST['only_last_point'], FILTER_VALIDATE_BOOLEAN);
        $south_west_lat = filter_var($_POST['south_west_lat'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $south_west_lng = filter_var($_POST['south_west_lng'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $north_east_lat = filter_var($_POST['north_east_lat'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $north_east_lng = filter_var($_POST['north_east_lng'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $params = array(
            'key' => $api_key,
            'get' => $get,
        );

        if ($call_sign) $params['call_sign'] = $call_sign;
        if ($from) $params['from'] = $from;
        if ($to) $params['to'] = $to;
        if ($only_last_point) $params['only_last_point'] = $only_last_point;
        if ($south_west_lat) $params['south_west_lat'] = $south_west_lat;
        if ($south_west_lng) $params['south_west_lng'] = $south_west_lng;
        if ($north_east_lat) $params['north_east_lat'] = $north_east_lat;
        if ($north_east_lng) $params['north_east_lng'] = $north_east_lng;

        $request_url = $api_url . '?' . http_build_query($params);

        if (!$frontend_url || !$api_key) {
            echo json_encode(array(
                "error" => "Invalid request"
            ));
            wp_die();
        }

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $request_url);
        curl_setopt($handler, CURLOPT_HEADER, FALSE);
        curl_setopt($handler, CURLINFO_HEADER_OUT, FALSE);
        curl_setopt($handler, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($handler, CURLOPT_MAXREDIRS, 10);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handler, CURLOPT_TIMEOUT, 30);
        curl_setopt($handler, CURLOPT_USERAGENT, "WordPress at " . get_home_url());
        $result = curl_exec($handler);
        $http_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        curl_close($handler);

        if (!$result) {
            http_response_code($http_code);
            echo json_encode(array(
                "code" => $http_code,
                "error" => "No response from remote API"
            ));
            wp_die();
        }

        $json = json_decode($result);

        if (!$json) {
            http_response_code(500);
            echo json_encode(array(
                "code" => $http_code,
                "error" => "Invalid remote API response",
                //"url" => $request_url,
                //"raw" => $result
            ));
            wp_die();
        }

        echo json_encode($json);

        wp_die();
    }
}

$High_Altitude_Balloon_APRS_Tracker_Plugin = new High_Altitude_Balloon_APRS_Tracker_Plugin();