<?php

class ForceTemplate
{
    /** @var  modX $modx */
    protected $modx;

    /** @var int $startId */
    protected $startId;

    /** @var string $rules */
    protected $rules;

    public function __construct(modX &$modx, $start, $rules)
    {
        $this->modx = $modx;
        $this->startId = intval($start);
        $this->rules = trim($rules);
    }

    /**
     * Calculate parent template id
     *
     * @return int
     */
    function calculateParentTemplateId()
    {
        $entity = $this->parseRules();
        if (empty($entity)) { return ''; }

        if(!empty($entity[$this->startId])) {
            $parentId = $this->startId;
        } else {
            // we use max possible level value from all given rules, because we don't know
            // if inheritance chain of start document contains any parents we are searching for
            $maxLevel = max(array_map(function($arr) { return $arr['level']; }, $entity));
            $parentIds = $this->modx->getParentIds($this->startId, $maxLevel, ['context' => 'web']);

            if (empty($parentIds)) { return ''; }

            foreach ($parentIds as $parent) {
                if(array_key_exists($parent, $entity)) $parentId = $parent;
            }

            // in checking level operation we sum founded array_search value with 2 because:
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

        $template = $this->modx->getObject('modTemplate', $clause);

        if (empty($template)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: template with input value ' . $givenTemplate . ' not exists. Parent template used!');

            $parent = $this->modx->getObject('modResource', $parentId);
            $parentTemplateId = $parent->get('template');
        }

        $templateId = !empty($parentTemplateId) ? $parentTemplateId : $template->get('id');

        return $templateId;
    }

    /**
     * Parse rules and return rule array
     *
     * @return array
     */
    protected function parseRules()
    {
        $rules = preg_split('/(\|\||\|)/', $this->rules);
        $entity = [];

        foreach ($rules as $rule) {
            if (preg_match('/([^\:]+)\:([^\:]+)(?:\:([^\:]+))?$/', trim($rule), $match)) {
                $temp = [
                    'parent' => intval($match[1]),
                    'template' => trim($match[2]),
                    'level' => (!empty($match[3]) && intval($match[3]) > 1) ? intval($match[3]) : 0,
                ];
                $entityKey = $temp['parent'];

                if ($this->modx->getoption('forcetemplate.parents_check') && array_key_exists($entityKey, $entity)) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: parent ' . $entityKey . ' is doubled with template ' . $temp['template'] . '. Check rules!');
                }

                $entity[$entityKey] = $temp;
            } else {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: incorrect rule ' . $rule . ' in ' . $rules. '');
            }
        }

        return $entity;
    }
}