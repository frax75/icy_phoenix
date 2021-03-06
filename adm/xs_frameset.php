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
* Vjacheslav Trushkin (http://www.stsoftware.biz)
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
$no_page_header = true;
require('pagestart.' . PHP_EXT);

define('IN_XS', true);
define('NO_XS_HEADER', true);
include_once('xs_include.' . PHP_EXT);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$get_data = array();
foreach($_GET as $var => $value)
{
	if(($var !== 'action') && ($var !== 'sid'))
	{
		$get_data[] = $var . '=' . urlencode(stripslashes($value));
	}
}

// check for style download command
if(isset($_POST['action']) && ($_POST['action'] === 'web'))
{
	$action = 'import';
	$get_data[] = 'get_remote=' . urlencode(stripslashes($_POST['source']));
	if(isset($_POST['return']))
	{
		$get_data[] = 'return=' . urlencode(stripslashes($_POST['return']));
	}
}

$get_data = PHP_EXT . (sizeof($get_data) ? ('?' . implode('&', $get_data)) : '');

$content_url = array(
	'config' => append_sid('xs_config.' . $get_data),
	'install' => append_sid('xs_install.' . $get_data),
	'uninstall' => append_sid('xs_uninstall.' . $get_data),
	'default' => append_sid('xs_styles.' . $get_data),
	'cache' => append_sid('xs_cache.' . $get_data),
	'import' => append_sid('xs_import.' . $get_data),
	'export' => append_sid('xs_export.' . $get_data),
	'clone' => append_sid('xs_clone.' . $get_data),
	'download' => append_sid('xs_download.' . $get_data),
	'edittpl' => append_sid('xs_edit.' . $get_data),
	'editdb' => append_sid('xs_edit_data.' . $get_data),
	'exportdb' => append_sid('xs_export_data.' . $get_data),
	'updates' => append_sid('xs_update.' . $get_data),
	'portal' => append_sid('xs_portal.' . $get_data),
	'style_config' => append_sid('xs_style_config.' . $get_data),
	);

if(isset($content_url[$action]))
{
	$content = $content_url[$action];
}
else
{
	$content = append_sid('xs_index.'.$get_data);
}

$template->set_filenames(array('body' => XS_TPL_PATH . 'frameset.tpl'));
$template->assign_vars(array(
	'FRAME_TOP' => append_sid('xs_frame_top.' . PHP_EXT),
	'FRAME_MAIN' => $content,
	)
);

$template->pparse('body');
xs_exit();

?>