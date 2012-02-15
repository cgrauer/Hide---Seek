<?php  
# CgLib for concrete5 - Version 0.9.0
# copyright (c) 2012 Christian Grauer

defined('C5_EXECUTE') or die(_("Access Denied."));

class CgLib {
	
	public static function cmpVersionStrings($string1, $string2) {
		$cmp1 = preg_split('/\D+/',$string1);
		$cmp2 = preg_split('/\D+/',$string2);
		$i = 0; 
		foreach ( $cmp1 as $digit ) {		
			if ( $cmp1[$i] < $cmp2[$i] ) {
				return 1;
			} elseif ( $cmp1[$i] > $cmp2[$i] ) {
				return 3;
			}
			$i++;
		}
		return 2;
	}

}

class cgInstaller {
	
	function __construct( &$package ) {
		$this->pkg = $package;
	}

	public function install_blocks ( $blocks ) {
		$pkg = $this->getPackage();
		foreach ( $blocks as $handle ) {
			BlockType::installBlockTypeFromPackage($handle, $pkg);
		}
	}

	
	public function install_settings ( $settings ) {
		$pkg = $this->getPackage();

		$db = Loader::db();
		$sql = "INSERT INTO " . $pkg->getPackageHandle() . 'Settings ( ' . implode( ', ', array_keys($settings) ) . ' ) VALUES ( "' . implode( '", "', array_values($settings) ) . '" )'; 
		$records = $db->query($sql);
	
	}


	public function install_singlepages ($paths, $names, $descriptions ) {
		$pkg = $this->getPackage();

		Loader::model('single_page');
		
		for ( $i = 0; $i < count( $paths ); $i++ )  {
			$t1 = SinglePage::getByPath($paths[$i]); 
#			if ( !isset($t1) ) {
				$t1 = SinglePage::add($paths[$i], $pkg);
				$t1->update(array('cName'=>$names[$i], 'cDescription'=>$descriptions[$i]));
#			}
		}				
	}
	
	public function createAttributeSet($handle,$name ,$category = 'collection') {
		Loader::model('attribute');
		$pkg = $this->getPackage();
		$pkgID = $pkg->getPackageID();
		$cat = AttributeKeyCategory::getByHandle($category);
		$catID = $cat->getAttributeKeyCategoryID();
		$as = AttributeSet::getByHandle($handle);
		if ( !is_object( $as ) ) {
			
			# create Attribute set
			$db = Loader::db();
			$db->Execute('INSERT INTO AttributeSets ( asName, asHandle, akCategoryID, pkgID ) VALUES ( ?, ?, ?, ? )', array( $name, $handle, $catID, $pkgID ) );
			$as = AttributeSet::getByHandle($handle);
			
		}
		
		return $as;
	}

	public function install_attribute($type, $handle, $name, &$set, $searchable = 1, $indexsearch = 1, $autocreate = 0, $editable = 1 ) {
		Loader::model('attribute/categories/collection');
		Loader::model('attribute/set');
		$pkg = $this->getPackage();

		$ak = CollectionAttributeKey::getByHandle($handle);
		if ( !is_object( $ak ) ) {
			$cak = new CollectionAttributeKey();
			$akArgs = array ( 'akHandle' => $handle, 'akName' => $name, 'akIsSearchable' => $searchable, 'akIsSearchableIndexed' => $indexsearch, 'akIsAutoCreated' => $autocreate, 'akIsEditable' => $editable );
			$cak->add($type, $akArgs, $pkg );
			$cak->__destruct();
			unset($cak);
			$ak = CollectionAttributeKey::getByHandle($handle);
		}

		$ak->setAttributeSet( $set );
		return $ak;
	}

	public function delete_attributes( $handle ) {
		Loader::model('attribute/categories/collection');
		$cak = CollectionAttributeKey::getByHandle($handle);
		if ( is_object( $cak ) ) {
			$cak->delete();
		}
	}
	
	public function getPackage() {
		return $this->pkg;
	}

}
