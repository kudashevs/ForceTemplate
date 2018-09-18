<?php
$settings = array();
$tmp = array(
    'rules' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => PKG_NAME_LOWER . '.main',
    ),
    'quick_create' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => PKG_NAME_LOWER . '.main',
    ),
    'parents_check' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => PKG_NAME_LOWER . '.main',
    ),
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => PKG_NAME_LOWER . '.' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;