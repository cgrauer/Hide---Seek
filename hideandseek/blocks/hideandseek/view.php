<?php defined('C5_EXECUTE') or die("Access Denied."); 

# get some objects and variables
$ah = $this->block->getAreaHandle();
$ar = $this->block->getBlockAreaObject();
$c = Page::getCurrentPage();
$isFirstOpenerInArea = $this->controller->isFirstOpenerInArea($c, $ah);
$enclosingEnd = $this->area->getAttribute('enclosingEnd');
$hasReplacementsEnd = $this->area->getAttribute('enclosingEndHasReplacements');
$enclosingStart = $this->area->getAttribute('enclosingStart');
$hasReplacementsStart = $this->area->getAttribute('enclosingStartHasReplacements');
$obj = $this->block; # the variable named "obj" is used by custom styles include (see below)
$blockStyle = $obj->getBlockCustomStyleRule();

# block display for edit mode
if ( $c->isEditMode() ) { 

	?><div>
		<img style="float: left; margin: 0 5px 0 0;" src="<?php echo $this->getBlockURL(); ?>/images/icon32.png" />
		<p>
			<strong><?php echo $openLink ?></strong><br />
			<?php echo t('Hide & Seek controller is deactivated when in edit mode'); ?>
		</p>
	</div><?php 

# prepare block display in normal mode
} else { 

		#
		# first prepare buttons, classes, id's ...
		#

		// get uniqe IDs for html elements using the block-id
		$blockID = $this->block->getBlockID();
		$idOpener = 'hs-opener-' . $blockID;
		$idContent = 'hs-content-' . $blockID;
		$idCloser = 'hs-closer-' . $blockID;
	
		// set classes for css and jquery
		$classOpener = 'hs-link hs-opener';
		$classContent = 'hs-content';
		$classCloser = 'hs-link hs-closer';
	
		// set classes for jquery to auto-close this section
		if ( $autoClose ) {
			$classOpener .= ' hs-opener-auto';
			$classContent .= ' hs-content-auto';
			$classCloser .= ' hs-closer-auto';
		}
	
		// set classes for on-load status when jacascript is turned on
		if ( $openOnLoad ) { 
			$classOpener .= ' hs-hide'; 
			$classContent .= ' hs-show'; 
			$classCloser .= ' hs-show';
		} else {
			$classOpener .= ' hs-show'; 
			$classContent .= ' hs-hide'; 
			$classCloser .= ' hs-hide';
		}
	
		// set style for on-load status when jacascript is turned off
		$styleOpener = 'display: none;';
		$styleContent = 'display: block';
		$styleCloser = 'display: block'; 
	
		$htmlAnchorOpen = '<a href="javascript:void(0);" class="' . $classOpener . '" style="' . $styleOpener . '" id="' . $idOpener . '">';
		$htmlAnchorClose = '<a href="javascript:void(0);" class="' . $classCloser . '" style="' . $styleCloser . '" id="' . $idCloser . '">';
		if ( $openLinkImageID ) { 
			$openLinkImageSrc = File::getRelativePathFromID( $openLinkImageID );
			$imageOpener = '<img border="0" src="' . $openLinkImageSrc . '" alt="'.$openLink .'" title="'.$openLink .'" style="margin-right: 0.5em;" />';
		}
		if ( $closeLinkImageID ) { 
			$closeLinkImageSrc = File::getRelativePathFromID( $closeLinkImageID );
			$imageCloser = '<img border="0" src="' . $closeLinkImageSrc . '" alt="'.$closeLink .'" title="'.$closeLink .'" style="margin-right: 0.5em;" />';
		}
		$htmlOpener =  '<' . $openTag . '>' . $imageOpener .  $openLink . '</'. $openTag .'>';
		$htmlCloser =  '<' . $closeTag . '>' . $imageCloser .  $closeLink . '</'. $closeTag .'>';
		if ( $appendLinkToTeaser ) { 
			if ( $teaserIsLink ) {  
				$teaserOpen = preg_replace('/(<[^>]*?>[^>]*)$/', $htmlOpener.'$1', $teaser );
				if ( $hideTeaserOnOpen ) {
					$teaserClose = $htmlCloser;
				} else {
					$teaserClose = preg_replace('/(<[^>]*?>[^>]*)$/', $htmlCloser.'$1', $teaser );
				}
				$htmlOpener = $htmlAnchorOpen . $teaserOpen . '</a>';
				$htmlCloser = $htmlAnchorClose . $teaserClose . '</a>';
			} else {
				$htmlOpener = $htmlAnchorOpen . $htmlOpener.'</a>';
				$htmlCloser =  $htmlAnchorClose . $htmlCloser.'</a>';
				$htmlOpener = '<div class="' . $classOpener . '" style="' . $styleOpener . '" id="hs-teaser-open-' . $blockID . '">' . preg_replace('/(<[^>]*?>[^>\s]*)$/', ' '.$htmlOpener.'$1', $teaser ) . '</div>';
				if ( !$hideTeaserOnOpen ) {
					$htmlCloser = '<div class="' . $classCloser . '" style="' . $styleCloser . '" id="hs-teaser-close-' . $blockID . '">' . preg_replace('/(<[^>]*?>[^>\s]*)$/', ' '.$htmlCloser.'$1', $teaser ) . '</div>';
				}
			}
		} else {	
			if ( $teaserIsLink ) {  
				$htmlOpener = $htmlAnchorOpen . $teaser . $htmlOpener . '</a>';
				if ( $hideTeaserOnOpen ) {
					$teaser = ''; 
				}
				$htmlCloser = $htmlAnchorClose . $teaser . $htmlCloser . '</a>';
			} else {
				$htmlOpener = '<div class="' . $classOpener . '" style="' . $styleOpener . '" id="hs-teaser-open-' . $blockID . '">' . $teaser . '</div>' . $htmlAnchorOpen . $htmlOpener . '</a>';
				if ( $hideTeaserOnOpen ) {
					$htmlCloser = $htmlAnchorClose . $htmlCloser . '</a>';
				} else {
					$htmlCloser = '<div class="' . $classCloser .'" style="' . $styleCloser . '" id="hs-teaser-close-' . $blockID . '">' . $teaser . '</div>' . $htmlAnchorClose . $htmlCloser . '</a>';
				}
			}
		}
		
		
		#
		# everything is prepared now, begin with output
		#
	
		# close opened division for custom styles before opening h&s section
		$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_view.php';										
		include($footer);
	
		# close block wrapper before opening h&s section
		$th = Loader::helper('text');
		if (!empty($enclosingEnd) && $hasReplacementsEnd) {
			$bID = $this->block->getBlockID();
			$btHandle = $this->block->getBlockTypeHandle();
			$bName = ($btHandle == 'core_stack_display') ? Stack::getByID($this->block->getInstance()->stID)->getStackName() : $this->block->getBlockName();
			$th = Loader::helper('text');
			$bSafeName = $th->entities($bName);
#			$alternatingClass = ($blockPositionInArea % 2 == 0) ? 'even' : 'odd';
			echo sprintf($enclosingEnd, $bID, $btHandle, $bSafeName, $blockPositionInArea, $alternatingClass);
		} else {
			echo $enclosingEnd;
		}
			
		# close div:hideandseek and div:content (if not first h&s block)
		if ( !$isFirstOpenerInArea ) {
			echo '</div></div>';
		}	
	
		# start h&s division
		?><div class="hideandseek"><?php

		# clone the division for custom block styles
		if (is_object($blockStyle)) { 
			?><div id="<?php echo $blockStyle->getCustomStyleRuleCSSID(true)?>Hideandseek" class="<?php echo $blockStyle->getCustomStyleRuleClassName() ?> ccm-block-styles" ><?php 
		}
		
		# H&S buttons
		echo $htmlOpener;
		echo $htmlCloser;
		
		# clone division for custom block styles (end)
		if (is_object($blockStyle)) { 
			?></div><?php  
		} 

		# open division containing expand/collaps content 
		?><div class="<?php echo $classContent ?>" style="<?php echo $styleContent ?>" id="<?php echo $idContent ?>"><?php
	
		# open block wrapper for h&s block
		if (!empty($enclosingStart) && $hasReplacementsStart) {
			$bID = $this->block->getBlockID();
			$btHandle = $this->block->getBlockTypeHandle();
			$bName = ($btHandle == 'core_stack_display') ? Stack::getByID($this->block->getInstance()->stID)->getStackName() : $this->block->getBlockName();
			$th = Loader::helper('text');
			$bSafeName = $th->entities($bName);
#			$alternatingClass = ($blockPositionInArea % 2 == 0) ? 'even' : 'odd';
			echo sprintf($enclosingStart, $bID, $btHandle, $bSafeName, $blockPositionInArea, $alternatingClass);
		} else {
			echo $enclosingStart;
		}

		# open virtual division for closing custom styling
		if (is_object($blockStyle)) { 
			?><div style="display:none"><?php  
		} 

	}
