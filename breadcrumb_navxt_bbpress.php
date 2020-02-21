<?php
/*
Plugin Name: Breadcrumb NavXT bbPress Extensions
Plugin URI: https://mtekk.us/extensions/breadcrumb-navxt-bbpress-extensions
Description: Fixes a few edge cases that bbPress presents, specifically fixes the breadcrumb trail in topic tag archives. For details on how to use this plugin visit <a href="https://mtekk.us/extensions/breadcrumb-navxt-bbpress-extensions">Breadcrumb NavXT bbPress Extensions</a>. 
Version: 1.1.2
Author: John Havlik
Author URI: http://mtekk.us/
License: GPL2
TextDomain: breadcrumb-navxt-bbpress
DomainPath: /languages/
*/
/*  Copyright 2014-2020  John Havlik  (email : john.havlik@mtekk.us)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once(dirname(__FILE__) . '/includes/block_direct_access.php');
add_filter('bcn_type_archive_post_type', 'bcn_bbp_type_topic_swap', 11);
/**
 * Hooks into the bcn_type_archive_post_type filter and replaces 'topic' with 'forum' for the type
 * 
 * @param string $type The post type name to change/filter
 * 
 * @return string The filtered post type name
 */
function bcn_bbp_type_topic_swap($type)
{
	//If we have a bbPress topic, replace it with forum
	if($type == 'topic' && !bbp_is_topic_archive())
	{
		$type = 'forum';
	}
	return $type;
}
add_action('bcn_after_fill', 'bcn_bbp_filler', 11);
/**
 * Hooks into the bcn_after_fill action and deals with filling the bbPress items
 * 
 * @param bcn_breadcrumb_trail &$breadcrumb_trail The Breadcrumb NavXT breadcrumb trail object that we're playing with
 */
function bcn_bbp_filler($breadcrumb_trail)
{
	//Exit early is this is not a bbPress resource
	if(!function_exists('is_bbpress') || !is_bbpress())
	{
		return;
	}
	//Handle user pages
	if(bbp_is_single_topic())
	{
		//Start by adding in the forum archive link (so that we have a known good position)
		bcn_bbp_do_forum_archive($breadcrumb_trail);
	}
}
function bcn_bbp_do_forum_archive(&$breadcrumb_trail)
{
	$breadcrumb = new bcn_breadcrumb(
			bbp_get_forum_title($GLOBALS['post']->post_parent),
			null,
			array('groups', 'groups-directory'),
			bbp_get_forum_permalink($GLOBALS['post']->post_parent),
			null,
			true);
	array_splice($breadcrumb_trail->breadcrumbs, count($breadcrumb_trail->breadcrumbs)-1, 0, array($breadcrumb));
}
