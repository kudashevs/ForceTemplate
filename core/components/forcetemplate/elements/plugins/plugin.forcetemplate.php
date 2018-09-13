<?php
/**
 * MODx Revolution plugin which force template set for given parents
 *
 * @package forcetemplate
 * @var modX $modx MODX instance
 *
 */
if ($modx->event->name === 'OnDocFormRender') {

    $parentId = $resource->get('parent');
    $rules = trim($modx->getOption('forcetemplate.rules'));

    if ($parentId < 1 || empty($rules) || empty($scriptProperties['mode']) || $scriptProperties['mode'] !== 'new') { return; }

    $rules = preg_split('/(\|\||\|)/', $rules);
    $pairs = [];

    foreach ($rules as $rule) {
        if (strpos($rule, ':')) {
            list($key, $val) = array_map('trim', explode(':', $rule));
            $key = (int)$key;

            if ($modx->getOption('forcetemplate.parents_check') && array_key_exists($key, $pairs)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: parent ' . $key . ' is doubled with template ' . $val . '. Check rules!');
            }

            $pairs[$key] = $val;
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: incorrect rule ' . $rule . '');
        }
    }

    if (empty($pairs) || !array_key_exists($parentId, $pairs)) { return; }

    $templateId = $pairs[$parentId];

    if ($modx->getOption('forcetemplate.template_check')) {
        $template = $modx->getObject('modTemplate', ['id' => $templateId]);

        if (!$template) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: template with id ' . $templateId . ' not exists. Check rules!');

            $parent = $modx->getObject('modResource', $parentId);
            $templateId = $parent->get('template');
        }
    }

    $modx->controller->setProperty('template', $templateId);
    unset($parentId, $templateId, $pairs);

    return '';
}