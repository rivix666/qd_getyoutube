<?php
require_once dirname(__FILE__).'/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$sources = array(
'model' => $modx->getOption('qd_getyoutube.core_path').'model/',
'schema_file' => $modx->getOption('qd_getyoutube.core_path').'model/schema/qd_getyoutube.mysql.schema.xml'
);
$manager= $modx->getManager();
$generator= $manager->getGenerator();

if (!is_dir($sources['model'])) { $modx->log(modX::LOG_LEVEL_ERROR,'Model directory not found!'); die(); }
if (!file_exists($sources['schema_file'])) { $modx->log(modX::LOG_LEVEL_ERROR,'Schema file not found!'); die(); }
$generator->parseSchema($sources['schema_file'], $sources['model']);

$modx->addPackage('qd_getyoutube', $sources['model']); // add package to make all models available
$manager->createObjectContainer('qd_getYoutube'); // created the database table

$modx->log(modX::LOG_LEVEL_INFO, 'Done!');