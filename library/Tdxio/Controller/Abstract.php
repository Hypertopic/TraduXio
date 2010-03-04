<?php 

/**
 * Abstract controller
 *
 * 
 * @uses       Zend_Controller_Action
 * @package    Traduxio
 * @subpackage Controller
 */
class Tdxio_Controller_Abstract extends Zend_Controller_Action
{
	protected $_model=null;
    protected $_modelname=null;

	
	 /**
     * _getModel() is a protected utility method for this controller. It is
     * responsible for creating the model object and returning it to the
     * calling action when needed. Depending on the depth and breadth of the
     * application, this may or may not be the best way of handling the loading
     * of models.  This concept will be visited in later tutorials, but for now
     * - in this application - this is the best technique.
     *
     * @return $_modelnameModel
     */
   protected function _getModel($classname=null)
    {
        if (null!=$classname) {
            $classname='Model_'.$classname;
            return new $classname();
        } else {
			$classname='Model_'.$this->_modelname;
			if (null === $this->_model) {
                $this->_model = new $classname();
				Tdxio_Log::info($this->_model);
            }
            return $this->_model;
        }
    }
	
	protected function _getUser(){
	
	}
		
	protected function _getRole(){
	
	}
	
	public function getRule($request){
		return 'noAction';	
	}
	
}
