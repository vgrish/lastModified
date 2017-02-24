<?php

$settings = array();

$tmp = array(
    'active'            => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'lastmodified_main',
    ),
    'check_type'        => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'lastmodified_main',
    ),
    'check_template'    => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'lastmodified_main',
    ),
    'skip_tags'      => array(
        'xtype' => 'textarea',
        'value' => 'pre,code,script',
        'area'  => 'lastmodified_main',
    ),
    'working_type'      => array(
        'xtype' => 'textfield',
        'value' => 'text/html',
        'area'  => 'lastmodified_main',
    ),
    'working_templates' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'lastmodified_main',
    ),
    'working_on_resource_save'      => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'lastmodified_main',
    ),

    //временные
/*
    'core_path' => array(
        'value' => '{base_path}lastmodified/core/components/lastmodified/',
        'xtype' => 'textfield',
        'area'  => 'lastmodified_temp',
    ),*/

    //временные

);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'lastmodified_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
