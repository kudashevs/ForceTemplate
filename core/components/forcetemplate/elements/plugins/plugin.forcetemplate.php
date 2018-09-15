<?php
/**
 * MODx Revolution plugin which force template set for given parents
 *
 * @package forcetemplate
 * @var modX $modx MODX instance
 *
 */
if ($modx->event->name === 'OnDocFormRender') {

    $startId = $resource->get('parent');
    $rules = trim($modx->getOption('forcetemplate.rules'));

    if ($startId < 1 || empty($rules) || empty($scriptProperties['mode']) || $scriptProperties['mode'] !== 'new') { return ''; }

    $rules = preg_split('/(\|\||\|)/', $rules);
    $entity = [];

    foreach ($rules as $rule) {
        if (preg_match('/([^\:]+)\:([^\:]+)(?:\:([^\:]+))?$/', trim($rule), $match)) {
            $temp = [
                'parent' => intval($match[1]),
                'template' => trim($match[2]),
                'level' => (!empty($match[3]) && intval($match[3]) > 1) ? intval($match[3]) : 0,
            ];
            $entityKey = $temp['parent'];

            if ($modx->getoption('forcetemplate.parents_check') && array_key_exists($entityKey, $entity)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: parent ' . $entityKey . ' is doubled with template ' . $temp['template'] . '. Check rules!');
            }

            $entity[$entityKey] = $temp;
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: incorrect rule ' . $rule . ' in ' . $rules. '');
        }
    }

    if (empty($entity)) { return ''; }

    if(!empty($entity[$startId])) {
        $parentId = $startId;
    } else {
        // we use max possible level value from all given rules, because we don't know
        // if inheritance chain of start document contains any parents we are searching for
        $maxLevel = max(array_map(function($arr) { return $arr['level']; }, $entity));
        $parentIds = $modx->getParentIds($startId, $maxLevel, ['context' => 'web']);

        if (empty($parentIds)) { return ''; }

        foreach ($parentIds as $parent) {
            if(array_key_exists($parent, $entity)) $parentId = $parent;
        }

        // in checking level operation we sum value founded by array_search() with 2 because:
        // - we start from parent not document level (+1 level)
        // - $parentsIds array indexing start from 0 (+1 level)
        if (empty($parentId) || ($entity[$parentId]['level'] < (int)array_search($parentId, $parentIds) + 2)) { return ''; }
    }

    $givenTemplate = $entity[$parentId]['template'];

    if (!is_numeric($givenTemplate)) {
        $clause = ['templatename' => $givenTemplate];
    } else {
        $clause = ['id' => $givenTemplate];
    }

    $template = $modx->getObject('modTemplate', $clause);

    if (empty($template)) {
        $modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: template with input value ' . $givenTemplate . ' not exists. Parent template used!');

        $parent = $modx->getObject('modResource', $parentId);
        $parentTemplateId = $parent->get('template');
    }

    $templateId = isset($parentTemplateId) ? $parentTemplateId : $template->get('id');

    $modx->controller->setProperty('template', $templateId);
    unset($startId, $parentId, $parentIds, $maxLevel, $templateId, $parentTemplateId, $clause, $givenTemplate, $entity);

    return '';
}