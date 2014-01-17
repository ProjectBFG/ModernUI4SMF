<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2013 Simple Machines and individual contributors
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1 Alpha 1
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

/**
 * Initialize the template... mainly little settings.
 */
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	$settings['theme_version'] = '2.0';

	// Use plain buttons - as opposed to text buttons?
	$settings['use_buttons'] = true;

	// Show sticky and lock status separate from topic icons?
	$settings['separate_sticky_lock'] = true;

	// Does this theme use the strict doctype?
	$settings['strict_doctype'] = false;

	// Set the following variable to true if this theme requires the optional theme strings file to be loaded.
	$settings['require_theme_strings'] = false;

	// Set the following variable to true is this theme wants to display the avatar of the user that posted the last post on the board index and message index
	$settings['avatars_on_indexes'] = false;
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?alp21 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?alp21" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/metro-bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/metro-bootstrap-responsive.css" />';

	// The most efficient way of writing multi themes is to use a master index.css plus variant.css files.
	if (!empty($context['theme_variant']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?alp21" />';

	// Save some database hits, if a width for multiple wrappers is set in admin.
	if (!empty($settings['forum_width']))
		echo '
	<style type="text/css">#wrapper, .frame {width: ', $settings['forum_width'], ';}</style>';

	// load in any css from mods or themes so they can overwrite if wanted
	template_css();

	// load in any javascript files from mods and themes
	template_javascript();		echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/jquery.widget.min.js"></script>	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/metro.min.js"></script>';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
	{
		echo '
		<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css?alp21" />';

	if (!empty($context['theme_variant']))
		echo '
		<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl', $context['theme_variant'], '.css?alp21" />';
	}

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="contents" href="', $scripturl, '" />', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search" />' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss2;action=.xml" />
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?type=atom;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
	{
		echo '
	<link rel="next" href="', $context['links']['next'], '" />';
	}

	if (!empty($context['links']['prev']))
	{
		echo '
	<link rel="prev" href="', $context['links']['prev'], '" />';
	}

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body class="metro darkbg">';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Wrapper div now echoes permanently for better layout options. h1 a is now target for "Go up" links.
	echo '
	<nav class="navigation-bar dark">
		<nav class="navigation-bar-content">';
	
	// If the user is logged in, display some things that might be useful.
	if ($context['user']['is_logged'])
	{
		echo '
			<div class="element">
				<a class="dropdown-toggle" href="#" id="profile_menu_top">', $context['user']['name'], '</a>
				<ul id="profile_menu" class="dropdown-menu" data-role="dropdown"></ul>
			</div>';
			
		if ($context['allow_pm'])
		{
		echo '
			<div class="element">
				<a class="dropdown-toggle" href="', $scripturl, '?action=pm"', !empty($context['self_pm']) ? ' class="active"' : '', ' id="pm_menu_top">', $txt['pm_short'], !empty($context['user']['unread_messages']) ? ' <span class="amt">' . $context['user']['unread_messages'] . '</span>' : '', '</a>
				<ul id="pm_menu" class="dropdown-menu" data-role="dropdown"></ul>
			</div>';
		}
		
		echo '
			<div class="element">
				<a class="dropdown-toggle" href="', $scripturl, '?action=alerts"', !empty($context['self_alerts']) ? ' class="active"' : '', ' id="alerts_menu_top">', $txt['alerts'], !empty($context['user']['alerts']) ? ' <span class="amt">' . $context['user']['alerts'] . '</span>' : '', '</a>
				<ul id="alerts_menu" class="dropdown-menu" data-role="dropdown"></ul>
			</div>';
	}	
	
	// Otherwise they're a guest. Ask them to either register or login.
	else
		echo '
			<div class="element">
				', sprintf($txt[$context['can_register'] ? 'welcome_guest_register' : 'welcome_guest'], $txt['guest_title'], $scripturl . '?action=login'), '
			</div>';
			
		if ($context['allow_search'])
		{
		echo '
			<div class="element input-element place-right">
			<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
				<input type="text" name="search" value="" class="input_text" />&nbsp;';

		// Using the quick search dropdown?
		if (!empty($modSettings['search_dropdown']))
		{
			$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');

			echo '
				<select name="search_selection">
					<option value="all"', ($selected == 'all' ? ' selected="selected"' : ''), '>', $txt['search_entireforum'], ' </option>';

			// Can't limit it to a specific topic if we are not in one
			if (!empty($context['current_topic']))
				echo '
					<option value="topic"', ($selected == 'current_topic' ? ' selected="selected"' : ''), '>', $txt['search_thistopic'], '</option>';

		// Can't limit it to a specific board if we are not in one
		if (!empty($context['current_board']))
			echo '
					<option value="board"', ($selected == 'current_board' ? ' selected="selected"' : ''), '>', $txt['search_thisbrd'], '</option>';
			echo '
					<option value="members"', ($selected == 'members' ? ' selected="selected"' : ''), '>', $txt['search_members'], ' </option>
				</select>';
		}

		// Search within current topic?
		if (!empty($context['current_topic']))
			echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_topic' : 'topic'), '" value="', $context['current_topic'], '" />';
		// If we're on a certain board, limit it to this board ;).
		elseif (!empty($context['current_board']))
			echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_brd[' : 'brd['), $context['current_board'], ']"', ' value="', $context['current_board'], '" />';

		echo '
				<input type="submit" name="search2" value="', $txt['search'], '" class="button_submit" />
				<input type="hidden" name="advanced" value="0" />
			</form>
		</div>';
	}	

	// Unread things? now I love you button
	if ($context['user']['is_logged'])
	echo '
		<div class="element">
			<a href="', $scripturl, '?action=unread" title="', $txt['unread_since_visit'], '">', $txt['view_unread_category'], '</a>
		</div>
		<div class="element">
			<a href="', $scripturl, '?action=unreadreplies" title="', $txt['show_unread_replies'], '">', $txt['unread_replies'], '</a>
		</div>';
						
	echo '		
		</nav>
	</nav>';

	echo '
	<div id="header">
		<div class="frame">
			<h1 class="forumtitle">
				<a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
			</h1>';

	echo '
	<div class="floatright">
		<div class="tile live bg-dark" data-role="live-tile" data-effect="slideUp">
			<div class="tile-content">
				<div class="brand">
					<span class="name">DayZ Mod</span>
				</div>
			</div>
			<div class="tile-content">
				<div class="brand">
					<span class="name">ARMA II</span>
				</div>
			</div>
		</div>
	</div>';

	echo'
		</div>
	</div>';

	// Show the menu here, according to the menu sub template, followed by the navigation tree.
	template_menu();

	theme_linktree();

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show the XHTML, RSS and WAP2 links, as well as the copyright.
	// Footer is now full-width by default. Frame inside it will match theme wrapper width automatically.
	echo '
	<div id="footer_section">
		<div class="frame">';

	// There is now a global "Go to top" link at the right.
		echo '
			<a href="#top" id="bot" class="go_up">', $txt['go_up'], '</a>
			<ul class="reset">
				<li class="copyright">', theme_copyright(), '</li>
			</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';

	echo '
		</div>
	</div>';

}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// load in any javascipt that could be defered to the end of the page
	template_javascript(true);

	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 * @param bool $force_show = false
 */
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
				<div class="navigate_section">
					<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
						<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the first one.
		// Better here. Always points to the next level when the linktree breaks to a second line.
		// Picked a better looking HTML entity, and added support for RTL plus a span for styling.
		if ($link_num != 0)
			echo '
							<span class="dividers">', $context['right_to_left'] ? ' &#9668; ' : ' &#9658; ', '</span>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
							<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
						</li>';
	}

	echo '
					</ul>
				</div>';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
				<nav class="horizontal-menu compact">
					<ul>';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
						<li id="button_', $act, '">
							<a class="tile half bg-dark" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
								<div class="tile-content icon">
									<i class="icon-', $button['title'], '"></i>
								</div>
							</a>
						</li>';
	}

	echo '
					</ul>
				</nav>';
}

/**
 * Generate a strip of buttons.
 * @param array $button_strip
 * @param string $direction = ''
 * @param array $strip_options = array()
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// @todo this check here doesn't make much sense now (from 2.1 on), it should be moved to where the button array is generated
		// Kept for backward compatibility
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintainence'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

function template_maint_warning_below()
{

}

?>