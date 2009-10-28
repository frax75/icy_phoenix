<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Selven (Selven@zaion.com)
*
*/

define('IN_ICYPHOENIX', true);

if(!empty($setmodules))
{
	$file = basename(__FILE__);
	$module['2300_FAQ']['110_FAQ_BBCode'] = $file . '?file=bbcode';
	$module['2300_FAQ']['120_FAQ_Board'] = $file . '?file=faq';
	$module['2300_FAQ']['130_FAQ_Rules'] = $file . '?file=rules';
	return;
}

if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
require('pagestart.' . PHP_EXT);

define('Q', 0);
define('A', 1);
include(IP_ROOT_PATH . 'includes/functions_selects.' . PHP_EXT);

/* this function takes the FAQ array generated as a result
 * of including the lang_faq.php file and turns it into
 * a pair of arrays, $blocks and $quests.
 *    $blocks - just contains numerically indexed block titles
 *    $quests - is in the following format:
 *      $quests[$block_number][$question_number][Q] - is the question
 *      $quests[$block_number][$question_number][A] - is the answer
 */
if (!function_exists('faq_to_array'))
{
	function faq_to_array($faq)
	{
		$blocks = array();
		$quests = array();

		$block_no = -1;
		$quest_no = 0;

		for($i = 0; $i < sizeof($faq); $i++)
		{
			if($faq[$i][0] == '--')
			{
				$block_no++;
				$blocks[$block_no] = $faq[$i][1];
				$quests[$block_no] = array();
				$quest_no = 0;
			}
			else
			{
				$quests[$block_no][$quest_no][Q] = $faq[$i][0];
				$quests[$block_no][$quest_no][A] = $faq[$i][1];
				$quest_no++;
			}
		}

		return array($blocks, $quests);
	} /* END function faq_to_array */
}

/* this function takes the array generated by faq_to_array and changes
 * it back into lines suitable for dumping to a lang_faq.php file. It
 * returns a numerically-indexed array of said lines.
 */
if (!function_exists('array_to_faq'))
{
	function array_to_faq($blocks, $quests)
	{
		$lines = array();

		for($i = 0; $i < sizeof($blocks); $i++)
		{
			$lines[] = '$faq[] = array("--", "' . str_replace('"', '\"', $blocks[$i]) . '");' . "\n";

			for($j = 0; $j < sizeof($quests[$i]); $j++)
			{
				if(!empty($quests[$i][$j][Q]) && !empty($quests[$i][$j][A]))
				{
					$lines[] = '$faq[] = array("' . str_replace('"', '\"', $quests[$i][$j][Q]) . '", "' . str_replace('"', '\"', $quests[$i][$j][A]) . '");' . "\n";
				}
			}

			$lines[] = "\n";
		}

		return $lines;
	} /* END function array_to_faq */
}

/* this is the header which will be dumped to the FAQ
 * file each time we dump the page. Split up the < and
 * the ?php to avoid problems parsing this file!!
 */

$faq_header = '<' . '?php' . "\n\n";

/***************************************************************************
      This file was automatically generated by Admin FAQ Editor
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

//
// To add an entry to your FAQ simply add a line to this file in this format:
// ".'$'."faq[] = array(\"question\", \"answer\");
// If you want to separate a section enter ".'$'."faq[] = array(\"--\",\"Block heading goes here if wanted\");
// Links will be created automatically
//
// DO NOT forget the ; at the end of the line.
// Do NOT put double quotes (\") in your FAQ entries, if you absolutely must then escape them ie. \\\"something\\\"
//
// The FAQ items will appear on the FAQ page in the same order they are listed in this file
//\n\n";

$faq_footer = "\n\n?" . '>';

// initially include the current FAQ or BBCode guide, depending on the file= in the query_string
$file = isset($_GET['file']) ? htmlspecialchars($_GET['file']) : 'faq';

if(!isset($_GET['language']) && !isset($_POST['language']))
{
	$template->set_filenames(array('body' => ADM_TPL . 'faq_select_lang_body.tpl'));

	$template->assign_vars(array(
		'L_LANGUAGE' => $lang['faq_select_language'],
		'LANGUAGE_SELECT' => language_select($config['default_lang'], 'language', $phpbb_realpath . 'language'),
		'S_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file),
		'L_SUBMIT' => $lang['faq_retrieve'],
		'L_TITLE' => $lang['faq_editor'],
		'L_EXPLAIN' => $lang['faq_editor_explain']
		)
	);

	$template->pparse('body');
	include('./page_footer_admin.' . PHP_EXT);
	exit;
}

// get the language we want to edit
$language = isset($_GET['language']) ? $_GET['language'] : $_POST['language'];
$language = phpbb_ltrim(basename(phpbb_rtrim($language)), "'");

if(!is_writable(IP_ROOT_PATH . 'language/lang_' . $language . '/lang_' . $file . '.' . PHP_EXT))
{
	message_die(GENERAL_ERROR, $lang['faq_write_file_explain'], $lang['faq_write_file'], __LINE__, __FILE__);
}

// the FAQ which will generate our $faq array
include(IP_ROOT_PATH . 'language/lang_' . $language . '/lang_' . $file . '.' . PHP_EXT);

// change into our array
list($blocks, $quests) = faq_to_array($faq);

// if we have a mode set this means we have to do something
if(isset($_GET['mode']) || isset($_POST['mode']))
{
	// fetch the mode and two commonly past variables
	$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$block_no = intval(isset($_GET['block']) ? $_GET['block'] : (isset($_POST['block']) ? $_POST['block'] : 0));
	$quest_no = intval(isset($_GET['quest']) ? $_GET['quest'] : (isset($_POST['quest']) ? $_POST['quest'] : 0));

	switch($mode)
	{
		// create a new block as a result of typing the block name and pressing submit
		case 'block_new':
			$blocks[] = isset($_GET['block_title']) ? $_GET['block_title'] : $_POST['block_title'];
			$quests[] = array();
			break;

		// result of pressing the delete link next to a block
		case 'block_del':
			$template->set_filenames(array('confirm' => ADM_TPL . 'confirm_body.tpl'));

			$s_hidden_fields = '<input type="hidden" name="mode" value="block_del_confirm" />';
			$s_hidden_fields .= '<input type="hidden" name="block" value="'.$block_no.'" />';

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $lang['Confirm'],
				'MESSAGE_TEXT' => $lang['faq_block_delete'],

				'L_YES' => $lang['Yes'],
				'L_NO' => $lang['No'],

				'S_CONFIRM_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language),
				'S_HIDDEN_FIELDS' => $s_hidden_fields
				)
			);

			$template->pparse('confirm');
			include('./page_footer_admin.' . PHP_EXT);

			exit;

		// result of pressing YES on the block delete confirmation
		case 'block_del_confirm':
			if(isset($_GET['confirm']) || isset($_POST['confirm']))
			{
				for($i = $block_no; $i < sizeof($blocks); $i++)
				{
					$blocks[$i] = $blocks[$i+1];
					$quests[$i] = $quests[$i+1];
				}

				$last_id = sizeof($blocks) - 1;

				unset($blocks[$last_id]);
				unset($quests[$last_id]);
			}

			break;

		// generate the edit screen as a result of pressing the edit link
		case 'block_edit':
			$template->set_filenames(array('body' => ADM_TPL . 'faq_block_body.tpl'));

			$template->assign_vars(array(
				'L_TITLE' => $lang['faq_block_rename'],
				'L_EXPLAIN' => $lang['faq_block_rename_explain'],
				'L_SUBMIT' => $lang['Submit'],
				'L_BLOCK_NAME' => $lang['faq_block_name'],

				'BLOCK_TITLE' => $blocks[$block_no],

				'S_HIDDEN_FIELDS' => '<input type="hidden" name="mode" value="block_do_edit" /><input type="hidden" name="block" value="' . $block_no . '" />',
				'S_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language)
				)
			);

			$template->pparse('body');
			include('./page_footer_admin.' . PHP_EXT);

			exit;

		// actually do the edit after pressing submit on the block edit screen
		case 'block_do_edit':
			$blocks[$block_no] = isset($_GET['block_title']) ? $_GET['block_title'] : $_POST['block_title'];
			break;

		// re-arrange the blocks after someone presses an UP link
		case 'block_up':
			if($block_no != 0)
			{
				$block_temp = $blocks[$block_no - 1];
				$quest_temp = $quests[$block_no - 1];

				$blocks[$block_no - 1] = $blocks[$block_no];
				$quests[$block_no - 1] = $quests[$block_no];

				$blocks[$block_no] = $block_temp;
				$quests[$block_no] = $quest_temp;

				unset($block_temp);
				unset($quest_temp);
			}

			break;

		// re-arrange the blocks after someone presses an DOWN link
		case 'block_dn':
			if($block_no != (sizeof($blocks) - 1))
			{
				$block_temp = $blocks[$block_no + 1];
				$quest_temp = $quests[$block_no + 1];

				$blocks[$block_no + 1] = $blocks[$block_no];
				$quests[$block_no + 1] = $quests[$block_no];

				$blocks[$block_no] = $block_temp;
				$quests[$block_no] = $quest_temp;

				unset($block_temp);
				unset($quest_temp);
			}

			break;

		// create a new question as a result of typing a question on the main page
		case 'quest_new':
			$template->set_filenames(array('body' => ADM_TPL . 'faq_quest_body.tpl'));

			$s_block_list = '';
			$s_selected_block = intval(isset($_GET['block']) ? $_GET['block'] : $_POST['block']);

			for($i = 0; $i < sizeof($blocks); $i++)
			{
				$is_selected = ($s_selected_block == $i) ? ' selected' : '';
				$s_block_list .= '<option value="' . $i . '"' . $is_selected . '>' . $blocks[$i] . '</option>';
			}

			$template->assign_vars(array(
				'L_TITLE' => $lang['faq_quest_create'],
				'L_EXPLAIN' => $lang['faq_quest_create_explain'],
				'L_BLOCK' => $lang['faq_block'],
				'L_QUESTION' => $lang['faq_quest'],
				'L_ANSWER' => $lang['faq_answer'],
				'L_SUBMIT' => $lang['Submit'],

				'QUESTION' => htmlspecialchars(stripslashes(isset($_GET['quest_title']) ? $_GET['quest_title'] : $_POST['quest_title'])),
				'ANSWER' => '',

				'S_BLOCK_LIST' => $s_block_list,
				'S_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language),
				'S_HIDDEN_FIELDS' => '<input name="mode" type="hidden" value="quest_create">'
				)
			);

			$template->pparse('body');
			include('./page_footer_admin.' . PHP_EXT);
			exit;

		// actually create the question when the user submits the new question form
		case 'quest_create':
			$question = isset($_GET['quest_title']) ? $_GET['quest_title'] : $_POST['quest_title'];
			$answer = str_replace("\n", '<br />', isset($_GET['answer']) ? $_GET['answer'] : $_POST['answer']);

			$new_id = sizeof($quests[$block_no]);

			$quests[$block_no][$new_id][Q] = stripslashes($question);
			$quests[$block_no][$new_id][A] = stripslashes($answer);
			break;

		// present the question edit screen
		case 'quest_edit':
			$template->set_filenames(array('body' => ADM_TPL . 'faq_quest_body.tpl'));

			$s_block_list = '';
			$s_selected_block = intval(isset($_GET['block']) ? $_GET['block'] : $_POST['block']);

			for($i = 0; $i < sizeof($blocks); $i++)
			{
				$is_selected = ($s_selected_block == $i) ? ' selected' : '';
				$s_block_list .= '<option value="' . $i . '"' . $is_selected . '>' . $blocks[$i] . '</option>';
			}

			$template->assign_vars(array(
				'L_TITLE' => $lang['faq_quest_edit'],
				'L_EXPLAIN' => $lang['faq_quest_edit_explain'],
				'L_BLOCK' => $lang['faq_block'],
				'L_QUESTION' => $lang['faq_quest'],
				'L_ANSWER' => $lang['faq_answer'],
				'L_SUBMIT' => $lang['Submit'],

				'QUESTION' => htmlspecialchars($quests[$block_no][$quest_no][Q]),
				'ANSWER' => htmlspecialchars(str_replace('<br />', "\n", $quests[$block_no][$quest_no][A])),

				'S_BLOCK_LIST' => $s_block_list,
				'S_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language),
				'S_HIDDEN_FIELDS' => '<input name="quest" type="hidden" value="' . $quest_no . '"><input name="old_block" type="hidden" value="' . $block_no . '"><input name="mode" type="hidden" value="quest_do_edit">'
				)
			);

			$template->pparse('body');
			include('./page_footer_admin.' . PHP_EXT);
			exit;

		case 'quest_do_edit':
			$old_block_no = intval(isset($_GET['old_block']) ? $_GET['old_block'] : $_POST['old_block']);

			$question = stripslashes(isset($_GET['quest_title']) ? $_GET['quest_title'] : $_POST['quest_title']);
			$answer = str_replace("\n", '<br />', stripslashes(isset($_GET['answer']) ? $_GET['answer'] : $_POST['answer']));

			if($block_no == $old_block_no)
			{
				// standard edit where we don't change blocks

				$quests[$block_no][$quest_no][Q] = $question;
				$quests[$block_no][$quest_no][A] = $answer;
			}
			else
			{
				// edit where we move blocks

				for($i = $quest_no; $i < sizeof($quests[$old_block_no]); $i++)
				{
					$quests[$old_block_no][$i] = $quests[$old_block_no][$i+1];
				}

				unset($quests[$old_block_no][sizeof($quests[$old_block_no]) - 1]);

				$new_id = sizeof($quests[$block_no]);

				$quests[$block_no][$new_id][Q] = $question;
				$quests[$block_no][$new_id][A] = $answer;
			}
			break;

		// delete a question: confirm box
		case 'quest_del':
			$template->set_filenames(array('confirm' => ADM_TPL . 'confirm_body.tpl'));

			$s_hidden_fields = '<input type="hidden" name="mode" value="quest_del_confirm" />';
			$s_hidden_fields .= '<input type="hidden" name="block" value="' . $block_no . '" />';
			$s_hidden_fields .= '<input type="hidden" name="quest" value="' . $quest_no . '" />';

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $lang['Confirm'],
				'MESSAGE_TEXT' => $lang['faq_quest_delete'],

				'L_YES' => $lang['Yes'],
				'L_NO' => $lang['No'],

				'S_CONFIRM_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language),
				'S_HIDDEN_FIELDS' => $s_hidden_fields
				)
			);

			$template->pparse('confirm');
			include('./page_footer_admin.' . PHP_EXT);
			exit;

		// delete is confirmed or rejected
		case 'quest_del_confirm':
			if(isset($_GET['confirm']) || isset($_POST['confirm']))
			{
				for($i = $quest_no; $i < sizeof($quests[$block_no]); $i++)
				{
					$quests[$block_no][$i] = $quests[$block_no][$i+1];
				}

				unset($quests[$block_no][sizeof($quests[$block_no]) - 1]);
			}
			break;

		// move a question upwards
		case 'quest_up':
			if($quest_no != 0)
			{
				$temp = $quests[$block_no][$quest_no - 1];
				$quests[$block_no][$quest_no - 1] = $quests[$block_no][$quest_no];
				$quests[$block_no][$quest_no] = $temp;
				unset($temp);
			}
			break;

		// move a question downwards
		case 'quest_dn':
			if($quest_no != (sizeof($quests[$block_no]) - 1))
			{
				$temp = $quests[$block_no][$quest_no + 1];
				$quests[$block_no][$quest_no + 1] = $quests[$block_no][$quest_no];
				$quests[$block_no][$quest_no] = $temp;
				unset($temp);
			}
			break;
	}

	// write these changes back to the FAQ file

	$fp = fopen(IP_ROOT_PATH . 'language/lang_' . $language . '/lang_' . $file . '.' . PHP_EXT, 'w');

	if($fp)
	{
			fwrite($fp, $faq_header);
			$lines = array_to_faq($blocks, $quests);
			for($i = 0; $i < sizeof($lines); $i++)
			{
				fwrite($fp, $lines[$i]);
			}
			fwrite($fp, $faq_footer);
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['faq_write_file_explain'], $lang['faq_write_file'], __LINE__, __FILE__);
	}
}

// if we've got this far without exiting we just dump the default page

$template->set_filenames(array('body' => ADM_TPL . 'faq_editor_body.tpl'));

$template->assign_vars(array(
	'L_TITLE' => $lang['faq_editor'],
	'L_EXPLAIN' => $lang['faq_editor_explain'],

	'S_ACTION' => append_sid('admin_faq_editor.' . PHP_EXT . '?file=' . $file . '&language=' . $language),

	'L_ADD_BLOCK' => $lang['faq_block_add'],
	'L_ADD_QUESTION' => $lang['faq_quest_add'],

	'L_EDIT' => $lang['Edit'],
	'L_DELETE' => $lang['Delete'],
	'L_MOVE_UP' => $lang['Move_up'],
	'L_MOVE_DOWN' => $lang['Move_down'],

	'L_NO_QUESTIONS' => $lang['faq_no_quests'],
	'L_NO_BLOCKS' => $lang['faq_no_blocks']
	)
);

$k = 0;

if(sizeof($blocks) > 0)
{
	for($i = 0; $i < sizeof($blocks); $i++)
	{
		$template->assign_block_vars('blockrow', array(
			'BLOCK_TITLE' => $blocks[$i],
			'BLOCK_NUMBER' => $i,
			'BLOCK_ANCHOR' => $anchor_code,

			'U_BLOCK_EDIT' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=block_edit&block=' . $i . '&file=' . $file . '&language=' . $language),
			'U_BLOCK_MOVE_UP' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=block_up&block=' . $i . '&file=' . $file . '&language=' . $language),
			'U_BLOCK_MOVE_DOWN' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=block_dn&block=' . $i . '&file=' . $file . '&language=' . $language),
			'U_BLOCK_DELETE' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=block_del&block=' . $i . '&file=' . $file . '&language=' . $language)
			)
		);

		if(sizeof($quests[$i]) > 0)
		{
			for($j = 0; $j < sizeof($quests[$i]); $j++)
			{
				$template->assign_block_vars('blockrow.questrow', array(
					'QUEST_TITLE' => $quests[$i][$j][Q],
					'U_QUEST' => append_sid(IP_ROOT_PATH . 'faq.' . PHP_EXT . '?mode=' . $file) . '#f' . $k,

					'U_QUEST_EDIT' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=quest_edit&block=' . $i . '&quest=' . $j . '&file=' . $file . '&language=' . $language),
					'U_QUEST_MOVE_UP' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=quest_up&block=' . $i . '&quest=' . $j . '&file=' . $file . '&language=' . $language),
					'U_QUEST_MOVE_DOWN' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=quest_dn&block=' . $i . '&quest=' . $j . '&file=' . $file . '&language=' . $language),
					'U_QUEST_DELETE' => append_sid('admin_faq_editor.' . PHP_EXT . '?mode=quest_del&block=' . $i . '&quest=' . $j . '&file=' . $file . '&language=' . $language)
					)
				);

				$k++;
			}
		}
		else
		{
			$template->assign_block_vars('blockrow.no_questions', array());
		}
	}
}
else
{
	$template->assign_block_vars('no_blocks', array());
}

$template->pparse('body');

include('./page_footer_admin.' . PHP_EXT);

?>