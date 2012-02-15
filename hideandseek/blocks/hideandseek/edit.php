<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$form = Loader::helper('form');
?>
<ul  id="ccm-autonav-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-autonav-tab-buttons"><?php echo t('Buttons'); ?></a></li>
	<li class=""><a id="ccm-autonav-tab-teaser"><?php echo t('Teaser') ?></a></li>
	<li class=""><a id="ccm-autonav-tab-settings"><?php echo t('Settings') ?></a></li>
</ul>

<div class="ccm-block-fields">

<div class="ccm-autonavPane ccm-settings-pane" id="ccm-autonavPane-settings" style="display: none">

	<h2><?php echo t('Settings') ?></h2>

	<p>
		<?php  echo $form->checkbox('autoClose', 1, $autoClose); ?>
		<?php echo t('Automatically hide content if another section is opened'); ?>
	</p>
	<p>
		<?php  echo $form->checkbox('openOnLoad', 1, $openOnLoad); ?>
		<?php echo t('Show content on page load'); ?>
	</p>
	<p>
		<?php  echo $form->checkbox('hideTeaserOnOpen', 1, $hideTeaserOnOpen); ?>
		<?php echo t('Hide teaser when section is open'); ?>
	</p>
	<p>
		<?php  echo $form->checkbox('appendLinkToTeaser', 1, $appendLinkToTeaser); ?>
		<?php echo t('Append button to teaser (without break)'); ?>
	</p>
	<p>
		<?php  echo $form->checkbox('teaserIsLink', 1, $teaserIsLink); ?>
		<?php echo t('Utilize entire teaser as a link'); ?>
	</p>

</div>

<div class="ccm-autonavPane ccm-teaser-pane" id="ccm-autonavPane-teaser" style="display: none">

	<h2><?php echo t('Teaser') ?></h2>

	<p><?php echo t('The HTML content is displayed before the open/close button. See Settings to utilize entire teaser as button.'); ?></p>

	<p>
		<?php  $this->inc('editor_init.php'); ?>
		<textarea id="teaser" name="teaser" class="advancedEditor ccm-advanced-editor"><?php  echo $teaser; ?></textarea>
	</p>

</div>

<div class="ccm-autonavPane ccm-buttons-pane" id="ccm-autonavPane-buttons">

	<h2><?php echo t('Control Buttons') ?></h2>
	<p><?php echo t('Select an HTML tag and text to display as open/close button. Upon selecting an image, the HTML content will be used as alt-text and the selected HTML tag will be ignored.'); ?></p>
	<table>
	<tr>
		<td><?php echo t('Open Button') ?>:</td>
		<td>
			<?php 
			$options = array(
				'span' => '- none -',
				'p' => 'P',
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'div' => 'DIV',
			);
			echo $form->select('openTag', $options, $openTag);
			?>
		</td>
		<td>
			<?php  echo $al->image('openLinkImageID', 'openLinkImageID', t('Choose Image'), $openLinkImage); ?>
		</td>
		<td>
			<?php  echo $form->text('openLink', $openLink, array('style' => 'width: 95%;', 'maxlength' => '255')); ?>
		</td>
	</tr>
	<tr>
		<td><?php echo t('Close Button') ?>:</td>
		<td>
			<?php 
			$options = array(
			'span' => '- none -',
			'p' => 'P',
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
			'div' => 'DIV',
			);
			echo $form->select('closeTag', $options, $closeTag);
			?>
		</td>
		<td>
			<?php  echo $al->image('closeLinkImageID', 'closeLinkImageID', t('Choose Image'), $closeLinkImage); ?>
		</td>
		<td>
			<?php  echo $form->text('closeLink', $closeLink, array('style' => 'width: 95%;', 'maxlength' => '255')); ?>
		</td>
	</tr>
</table>

<hr>
<h2><?php echo t('Advice'); ?></h2>
<p><?php echo t("Hide & Seek may cause conflicts if used together with block wrapping. Please check possible effects in a test environement before using Hide & Seek in areas with block wrapping!"); ?></p>
	
</div>


