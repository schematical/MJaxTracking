<?php
if(!defined('__MLC_TRACKING__')){
	MLCApplication::InitPackage('MLCTracking');
}
define('__MJAX_TRACKING_MANAGER__', dirname(__FILE__));
define('__MJAX_TRACKING_MANAGER_CORE__', __MJAX_TRACKING_MANAGER__ . '/_core');
define('__MJAX_TRACKING_MANAGER_CORE_CTL__', __MJAX_TRACKING_MANAGER__ . '/ctl');
define('__MJAX_TRACKING_MANAGER_CORE_MODEL__', __MJAX_TRACKING_MANAGER__ . '/model');
define('__MJAX_TRACKING_MANAGER_CORE_VIEW__', __MJAX_TRACKING_MANAGER__ . '/view');

MLCApplicationBase::$arrClassFiles['MJaxTrackingControlBase'] = __MJAX_TRACKING_MANAGER_CORE__ . '/MJaxTrackingControlBase.class.php';
MLCApplicationBase::$arrClassFiles['MJaxTrackingFormBase'] = __MJAX_TRACKING_MANAGER_CORE__ . '/MJaxTrackingFormBase.class.php';

MJaxControlBase::AddExtension(new MJaxTrackingControlBase());
MJaxFormBase::AddExtension(new MJaxTrackingFormBase());

//require_once(__MJAX_BS_MANAGER_CORE__ . '/_enum.inc.php');
require_once(__MJAX_TRACKING_MANAGER_CORE__ . '/_actions.inc.php');


