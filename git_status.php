<?php

/*
Plugin Name: Git Status
Plugin URI: https://github.com/topdown/WP-Git-Status
Description: <strong>(PHP 5+ is required)</strong> This plugin displays the current status if you are running under a git repository. The point of the plugin is to show the file status without needing to login with SSH for know reason.
Version: 1.0.0
Author: Jeff Behnke
Author URI: http://validwebs.com
License: GPL2
*/

/*  Copyright 2012  Jeff Behnke  (email : code@validwebs.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This plugin displays the current status if you are running under a git repository.
 *
 * PHP version 5 required
 *
 * Created 3/26/12, 2:14 AM
 *
 * @category   WordPress Plugin
 * @package    WP Git Status - git_status.php
 * @author     Jeff Behnke <code@validwebs.com>
 * @copyright  2009-12 ValidWebs.com
 * @license    GPL MIT
 */
class git_status
{

	/**
	 * Holds the path to the plugin file
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * @var string
	 */
	public $version = '1.0.0';


	/**
	 * Used for everything from the page request to the options array name
	 *
	 * @var string $plugin_slug
	 */
	private $plugin_slug = 'git_status';

	/**
	 * Used for the plugin pages title and page title
	 *
	 * @var string
	 */
	private $plugin_title = 'Git Status';

	/**
	 * Initiate the plugin
	 */
	public function __construct()
	{

		// Set Plugin Path
		$this->plugin_path = dirname(__FILE__);

		// Run this stuff only in the admin panel
		if (is_admin())
		{
			// admin menu
			add_action('admin_menu', array(
				$this,
				$this->plugin_slug . '_menu'
			));

			// Settings link for the plugin page
			add_filter('plugin_action_links', array(
				$this,
				'plugin_action_links'
			), 10, 2);

			add_action('init', array(
				$this,
				'github_updater_init'
			));
		}

		add_action('admin_bar_menu', array(
			$this,
			'admin_bar_git_status'
		), 161);

	}

	/**
	 * Add a quick link to the admin bar
	 *
	 * @return mixed
	 */
	public function admin_bar_git_status()
	{
		/** @var $wp_admin_bar WP_Admin_Bar */
		global $wp_admin_bar;
		if (!is_super_admin() || !is_admin_bar_showing())
		{
			return;
		}

		$wp_admin_bar->add_menu(array(
			'parent' => '',
			'id'     => 'git-status',
			'title'  => 'Git Status',
			'href'   => admin_url('options-general.php?page=git_status')
		));
	}

	/**
	 * This sets a link Settings to the plugin settings page on the WP Plugins page
	 *
	 * @param array  $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function plugin_action_links($links, $file)
	{
		if ($file == 'WP-Git-Status/git_status.php')
		{
			$settings_link = '<a href="options-general.php?page=' . $this->plugin_slug . '">' . __('Git Status', $this->plugin_slug) . '</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}

	/**
	 * Menu instance for the plugins setting page
	 */
	public function git_status_menu()
	{
		//create new menu item
		//( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = NULL )
		add_options_page(
			'Git Status Settings',
			'Git Status',
			'administrator',
			$this->plugin_slug,
			array(
				$this,
				$this->plugin_slug . '_settings'
			)
		);
	}

	/**
	 * The settings page and form
	 */
	public function git_status_settings()
	{
		global $current_user;

		// Need to have permissions to be here
		get_currentuserinfo();
		if (!current_user_can('manage_options'))
		{
			die();
		}
		?>

	<div class="wrap">

		<div id="icon-tools" class="icon32"></div>
		<h2><?php echo $this->plugin_title; ?></h2>

		<p>Version: <?php echo $this->version; ?></p>

		<p>This plugin simple shows the current Git state. Assuming you are using git version control.
			<br />
			If you are not using Git remove the plugin as it servers no purpose.
		</p>
		<iframe src="<?php echo WP_CONTENT_URL; ?>/plugins/WP-Git-Status/status.php" width='800px' style="min-height: 600px;"></iframe>

	</div>
	<?php
	}


	public function github_updater_init()
	{

		include_once('updater.php');

		define('WP_GITHUB_FORCE_UPDATE', true);

		$config = array(
			'slug'               => plugin_basename(__FILE__),
			'proper_folder_name' => plugin_basename(__FILE__),
			'api_url'            => 'https://api.github.com/repos/topdown/WP-Git-Status',
			'raw_url'            => 'https://raw.github.com/topdown/WP-Git-Status/master',
			'github_url'         => 'https://github.com/topdown/WP-Git-Status',
			'zip_url'            => 'https://github.com/topdown/WP-Git-Status/zipball/master',
			'sslverify'          => true,
			'requires'           => '3.0',
			'tested'             => '3.3',
		);

		new WPGitHubUpdater($config);

	}
}

// Plugin instance
$git_status = new git_status();
