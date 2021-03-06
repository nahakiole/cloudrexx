<?php
global $sessionObj, $_CONFIG, $_CORELANG, $objUser, $objDatabase;

if (!isset($objUser) || !isset($objDatabase) || !isset($license)) {
    require_once dirname(dirname(dirname(__FILE__))).'/core/Core/init.php';
    $cx = init('minimal');
    // In mode 'minimal' we have to manually register event listeners.
    // The listener registerYamlSettingEventListener is used to update the
    // settings.php file.
    \Cx\Core\Config\Controller\ComponentController::registerYamlSettingEventListener();
}

// Init user
if (empty($sessionObj)) $sessionObj = \cmsSession::getInstance();
if (!isset($objUser)) {
    $objUser = $cx->getUser()->objUser;
}
$objUser->login();

if (!isset($objDatabase)) {
    $objDatabase = $cx->getDb()->getAdoDb();
}

// update license, return "false" if no connection to license server could be established
if (!isset($license)) {
    $license = $cx->getLicense();
}
$licenseCommunicator = \Cx\Core_Modules\License\LicenseCommunicator::getInstance($_CONFIG);
try {
    $licenseCommunicator->update(
        $license,
        $_CONFIG,
        (isset($_GET['force']) && $_GET['force'] == 'true'),
        false,
        $_CORELANG,
        (isset($_POST['response']) && $objUser->getAdminStatus() ? contrexx_input2raw($_POST['response']) : '')
    );
} catch (\Exception $e) {
    $license->check();
    if (!isset($_GET['nosave']) || $_GET['nosave'] != 'true') {
        $license->save($objDatabase);
    }
    if (!isset($_GET['silent']) || $_GET['silent'] != 'true') {
        echo "false";
    }
    return;
}
$license->check();
if (!isset($_GET['nosave']) || $_GET['nosave'] != 'true') {
    $license->save($objDatabase);
}

if (!$objUser->login(true)) {
    // do not use die() here, or installer will not show success page
    return;
}

if (isset($_GET['silent']) && $_GET['silent'] == 'true') {
    return true;
}

// show info
$message = $license->getMessage(false, \FWLanguage::getLanguageCodeById(LANG_ID), $_CORELANG);
echo json_encode(array(
    'status' => contrexx_raw2xhtml($license->getState()),
    'link' => contrexx_raw2xhtml($message->getLink()),
    'target' => contrexx_raw2xhtml($message->getLinkTarget()),
    'text' => contrexx_raw2xhtml($message->getText()),
    'class' => contrexx_raw2xhtml($message->getType()),
));
