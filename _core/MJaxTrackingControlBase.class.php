<?php
class MJaxTrackingControlBase extends MJaxExtensionBase{
	public $strDisplayedVariation = null;
	public $arrTextVariation = array();
	public function InitControl($objControl){
		$this->objControl = $objControl;
		
	}
	public function AddTextVariation($strText, $strKey = null){
		if(!array_key_exists($this->objControl->ControlId, $this->arrTextVariation)){
			$this->arrTextVariation[$this->objControl->ControlId] = array();
		}
		if(!is_null($strKey)){
			$this->arrTextVariation[$this->objControl->ControlId][$strKey] = $strText;
		}else{
			$this->arrTextVariation[$this->objControl->ControlId][] = $strText;
		}
	}
	public function TriggerEvent($strEvent){
		$objTrackingEvent = $this->objControl->Form->TrackingEvent();
		$objTrackingEvent->Event = $strEvent;
		
		if(
			($this->objControl instanceof MJaxTextBox) ||
			($this->objControl instanceof MJaxLinkButton) ||
			($this->objControl instanceof MJaxButton) ||
			($this->objControl instanceof MJaxPanel) 
		){
			if(!is_null($this->strDisplayedVariation)){
				$objTrackingEvent->Text = $this->strDisplayedVariation;
			}
		}
		return  call_user_func_array(array($this->objControl, 'TriggerEvent'), array($strEvent)); 
	}
	public function ChooseTextVariation(){
		//$this->objControl = $this->objControl;
		if(
			!(
				(array_key_exists($this->objControl->ControlId, $this->arrTextVariation)) &&
				(count($this->arrTextVariation[$this->objControl->ControlId]) > 0)
			)
		){
			return null;
		}
		$arrKeys = array_keys($this->arrTextVariation[$this->objControl->ControlId]);
		$intKey = rand(0, count($arrKeys) -1);
		$this->strDisplayedVariation = $arrKeys[$intKey];
		$this->objControl->Text = $this->arrTextVariation[$this->objControl->ControlId][$this->strDisplayedVariation];
		return $arrKeys[$intKey];
	}
	
}
