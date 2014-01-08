<?php
// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_cloudflaredev', $params->get('layout', 'default'));
