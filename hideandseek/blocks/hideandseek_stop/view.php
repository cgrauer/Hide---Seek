<?php defined('C5_EXECUTE') or die("Access Denied."); 

$c = $this->getCollectionObject();
$isLastStopperInArea = $block_controller->isLastStopperInArea($c, $this->block->getAreaHandle(), $this->block->getBlockID());
?>

<?php if ( !$c->isEditMode() ) { 
	
	
	$obj = $this->block;
	$blockStyle = $obj->getBlockCustomStyleRule();

	$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_view.php';										
	include($footer);
	
	?>
	</div></div>
	<?php if ( !$isLastStopperInArea ) { ?>
		<div class="hs-none"><div class="hs-dummy">
	<?php } 
	
	if (is_object($blockStyle)) { ?>
		<div style="display:none">
	<?php  } ?>
	
	<?php } else { ?>
		<div><img style="float: left; margin: 0 5px 0 0;" src="<?php echo $this->getBlockURL(); ?>/images/icon32.png" /><p><?php echo t('Hide & Seek Stopper controller is disabled when in edit mode'); ?></br><strong><?php echo $openLink ?></strong></p></div>
	<?php } ?>
	

