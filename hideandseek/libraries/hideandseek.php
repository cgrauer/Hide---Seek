<?php   defined('C5_EXECUTE') or die("Access Denied.");
# SSL Switch Version 1.00
# License: v. LICENSE.TXT
# copyright (c) 2011 Christian Grauer

class HideAndSeek {

    public function hidePage($view) {
    	
		$cobj = $view->getCollectionObject();
		
		$blocks = $cobj->getBlocks();
				
		if ( !$cobj->isEditMode() ) {
				
			$count = array();
			foreach ( $blocks as $block ) {
				$ar = $block->getAreaHandle();
				$blockController = $block->getController();
				if ( method_exists($blockController,'getBlockTypeHandle') ) {
					$blockTypeHandle = $blockController->getBlockTypeHandle();
				} else {
					$blockTypeHandle = $block->getBlockTypeHandle();
				}
				#echo "___".$ar.": ";echo $blockTypeHandle;	echo "--"; echo "<br />";
				$areas[$ar] = $block->getBlockAreaObject();
				$count[$ar] = $count[$ar] + 1;
			
				if ( $block->getBlockTypeHandle() == 'hideandseek' ) {
					$hsStatus[$ar] = 1;
					$hsStart[$ar] = 1;
					$lastOpener[$ar] = $block;
#					echo "ECS: ". $areas[$ar]->enclosingStart. ":SCE";
					# replace custom block styles with display:none and apply them to the hideandseek division
					$blockStyle = $block->getBlockCustomStyleRule();
					if ( is_object ( $blockStyle ) ) {
						$cssID = $blockStyle->getCustomStyleRuleCSSID(true);
						$preg_pattern = '/#'.$cssID.'\s/';
						$preg_replace = '#'.$cssID.'Hideandseek';
						$hi = $view->getHeaderItems();
						foreach ( $hi as $headerItem ) {
							if ( is_string($headerItem) && preg_match($preg_pattern, $headerItem) ) {
								$headerItemHideandseek = preg_replace( $preg_pattern, $preg_replace, $headerItem);
								$headerItemDummy = '<style type="text/css"> #'.$cssID . '{ display: none !important; } </style>';
								$view->addHeaderItem($headerItemDummy);
								$view->addHeaderItem($headerItemHideandseek);
							}
						}
					}
				}
				
				if ( $block->getBlockTypeHandle() == 'hideandseek_stop' ) {
					$hsStatus[$ar] = 0 ;
					$hsStop[$ar] = $block;
				}
				
				# add script snippet to re-init google maps within hide&seek sections 
				if ( ( $hsStatus[$ar] > 0 ) && ( $block->getBlockTypeHandle() == 'google_map' ) ) {
					$hsBlock = $lastOpener[$ar];
					if ( is_object( $hsBlock ) ) {
						$script = '<script type="text/javascript">function initGoogleMap' . $hsBlock->getBlockID() . '() { googleMapInit'.$block->getBlockID().'(); }</script>';
						$view->addHeaderItem($script);
					}
				}
				
			}
			
			if ( isset( $areas ) ) {
				foreach ( $areas as $arHandle => $arObject ) {
					if ( $hsStatus[$arHandle] > 0 ) {	# Last HS block was an opener
					$total = $arObject->getTotalBlocksInArea($cobj);
						$bt = BlockType::getByHandle('hideandseek_stop');
						$newBlock = $cobj->addBlock($bt, $arObject, NULL);
					} else {	# last HS block was no opener
						if ( $hsStart[$arHandle] > 0 ) {	# but there was at least one above (i.e. last HS block was closer)
							# do nothing
						} else { # there was no HS, so delete the stopper
							if ( isset($hsStop[$arHandle]) ) {
								$stopBlock = $hsStop[$arHandle];
								$stopBlock->deleteBlock();
							}
						}
					} 
					
				}		
			}
			
		}
		
		return $view;
    }
    
}