<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

if (file_exists(dirname(__FILE__) . '/defines.php')) 
{
    include_once dirname(__FILE__) . '/defines.php';
}

if (!defined('_JDEFINES')) 
{
    define('JPATH_BASE', dirname(__FILE__) . '/../../..');
    require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';

$app = JFactory::getApplication('administrator');

$user = JFactory::getUser();
$allowed = false;
if($user->id) 
{
    $allowedGroups = array(7, 8);
    $groups = $user->getAuthorisedGroups();
    
    foreach($groups as $group) 
    {
        if(in_array($group, $allowedGroups)) $allowed = true;
    }
}

if(!$allowed) die('Not Authorised, Please log in as an administrator');

jimport('joomla.application.module.helper'); 
jimport('joomla.html.parameter');
$module = &JModuleHelper::getModule('mod_cloudflaredev'); 
$params = new JParameter($module->params);

$task = JRequest::getVar('task', '');

switch($task) 
{
  case 'status':
    $options = array(
      'z' => $params->get('site', '')
    ); 
    $result = curlCall('zone_settings', $options, $params);
    header('Content-Type: application/json');
    echo $result;
  break;

  case 'change':
    $mode = JRequest::getVar('mode', '0');
    $options = array(
      'v' => $mode,
      'z' => $params->get('site', '')
    ); 
    $result = curlCall('devmode', $options, $params);
    header('Content-Type: application/json');
    echo $result;
  break;
}

function curlCall($action, $options = array(), $params)
{
  $token = $params->get('token', '');
  $email = $params->get('email', '');
  $url = 'https://www.cloudflare.com/api_json.html';

  $data = array(
    'a' => $action,
    'tkn' => $token,
    'email' => $email
  );

  $data = array_merge($data, $options);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}
