<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// Most Used Languages
$template->assign_vars(array(
	'L_RANK' => $lang['Rank'],
	'L_TITLE' => $lang['module_name_most_used_languages'],
	'L_LANGUAGE' => $lang['Language'],
	'L_HOWMANY' => $lang['How_many']
	)
);

$sql = "SELECT user_lang, COUNT(*) as number
	FROM " . USERS_TABLE . "
	WHERE user_lang <> ''
	GROUP BY user_lang
	ORDER BY number DESC
	LIMIT " . $return_limit;
$result = $stat_db->sql_query($sql);
$lang_count = $stat_db->sql_numrows($result);
$lang_data = $stat_db->sql_fetchrowset($result);

$template->_tpldata['stats_row.'] = array();

for ($i = 0; $i < $lang_count; $i++)
{
	$class = ($i % 2) ? $theme['td_class2'] : $theme['td_class1'];

	$template->assign_block_vars('stats_row', array(
		'RANK' => $i + 1,
		'CLASS' => $class,
		'LANGUAGE' => $lang_data[$i]['user_lang'],
		'HOWMANY' => $lang_data[$i]['number'])
	);
}

?>