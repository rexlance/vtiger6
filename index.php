<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (!isset($_SESSION)) session_start();

//Overrides GetRelatedList : used to get related query
//TODO : Eliminate below hacking solution
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

// Hook: Maestrano
// Load Maestrano
include_once 'maestrano/init.php';
if(Maestrano::sso()->isSsoEnabled()) {
  $mnoSession = new Maestrano_Sso_Session($_SESSION);
  // Check session validity and trigger SSO if not
  if (!$mnoSession->isValid()) {
    header('Location: ' . Maestrano::sso()->getInitPath());
    exit;
  }
}

$webUI = new Vtiger_WebUI();
$webUI->process(new Vtiger_Request($_REQUEST, $_REQUEST));
