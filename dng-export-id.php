<?php
/**
 * Plugin Name: DNG WP InDesign Export
 * Plugin URI: https://github.com/CarlosLongarela/DNG-WP-InDesign-Export
 * Description: This plugin is includer in a bigger plugin for DNG Photo Magazine admin, this plugin has its admin menus and other integrations with DNG WordPress portal. This code is adapted for this portal but you can use it chnaging id categories, path for export files (this code don't check it because that task is handled by other plugin part). This plugin is made for fotodng.com, feel free to adpat to your WordPress installation and InDesign workflow, if you want CSS for html exported and other files contact me at carlos@longarela.eu
 * Version: 0.1.0
 * Author: Carlos Longarela
 * Author URI: https://tabernawp.com/
 *
 * @package DNG WP InDesign Export
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dng-export-id
 *
 * DNG WP InDesign Export is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * DNG WP InDesign Export is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DNG WP InDesign Export. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Register Menu
 */
function dng_register_menu_page() {
	add_menu_page(
		__( 'Export to InDesign', 'dng-export-id' ),
		'InDesign export',
		'manage_options',
		'dng-export-id/dng-export-id-page.php',
		'',
		'dashicons-paperclip',
		75
	);
}
add_action( 'admin_menu', 'dng_register_menu_page' );
