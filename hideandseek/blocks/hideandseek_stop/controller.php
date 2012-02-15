<?php  
defined('C5_EXECUTE') or die("Access Denied.");

class HideandseekStopBlockController extends BlockController {
	
	protected $btName = 'Hide & Seek Stopper';
	protected $btHandle = 'hideandseek_stop';
	protected $btDescription = '';
	protected $btTable = 'btHideAndSeekStop';
	protected $btWrapperClass = 'ccm-ui';

	public function getBlockTypeHandle() {
		return 'hideandseek_stop';
	}

	public function getBlockTypeDescription() {
		return t("Ends Hide & Seek sections.");
	}
	
	public function getBlockTypeName() {
		return t("Hide & Seek Stopper");
	}		
		
	public function view() {
		$this->set('block_controller', $this );
	}

	public function isLastStopperInArea($c, $ah, $bID) {
		$c = $this->getCollectionObject();
		$area = Area::get($c, $ah);
		$blocklist = $area->getAreaBlocksArray( $c );
		$blocklist = array_reverse($blocklist);
		foreach ( $blocklist as $block ) {
			if ( $block->getBlockTypeHandle() == $this->btHandle) {
				if ( $block->getBlockID() == $bID ) {
					return 1; 
				} else {
					return 0;
				}
			}
		}
	}
	
}

?>