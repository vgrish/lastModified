<?php

/** @var array $scriptProperties */
/** @var lastModified $lastModified */
$corePath = $modx->getOption('lastmodified_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/lastmodified/');
/** @var lastModified $lastModified */
$lastModified = $modx->getService(
    'lastmodified',
    'lastModified',
    $corePath . 'model/lastmodified/',
    array(
        'core_path' => $corePath
    )
);
if (!$lastModified) {
    return false;
}

$className = 'lastModified' . $modx->event->name;
$modx->loadClass('lastModifiedPlugin', $lastModified->getOption('modelPath') . 'lastmodified/systems/', true, true);
$modx->loadClass($className, $lastModified->getOption('modelPath') . 'lastmodified/systems/', true, true);

if (class_exists($className)) {
    /** @var lastModifiedPlugin $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}

return;