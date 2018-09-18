<?php
/**
 * Connector
 *
 * @package forcetemplate
 *
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('forcetemplate.core_path', [], $modx->getOption('core_path').'components/forcetemplate/');

$path = $modx->getOption('forcetemplate.processors_path', array(), $modx->getOption('core_path').'components/forcetemplate/processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));