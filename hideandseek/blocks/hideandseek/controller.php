<?php  
defined('C5_EXECUTE') or die("Access Denied.");

class HideandseekBlockController extends BlockController {
	
	protected $btName = 'Hide & Seek';
	protected $btHandle = 'hideandseek';
	protected $btDescription = '';
	protected $btTable = 'btHideAndSeek';
	protected $presetTable = 'hideandseekPresets';
	protected $btInterfaceWidth = "600";
	protected $btInterfaceHeight = "600";
	protected $btWrapperClass = 'ccm-ui';
	
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	protected $btCacheBlockOutputLifetime = 300;
	
	public function getHeaderItems () {
		return $this->headerItems;
	}

	public function getBlockTypeHandle() {
		return $this->btHandle;
	}

	public function getBlockTypeDescription() {
		return t("Ends Hide & Seek sections.");
	}
	
	public function getBlockTypeName() {
		return t("Hide & Seek Stopper");
	}		
		
	public function getSearchableContent() {
		$content = array();
		$content[] = $this->teaser;
		$content[] = $this->openLink;
		$content[] = $this->closeLink;
		$content[] = $this->teaserIsLink;
		$content[] = $this->appendLinkToTeaser;
		$content[] = $this->hideTeaserOnOpen;
		$content[] = $this->autoClose;
		$content[] = $this->openOnLoad;
		return implode(' - ', $content);
	}
	
	public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->javascript(DIR_REL.'/packages/hideandseek/js/hideandseek.js'));
	}
	
	public function isFirstOpenerInArea($c, $ah) {
		$block = $this->getBlockObject();
		$cobj = $this->getCollectionObject();
		$area = Area::get($c, $ah);
		$blocklist = $area->getAreaBlocksArray( $c );
		foreach ( $blocklist as $block ) {
			$blocktype = $block->getBlockTypeObject();
			$controller = $blocktype->getController();
			if ( $block->getBlockTypeHandle() == $this->btHandle) {
				if ( $block->getBlockID() == $this->getBlockObject()->getBlockID() ) {
					return 1; 
				} else {
					return 0;
				}
			}
		}
	}
	
	public function view() {
		$this->set('teaser', $this->translateFrom($this->teaser));
		$this->set('openLinkImage', (empty($this->openLinkImageID) ? null : $this->get_image_object($this->openLinkImageID, 0, 0, false)));
		$this->set('openLinkImage', (empty($this->closeLinkImageID) ? null : $this->get_image_object($this->closeLinkImageID, 0, 0, false)));
		
#		echo "BSR: ";
#		var_dump($this->headerItems);
	}

	public function edit() {
		$this->set('teaser', $this->translateFromEditMode($this->teaser));
		$this->set('openLinkImage', (empty($this->openLinkImageID) ? null : File::getByID($this->openLinkImageID)));
		$this->set('closeLinkImage', (empty($this->closeLinkImageID) ? null : File::getByID($this->closeLinkImageID)));
	}

	public function save($args) {
		$args['teaserIsLink'] = empty($args['teaserIsLink']) ? 0 : 1;
		$args['appendLinkToTeaser'] = empty($args['appendLinkToTeaser']) ? 0 : 1;
		$args['hideTeaserOnOpen'] = empty($args['hideTeaserOnOpen']) ? 0 : 1;
		$args['autoClose'] = empty($args['autoClose']) ? 0 : 1;
		$args['openOnLoad'] = empty($args['openOnLoad']) ? 0 : 1;
		$args['teaser'] = $this->translateTo($args['teaser']);
		$args['openLinkImageID'] = empty($args['openLinkImageID']) ? 0 : $args['openLinkImageID'];
		$args['closeLinkImageID'] = empty($args['closeLinkImageID']) ? 0 : $args['closeLinkImageID'];
		parent::save($args);
	}

	//Helper function for image fields
	private function get_image_object($fID, $width = 0, $height = 0, $crop = false) {
		if (empty($fID)) {
			$image = null;
		} else if (empty($width) && empty($height)) {
			//Show image at full size (do not generate a thumbnail)
			$file = File::getByID($fID);
			$size = @getimagesize($file->getPath());
			$image = new stdClass;
			$image->src = $file->getRelativePath();
			$image->width = $size[0];
			$image->height = $size[1];
		} else {
			//Generate a thumbnail
			$width = empty($width) ? 9999 : $width;
			$height = empty($height) ? 9999 : $height;
			$file = File::getByID($fID);
			$ih = Loader::helper('image_crop', 'designer_content');
			$image = $ih->getThumbnail($file, $width, $height, $crop);
		}
	
		return $image;
	}	

//WYSIWYG HELPER FUNCTIONS (COPIED FROM "CONTENT" BLOCK):
	function br2nl($str) {
		$str = str_replace("\r\n", "\n", $str);
		$str = str_replace("<br />\n", "\n", $str);
		return $str;
	}
	
	function translateFromEditMode($text) {
		// old stuff. Can remove in a later version.
		$text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
		$text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);

		// we have the second one below with the backslash due to a screwup in the
		// 5.1 release. Can remove in a later version.

		$text = preg_replace(
			array(
				'/{\[CCM:BASE_URL\]}/i',
				'/{CCM:BASE_URL}/i'),
			array(
				BASE_URL . DIR_REL,
				BASE_URL . DIR_REL)
			, $text);
			
		// now we add in support for the links
		
		$text = preg_replace(
			'/{CCM:CID_([0-9]+)}/i',
			BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=\\1',
			$text);

		// now we add in support for the files
		
		$text = preg_replace_callback(
			'/{CCM:FID_([0-9]+)}/i',
			array('HideAndSeekBlockController', 'replaceFileIDInEditMode'),
			$text);
		

		return $text;
	}
	
	function translateFrom($text) {
		// old stuff. Can remove in a later version.
		$text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
		$text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);

		// we have the second one below with the backslash due to a screwup in the
		// 5.1 release. Can remove in a later version.

		$text = preg_replace(
			array(
				'/{\[CCM:BASE_URL\]}/i',
				'/{CCM:BASE_URL}/i'),
			array(
				BASE_URL . DIR_REL,
				BASE_URL . DIR_REL)
			, $text);
			
		// now we add in support for the links
		
		$text = preg_replace_callback(
			'/{CCM:CID_([0-9]+)}/i',
			array('HideAndSeekBlockController', 'replaceCollectionID'),
			$text);

		$text = preg_replace_callback(
			'/<img [^>]*src\s*=\s*"{CCM:FID_([0-9]+)}"[^>]*>/i',
			array('HideAndSeekBlockController', 'replaceImageID'),
			$text);

		// now we add in support for the files that we view inline			
		$text = preg_replace_callback(
			'/{CCM:FID_([0-9]+)}/i',
			array('HideAndSeekBlockController', 'replaceFileID'),
			$text);

		// now files we download
		
		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			array('HideAndSeekBlockController', 'replaceDownloadFileID'),
			$text);
		
		return $text;
	}
	
	private function replaceFileID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			$path = File::getRelativePathFromID($fID);
			return $path;
		}
	}
	
	private function replaceImageID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			preg_match('/width\s*="([0-9]+)"/',$match[0],$matchWidth);
			preg_match('/height\s*="([0-9]+)"/',$match[0],$matchHeight);
			$file = File::getByID($fID);
			if (is_object($file) && (!$file->isError())) {
				$imgHelper = Loader::helper('image');
				$maxWidth = ($matchWidth[1]) ? $matchWidth[1] : $file->getAttribute('width');
				$maxHeight = ($matchHeight[1]) ? $matchHeight[1] : $file->getAttribute('height');
				if ($file->getAttribute('width') > $maxWidth || $file->getAttribute('height') > $maxHeight) {
					$thumb = $imgHelper->getThumbnail($file, $maxWidth, $maxHeight);
					return preg_replace('/{CCM:FID_([0-9]+)}/i', $thumb->src, $match[0]);
				}
			}
			return $match[0];
		}
	}

	private function replaceDownloadFileID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			$c = Page::getCurrentPage();
			return View::url('/download_file', 'view', $fID, $c->getCollectionID());
		}
	}

	private function replaceFileIDInEditMode($match) {
		$fID = $match[1];
		return View::url('/download_file', 'view_inline', $fID);
	}
	
	private function replaceCollectionID($match) {
		$cID = $match[1];
		if ($cID > 0) {
			$c = Page::getByID($cID, 'APPROVED');
			return Loader::helper("navigation")->getLinkToCollection($c);
		}
	}
	
	function translateTo($text) {
		// keep links valid
		$url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
		$url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
		$url3 = View::url('/download_file', 'view_inline');
		$url3 = str_replace('/', '\/', $url3);
		$url3 = str_replace('-', '\-', $url3);
		$url4 = View::url('/download_file', 'view');
		$url4 = str_replace('/', '\/', $url4);
		$url4 = str_replace('-', '\-', $url4);
		$text = preg_replace(
			array(
				'/' . $url1 . '\?cID=([0-9]+)/i', 
				'/' . $url3 . '([0-9]+)\//i', 
				'/' . $url4 . '([0-9]+)\//i', 
				'/' . $url2 . '/i'),
			array(
				'{CCM:CID_\\1}',
				'{CCM:FID_\\1}',
				'{CCM:FID_DL_\\1}',
				'{CCM:BASE_URL}')
			, $text);
		return $text;
	}

}
