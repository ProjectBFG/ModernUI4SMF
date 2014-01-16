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

function template_boardindex_outer_above()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show some statistics if stat info is off.
	if (!$settings['show_stats_index'])
		echo '
	<div id="index_common_stats">
		', $txt['members'], ': ', $context['common_stats']['total_members'], ' &nbsp;&#8226;&nbsp; ', $txt['posts_made'], ': ', $context['common_stats']['total_posts'], ' &nbsp;&#8226;&nbsp; ', $txt['topics_made'], ': ', $context['common_stats']['total_topics'], '<br />
		', $settings['show_latest_member'] ? ' ' . sprintf($txt['welcome_newest_member'], ' <strong>' . $context['common_stats']['latest_member']['link'] . '</strong>') : '' , '
	</div>';

	template_newsfader();
}

function template_newsfader()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show the news fader?  (assuming there are things to show...)
	if (!empty($settings['show_newsfader']) && !empty($context['news_lines']))
	{
		echo '
			<div id="newsfader">
				<div class="cat_bar">
					<h3 class="catbg">
						<img id="newsupshrink" src="', $settings['images_url'], '/collapse.png" alt="*" title="', $txt['hide'], '" align="bottom" style="display: none;" />
						', $txt['news'], '
					</h3>
				</div>
				<div class="roundframe rfix" id="smfFadeScrollerCont">
					<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>
						<li>
							', implode('</li><li>', $context['news_lines']), '
						</li>
					</ul>
				</div>
			</div>
			<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
			<script type="text/javascript"><!-- // --><![CDATA[
				// Create a news fader object.
				var oNewsFader = new smc_NewsFader({
					sFaderControlId: \'smfFadeScroller\',
					sItemTemplate: ', JavaScriptEscape('%1$s'), ',
					iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
				});

				// Create the news fader toggle.
				var smfNewsFadeToggle = new smc_Toggle({
					bToggleEnabled: true,
					bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
					aSwappableContainers: [
						\'smfFadeScrollerCont\'
					],
					aSwapImages: [
						{
							sId: \'newsupshrink\',
							srcExpanded: smf_images_url + \'/collapse.png\',
							altExpanded: ', JavaScriptEscape($txt['hide']), ',
							srcCollapsed: smf_images_url + \'/expand.png\',
							altCollapsed: ', JavaScriptEscape($txt['show']), '
						}
					],
					oThemeOptions: {
						bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
						sOptionName: \'collapse_news_fader\',
						sSessionVar: smf_session_var,
						sSessionId: smf_session_id
					},
					oCookieOptions: {
						bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
						sCookieName: \'newsupshrink\'
					}
				});
			// ]]></script>
		';
	}
}

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<div id="boardindex_table" class="boardindex_table">';

	// Here is my luvly Windows(r) 8/8.1 Based Concept Work
	foreach ($context['categories'] as $category)
	{
		echo '
			<div class="tile-group double">';
		
		foreach ($category['boards'] as $board)
		{
			echo '
				<a class="tile double bg-dark" href="', $board['href'], '" id="b', $board['id'], '">
					<div class="tile-content icon">
						<img src="', $settings['images_url'], '/cake.png" alt="',$board['name'],'" />
					</div>
					<div class="brand">
						<span class="name">', $board['name'], '</span>
					</div>
				</a>';
		}
		echo '
			</div>';
	}
	echo '
	</div>
	<div class="clear"></div>';

	// Show the mark all as read button?
	if ($context['user']['is_logged'] && $settings['show_mark_read'] && !empty($context['categories']))
	echo '
		<div class="mark_read">', template_button_strip($context['mark_read_button'], 'right'), '</div>';
}

function template_boardindex_outer_below()
{
	template_info_center();
}

function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Here's where the "Info Center" starts...
	echo '
	<div class="full_width">
		<div class="tile quadro">
			<div class="tile-content">
				<div class="brand">
					<span class="name">
						', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), '
						
						<p class="inline">
							', $context['common_stats']['boardindex_total_posts'], '', !empty($settings['show_latest_member']) ? ' - '. $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '<br />
							', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' )<br />' : ''), '
							<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a><br />
						</p>
						<a href="', $scripturl, '?action=stats" title="', $txt['more_stats'], '">', $txt['forum_stats'], '</a>
							<div class="badge">
								<img class="icon" src="', $settings['images_url'], '/icons/info.png" alt="" />
							</div>
					</span>
				</div>
			</div>
		</div>';
		
		
	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']) && (!empty($context['latest_posts']) || !empty($context['latest_post'])))
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<a href="', $scripturl, '?action=recent"><img class="icon" src="', $settings['images_url'], '/post/xx.png" alt="" />', $txt['recent_posts'], '</a>
				</h4>
			</div>
			<div class="hslice" id="recent_posts_content">
				<div class="entry-title" style="display: none;">', $context['forum_name_html_safe'], ' - ', $txt['recent_posts'], '</div>
				<div class="entry-content" style="display: none;">
					<a rel="feedurl" href="', $scripturl, '?action=.xml;type=webslice">', $txt['subscribe_webslice'], '</a>
				</div>';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<p id="infocenter_onepost" class="inline">
					<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>&nbsp;&quot;', sprintf($txt['is_recent_updated'], '&quot;' . $context['latest_post']['link'], '&quot;'), ' (', $context['latest_post']['time'], ')<br />
				</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
				<table id="ic_recentposts">
					<tr>
						<th class="recentpost first_th">', $txt['message'], '</th>
						<th class="recentposter">', $txt['author'], '</th>
						<th class="recentboard">', $txt['board'], '</th>
						<th class="recenttime last_th">', $txt['date'], '</th>
					</tr>';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
					<tr>
						<td class="recentpost"><strong>', $post['link'], '</strong></td>
						<td class="recentposter">', $post['poster']['link'], '</td>
						<td class="recentboard">', $post['board']['link'], '</td>
						<td class="recenttime">', $post['time'], '</td>
					</tr>';
			echo '
				</table>';
		}
		echo '
			</div>';
	}
	
	echo '
		
		<div class="tile quadro">
			<div class="brand">
				<span class="name">
					', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<div class="badge"><img class="icon" src="', $settings['images_url'], '/icons/online.png', '" alt="" /></div>', $txt['online_users'], '', $context['show_who'] ? '</a>' : '', '
				</span>
			</div>';
		
	// "Users online" - in order of activity.
	echo '
			<p class="inline">
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<strong>', $txt['online'], ': </strong>', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ', comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();
	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . ($context['num_spiders'] == 1 ? $txt['hidden'] : $txt['hidden_s']);

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '

				&nbsp;-&nbsp;', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>&nbsp;-&nbsp;
				', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')<br />';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				', sprintf($txt['users_active'], $modSettings['lastActive']), ': ', implode(', ', $context['list_users_online']);

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<span class="membergroups">[' . implode(',&nbsp;', $context['membergroups']). ']</span>';
	}

	echo '
			</p>
		</div>';	
		
	echo '	
		<div class="tile">
			<div class="tile-content icon">
				<i class="icon-checkmark"></i>
			</div>
			<div class="tile-status">
				Mark Read
			</div>
		</div>
	</div>
	<div class="roundframe" id="info_center">
		<div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>';

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<a href="', $scripturl, '?action=calendar' . '"><img class="icon" src="', $settings['images_url'], '/icons/calendar.png', '" alt="" />', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '</a>
				</h4>
			</div>';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
			echo '
				<p class="inline holiday"><span>', $txt['calendar_prompt'], '</span> ', implode(', ', $context['calendar_holidays']), '</p>';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
			echo '
				<p class="inline">
					<span class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span>';
			// Each member in calendar_birthdays has: id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?)
			foreach ($context['calendar_birthdays'] as $member)
				echo '
					<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong class="fix_rtl_names">' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '' : ', ';
			echo '
				</p>';
		}

		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<p class="inline">
					<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';

			// Each event in calendar_events should have:
			//		title, href, is_last, can_edit (are they allowed?), modify_href, and is_today.
			foreach ($context['calendar_events'] as $event)
				echo '
					', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/calendar_modify.png" alt="*" class="centericon" /></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';
			echo '
				</p>';
		}
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
	}

	// If they are logged in, but statistical information is off... show a personal message bar.
	if ($context['user']['is_logged'] && !$settings['show_stats_index'])
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img class="icon" src="', $settings['images_url'], '/message_sm.png" alt="" />', $txt['personal_message'], '', $context['allow_pm'] ? '</a>' : '', '
				</h4>
			</div>
			<p class="pminfo">
					', empty($context['user']['messages']) ? $txt['you_have_no_msg'] : ($context['user']['messages'] == 1 ? sprintf($txt['you_have_one_msg'], $scripturl . '?action=pm') : sprintf($txt['you_have_many_msgs'], $scripturl . '?action=pm', $context['user']['messages'])), '
			</p>';
	}

	echo '
		</div>
	</div>';

	// Info center collapse object.
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var oInfoCenterToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header_ic']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'upshrinkHeaderIC\'
			],
			aSwapImages: [
				{
					sId: \'upshrink_ic\',
					srcExpanded: smf_images_url + \'/collapse.png\',
					altExpanded: ', JavaScriptEscape($txt['hide']), ',
					srcCollapsed: smf_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['show']), '
				}
			],
			aSwapLinks: [
				{
					sId: \'upshrink_link\',
					msgExpanded: ', JavaScriptEscape(sprintf($txt['info_center_title'], $context['forum_name_html_safe'])), ',
					msgCollapsed: ', JavaScriptEscape(sprintf($txt['info_center_title'], $context['forum_name_html_safe'])), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header_ic\',
				sSessionId: smf_session_id,
				sSessionVar: smf_session_var,
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrinkIC\'
			}
		});
	// ]]></script>';
}
?>