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
                        since="1 day ago"
                        map_header="<i><?= __('High Altitude Balloon APRS Tracker', 'high-altitude-balloon-aprs-plugin'); ?></i>"
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
                                <i>since</i>
                            </td>
                            <td>
                                <?= __('Starting from date (accept relative date like "1 hour ago", "6 days ago" etc)', 'high-altitude-balloon-aprs-plugin'); ?>
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
            'since' => '',
            'map_header' => '',
            'map_height' => 480,
            'map_zoom' => 1,
            'map_center' => '',
        ), $attributes);

        if (!$args['map_zoom'] || !$args['map_center'] || !$args['map_center']) {
            return __('Missing mandatory attributes, check shortcode', 'high-altitude-balloon-aprs-plugin');
        }

        ob_start();
        ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
              integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
              crossorigin=""/>
        <style>
            #habat_map_<?= $guid; ?> {
                height: <?= intval($args['map_height']).'px'; ?>;
            }

            .habat_text_bold {
                font-weight: bold;
            }
        </style>
        <?php if ($args['map_header']) { ?>
        <h4>
            <?= $args['map_header']; ?>
        </h4>
    <?php } ?>
        <div id="habat_map_<?= $guid; ?>"></div>

        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment-with-locales.min.js"
                integrity="sha256-QwcluVRoJ33LzMJ+COPYcydsAIJzcxCwsa0zA5JRGEc=" crossorigin="anonymous"></script>

        <script>
            var habat_map_since_<?= $guid; ?> = '<?= $args['since']; ?>';
            var habat_map_<?= $guid; ?>,
                habat_map_markers_<?= $guid; ?> = [],
                habat_map_polylines_<?= $guid; ?> = [];

            function habat_map_reload_data_<?= $guid; ?>() {

                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "<?= admin_url('admin-ajax.php');?>", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        var json = JSON.parse(this.response),
                            marker, polyline;

                        habat_map_markers_<?= $guid; ?>.forEach(function (marker) {
                            habat_map_<?= $guid; ?>.removeLayer(marker);
                        });
                        habat_map_markers_<?= $guid; ?> = [];

                        habat_map_polylines_<?= $guid; ?>.forEach(function (polyline) {
                            habat_map_<?= $guid; ?>.removeLayer(polyline);
                        });
                        habat_map_polylines_<?= $guid; ?> = [];

                        json.data.forEach(function (call_sign, i) {
                            var polyline_coordinates = [], img, iconSize, iconAnchor;

                            call_sign.history.forEach(function (packet, idx) {
                                if (idx === call_sign.history.length - 1) {
                                    img = '<?= plugin_dir_url(__FILE__); ?>images/balloon.svg';
                                    iconSize = [24, 24];
                                    iconAnchor = [12, 24]
                                } else {
                                    img = '<?= plugin_dir_url(__FILE__); ?>images/dot.svg';
                                    iconSize = [16, 16];
                                    iconAnchor = [8, 8];
                                }
                                marker = L.marker([packet.latitude, packet.longitude], {
                                    title: call_sign.call_sign,
                                    icon: L.icon({
                                        iconUrl: img,
                                        iconSize: iconSize,
                                        iconAnchor: iconAnchor
                                    })
                                }).addTo(habat_map_<?= $guid; ?>);
                                marker.bindPopup('<div>' + moment(call_sign.date).format("LLL") + '</div>' +
                                    '<div class="habat_text_bold">' + call_sign.call_sign + '</div>' +
                                    (packet.speed ? '<div><b><?= __('Speed', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.speed + '</div>' : '') +
                                    (packet.altitude ? '<div><b><?= __('Altitude', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.altitude + ' m</div>' : '') +
                                    (packet.comment ? '<div><b><?= __('Comment', 'high-altitude-balloon-aprs-plugin'); ?></b>: ' + packet.comment + '</div>' : ''));
                                habat_map_markers_<?= $guid; ?>.push(marker);

                                polyline_coordinates.push([packet.latitude, packet.longitude]);
                            });

                            polyline = L.polyline(polyline_coordinates, {
                                color: '#175a95'
                            }).addTo(habat_map_<?= $guid; ?>);
                            habat_map_polylines_<?= $guid; ?>.push(polyline)
                        });
                    }
                };
                xhttp.send("action=habat_data&get=history" + (habat_map_since_<?= $guid; ?>.length > 0 ? "&since=" + habat_map_since_<?= $guid; ?> : ""));
            }

            document.addEventListener("DOMContentLoaded", function (event) {
                moment.locale("<?=get_locale();?>");

                habat_map_<?= $guid; ?> = L.map('habat_map_<?= $guid; ?>').setView(JSON.parse("<?= json_encode(array_map(function ($float) {
                    return floatval($float);
                }, explode(",", $args['map_center']))); ?>"), <?= $args['map_zoom'] && is_numeric($args['map_zoom']) ? $args['map_zoom'] : 5; ?>);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(habat_map_<?= $guid; ?>);

                habat_map_reload_data_<?= $guid; ?>();
                setInterval(habat_map_reload_data_<?= $guid; ?>, 60000);
            });
        </script>
        <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
                integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
                crossorigin=""></script>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    public function ajax_data()
    {
        $frontend_url = get_option('habat_frontend_url');
        $api_url = trim($frontend_url, '/') . '/api.php';
        $api_key = get_option('habat_api_key');

        $get = filter_var($_POST['get'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $since = filter_var($_POST['since'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $params = array(
            'key' => $api_key,
            'get' => $get ?? NULL,
            'since' => $since ?? NULL
        );
        $request_url = $api_url . '?' . http_build_query($params);

        if (!$frontend_url || !$api_key)
            wp_die();

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
        curl_close($handler);

        header("Content-type:application/json");
        echo $result;

        wp_die();
    }
}

$High_Altitude_Balloon_APRS_Tracker_Plugin = new High_Altitude_Balloon_APRS_Tracker_Plugin();