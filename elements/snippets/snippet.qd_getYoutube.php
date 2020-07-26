<?php

//define("QD_GYT_DEBUG", "QD_GYT_DEBUG");

define("HTML_KEY", "html");
define("YT_HASH_KEY", "yt_hash");
define("UPDATE_DATE_KEY", "last_update");

// Uncomment if you want to regenerate the db model
//----------------------------------------------------------
// include_once MODX_CORE_PATH . 'components/qd_getyoutube/_build/build.schema.php';

// qd_getYoutube functions
//----------------------------------------------------------
function generateNewYtOutput($mdx, $db_obj, $channel_key)
{
    $output = "";

    // For testing when we use all YT API Key limit
    if (defined('QD_GYT_DEBUG')) {
        $output = $mdx->runSnippet('getResources', array(
            'showHidden' => '1',
            'depth' => '0',
            'parents' => '23',
            'limit' => '1',
            'includeContent' => '1',
            'tpl' => 'c_gr_tpl',
            'sortdir' => 'ASC',
            'sortby' => 'menuindex',
        ));
    } else {
        $output = $mdx->runSnippet('getYoutube', array(
            'mode' => 'channel',
            'channel' => $channel_key,
            'tpl' => 'c_yt_videoThumbTpl',
            'limit' => '4',
        ));
    }

    if (empty($output)) {
        // TODO_QD
        // $modx->log(modX::LOG_LEVEL_ERROR,'Empty Yt output!'); die();
    }

    return $output;
}

// Load model
$model_path = $modx->getOption('qd_getyoutube.core_path', null, $modx->getOption('core_path')) . 'model/';
if (!$modx->addPackage('qd_getyoutube', $model_path)) {
    die('Can\'t load package, try again later.');
}

// Create the database table
$modx->getManager()->createObjectContainer('qd_getYoutube');

// Prepare service
$testPlug = $modx->getService('qd_getyoutube', 'qd_getYoutube', $modx->getOption('qd_getyoutube.core_path', null, $modx->getOption('core_path')) . 'model/qd_getyoutube/', $scriptProperties);
if (!($testPlug instanceof qd_getYoutube)) {
    return '';
}

// qd_getYoutube external variables init
$channel_key = !empty($channel_key) ? $channel_key : '';

// Prepare query
// $query = $modx->newQuery('qd_getYoutube');
// $query->where(array(YT_HASH_KEY => $channel_key));
// $query->sortby('qd_getYoutube.createdon', 'ASC');

// $cur_date = date('Y-m-d H:i:s', time());
// foreach ($modx->getIterator('qd_getYoutube', $query) as $it) {
//     $c_date = $it->get(UPDATE_DATE_KEY);
//     $twelve_h_from_c_date = date('Y-m-d H:i:s', strtotime("$c_date + 12 hours"));

//     echo "kekeke";
//     if ($twelve_h_from_c_date > $cur_date) {
//         // If tweleve hours didn't pass, just return html from db
//         return $it->get(HTML_KEY);
//     } else {
//         // If twelve hours passed from the last update, we need to regenerate yt output
//         $yt_output = generateNewYtOutput($modx, $it, $channel_key);

//         // Update db
//         $it->set(HTML_KEY, $yt_output);
//         $it->set(UPDATE_DATE_KEY, $cur_date);
//         $it->save();

//         return $yt_output;
//     }
// }

$yt_obj = $modx->getObject('qd_getYoutube', array(YT_HASH_KEY => $channel_key));
if ($yt_obj)
{
    // Db object found!
    $cur_date = date('Y-m-d H:i:s', time());
    $c_date = $yt_obj->get(UPDATE_DATE_KEY);
    $twelve_h_from_c_date = date('Y-m-d H:i:s', strtotime("$c_date + 12 hours"));

    if ($twelve_h_from_c_date > $cur_date) {
        // If tweleve hours didn't pass, just return html from db
        return $yt_obj->get(HTML_KEY);
    } else {
        // If twelve hours passed from the last update, we need to regenerate yt output
        $yt_output = generateNewYtOutput($modx, $yt_obj, $channel_key);

        // Update db
        $yt_obj->set(HTML_KEY, $yt_output);
        $yt_obj->set(UPDATE_DATE_KEY, $cur_date);
        $yt_obj->save();

        return $yt_output;
    }
}
else
{
    // If we get here that means that there is no entry in db for $channel_key, so we need to create a new one
    $new_entry = $modx->newObject('qd_getYoutube', array(
        HTML_KEY => $yt_output,
        UPDATE_DATE_KEY => date('Y-m-d H:i:s', time()),
    ));

    $yt_output = generateNewYtOutput($modx, $it, $channel_key);
    $new_entry->set(YT_HASH_KEY, $channel_key);
    $new_entry->save();

    return $yt_output;
}

return "";