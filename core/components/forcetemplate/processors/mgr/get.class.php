<?php
/**
 * Processor get parent id
 *
 * @package forcetemplate
 *
 */
class getForcetemplateParentListProcessor extends modProcessor
{
    public function process()
    {
        if (!$this->modx->getoption('forcetemplate.quick_create')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'ForceTemplate: activate forcetemplate.quick_create option to use Quick Create menu.');
            return $this->prepareResponse(true, 0);
        }

        $data = json_decode($_POST['data']);
        if (empty($data)) { return $this->prepareResponse(true, 0); }

        $startId = $data->parent;
        $rules = trim($this->modx->getOption('forcetemplate.rules'));

        if ($startId < 1 || empty($rules)) { return $this->prepareResponse(true, 0); }

        require_once MODX_CORE_PATH . 'components/forcetemplate/forcetemplate.class.php';

        $forceTemplate = new ForceTemplate($this->modx, $startId, $rules);
        $templateId = $forceTemplate->calculateParentTemplateId();

        if (empty($templateId)) { return $this->prepareResponse(true, 0); }

        $result = [
            'template' => $templateId,
        ];

        return $this->prepareResponse(true, 1, $result);
    }

    protected function prepareResponse($status, $count, $object = null)
    {
        return json_encode([
            'success' => boolval($status),
            'message' => '',
            'count'   => intval($count),
            'object'  => $object,
        ]);
    }
}

return 'getForcetemplateParentListProcessor';