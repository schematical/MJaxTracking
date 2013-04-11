<?php
class MJaxTrackingFormBase extends MJaxExtensionBase{
	
	protected $objEvent = null;
	protected $arrTrackingData = array();
	public function InitControl($objForm){
		$this->objControl = $objForm;
		
		$objForm->InitTrackingEvent(MLCEvent::PAGE_LOAD);
		
		$this->InitGoogleAnalytics();
		
		/*if(array_key_exists('e', $_GET)){
			MLCApplication::InitPackage('MJaxBootstrap');
			MLCApplication::InitPackage('MJaxCharts');
			foreach($this->objControl->Controls as $intIndex => $ctlControl){
				$lnkViewStats = new MJaxLinkButton($ctlControl);
				$lnkViewStats->AddCssClass('btn');
				$lnkViewStats->AddAction($this->objControl, 'lnkViewStats_click');
				$lnkViewStats->Text = 'View Stats';
				$ctlControl->Popover(array(
						'content'=>$lnkViewStats,
						'trigger'=>'click'
						//'container' => 'body'
				));
			}
		}*/
	}
	public function lnkViewStats_click($strFormId, $strControlId, $strActionParameter){
		$objControl = $this->objControl->Controls[$strControlId];
		$strSql = sprintf(
			'SELECT DAY(creDate) as "date", count(name) as "count" FROM TrackingEvent WHERE (name = "%s" OR name = "%s") AND creDate < "%s" AND creDate > "%s" AND app = "%s" AND form = "%s"  AND controlId = "%s" GROUP BY DAY(creDate), name',
			MLCEvent::AB_TEST_DISP,
			MLCEvent::EVENT_TRIGGER,
			MLCDateTime::Now(),
			date("Y") . '-' .(date("m")-1) . '-' . date("d"),
			MLC_APPLICATION_NAME,
			$this->objControl->FormId,
			$ctlControl->ControlId
		);
		//die($strSql);
		$resData = MLCDBDriver::Query($strSql, 'DB_0');
		$arrFields = mysql_fetch_assoc($resData);
	
		if(
			(count($arrFields) > 0) &&
			($arrFields !== false)
		){
			$arrNewFields = array();
			$arrNewFields[] = array('date', 'count');
			$arrData = array();
			foreach($arrFields as $strKey => $strVal){
				
				switch($strKey){
					case('date'):
						$arrData[] = $strVal;
					
					break;
					case('count'):
						$arrData[] = (int)$strVal;
						
					break;
				}
			}
			$arrNewFields[] = $arrData;
			
			$pnlChart = new MJaxLineChartPanel($this->objControl);
			$pnlChart->SetData($arrNewFields);
			//$pnlChart->SetOptions($arrFields);
			$this->objControl->Form->Alert($pnlChart);
	
		}
	}
	
	public function PopulateCtlsByQS(){
		//_dp($this->arrControls);
		foreach($this->objControl->ChildControls as $strCtlId => $ctlControl){
			foreach($_REQUEST as $strKey => $strVal){
				if($strCtlId == $strKey){
					$ctlControl->Text = $strVal;
				}
			}
		}
	}
	public function TriggerControlEvent($strControlId, $strEvent){
		$this->InitTrackingEvent(MLCEvent::EVENT_TRIGGER);
		$this->arrTrackingData['EVENT'] = $strEvent;
		$this->arrTrackingData['CONTROL_ID'] = $strControlId;
		$this->objEvent->ControlId = $strControlId;
//var_dump($this->objEvent);
		return $this->objControl->TriggerControlEvent($strControlId, $strEvent);
	}
	public function InitTrackingEvent($strEvent){
		
		$this->arrTrackingData = array();
		$this->objEvent = MLCEventTrackingDriver::Track(
			$strEvent,
			null, 
			false
		);
		$this->objEvent->App = MLC_APPLICATION_NAME;
		$this->objEvent->Form = $this->objControl->FormId;
		
		$this->arrTrackingData['URL'] = $_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI'];
		$this->arrTrackingData['FORM'] = get_class($this->objControl);

	}
	public function AddTrackingData($strName, $mixEventData){
		$this->arrTrackingData[$strName] = $mixEventData;
	}
	public function Form_Exit(){
		
		$this->objEvent->Value = serialize($this->arrTrackingData);
		$this->objEvent->Save();
	}
	public function TrackingEvent(){
		return $this->objEvent;
	}
	public function RunPreRender($objControl){
		$this->objControl = $objControl;
		foreach($this->objControl->Controls as $intIndex => $objChildControl){
			$strKey = $objChildControl->ChooseTextVariation();
			if(!is_null($strKey)){
				$objTrackingEvent = MLCEventTrackingDriver::Track(
					MLCEvent::AB_TEST_DISP,
					null, 
					false
				);
				$objTrackingEvent->App = MLC_APPLICATION_NAME;
				$objTrackingEvent->Form = $this->objControl->FormId;
				$objTrackingEvent->ControlId = $objChildControl->ControlId;
				$objTrackingEvent->Name = MLCEvent::AB_TEST_DISP;
				$objTrackingEvent->Text = $strKey;
				$objTrackingEvent->Save();
			}
			
		}
		
		if($this->blnUseGoogleAnaltyics){
			//TODO: Seperate this out
			foreach($this->objControl->Controls as $intIndex => $objChildControl){
				if(
					($objChildControl instanceof MJaxLinkButton) ||
					($objChildControl instanceof MJaxButton) 
				){
					//$objChildControl->AddAction(new MJaxClickEvent(), new MJaxGoogleAnalyticsAction());
				}
			}
		}
	}
	
	
	//TODO: Seperate out
	public $blnUseGoogleAnaltyics = false;
	public function InitGoogleAnalytics(){
		$this->blnUseGoogleAnaltyics = true;
		
	}
	
}
