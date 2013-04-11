<?php
class MJaxGoogleAnalyticsAction extends MJaxBaseAction{
    protected $strSpecial = null;
    public function __construct($strSpecial = null){
        $this->strSpecial = $strSpecial;
    }
    public function Render(){
        $strRendered = '';
        $strRendered .= sprintf(
        	"_gaq.push(['_trackEvent', '%s', '%s']);",
			$this->objEvent->Control->ControlId,
			$this->objEvent->EventName
		);
        //The following wont render anything unless blnOnce is set to true
        $strRendered .= $this->objEvent->RenderUnbind();
        $strRendered .= '';
        return $strRendered;
    }
}