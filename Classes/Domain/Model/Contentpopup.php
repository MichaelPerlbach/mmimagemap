<?php
namespace MikelMade\Mmimagemap\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 MikelMade (http://www.mikelmade.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package mmimagemap
 *
 */
 
class Contentpopup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
		* uid
		*
		* @lazy
		* @var \integer
		*/
	protected $uid;

	/**
		* areaid 
		*
		* @lazy
		* @var \integer
		*/
	protected $areaid;

	/**
		* contentid
		*
		* @lazy
		* @var \integer
		*/
	protected $contentid;
	
	/**
		* popupwidth
		*
		* @lazy
		* @var \integer
		*/
	protected $popupwidth;
	
	/**
		* popupheight
		*
		* @lazy
		* @var \integer
		*/
	protected $popupheight;
	
	/**
		* popupx
		*
		* @lazy
		* @var \integer
		*/
	protected $popupx;
	
	/**
		* popupy
		*
		* @lazy
		* @var \integer
		*/
	protected $popupy;
	
	/**
		* popupbordercolor
		*
		* @lazy
		* @var \string
		*/
	protected $popupbordercolor;
	
	/**
		* popupbackgroundcolor
		*
		* @lazy
		* @var \string
		*/
	protected $popupbackgroundcolor;
	
	/**
		* popupborderwidth
		*
		* @lazy
		* @var \integer
		*/
	protected $popupborderwidth;
	
	/**
		* active
		*
		* @lazy
		* @var \integer
		*/
	protected $active;
	
	/**
		* Returns the uid
		*
		* @return \integer $uid
		*/
	public function getId() {
		return $this->uid;
	}
	
	/**
		* Returns the areaid
		*
		* @return \integer $areaid
		*/
	public function getAreaid() {
		return $this->areaid;
	}
	
	/**
		* Sets the areaid
		*
		* @param \integer $areaid
		* @return void
		*/
	public function setAreaid($areaid) {
		$this->areaid = $areaid;
	}
	
	/**
		* Returns the contentid
		*
		* @return \string $contentid
		*/
	public function getContentid() {
		return $this->contentid;
	}
	
	/**
		* Sets the contentid
		*
		* @param \integer $contentid
		* @return void
		*/
	public function setContentid($contentid) {
		$this->contentid = $contentid;
	}
	
	/**
		* Returns the popupwidth
		*
		* @return \integer $popupwidth
		*/
	public function getPopupwidth() {
		return $this->popupwidth;
	}
	
	/**
		* Sets the popupwidth
		*
		* @param \integer $popupwidth
		* @return void
		*/
	public function setPopupwidth($popupwidth) {
		$this->popupwidth = $popupwidth;
	}
	
	/**
		* Returns the popupheight
		*
		* @return \integer $popupheight
		*/
	public function getPopupheight() {
		return $this->popupheight;
	}
	
	/**
		* Sets the popupheight
		*
		* @param \integer $popupheight
		* @return void
		*/
	public function setPopupheight($popupheight) {
		$this->popupheight = $popupheight;
	}
	
	/**
		* Returns the popupx
		*
		* @return \integer $popupx
		*/
	public function getPopupx() {
		return $this->popupx;
	}
	
	/**
		* Sets the popupx
		*
		* @param \integer $popupx
		* @return void
		*/
	public function setPopupX($popupx) {
		$this->popupx = $popupx;
	}	
	
	/**
		* Returns the popupy
		*
		* @return \integer $popupy
		*/
	public function getPopupy() {
		return $this->popupy;
	}
	
	/**
		* Sets the popupy
		*
		* @param \integer $popupy
		* @return void
		*/
	public function setPopupY($popupy) {
		$this->popupy = $popupy;
	}	
	
	/**
		* Returns the popupbordercolor
		*
		* @return \string $popupbordercolor
		*/
	public function getPopupbordercolor() {
		return $this->popupbordercolor;
	}
	
	/**
		* Sets the popupbordercolor
		*
		* @param \string $popupbordercolor
		* @return void
		*/
	public function setPopupbordercolor($popupbordercolor) {
		$this->popupbordercolor = $popupbordercolor;
	}	
	
	/**
		* Returns the popupbackgroundcolor
		*
		* @return \string $popupbackgroundcolor
		*/
	public function getPopupbackgroundcolor() {
		return $this->popupbackgroundcolor;
	}
	
	/**
		* Sets the popupbackgroundcolor
		*
		* @param \string $popupbackgroundcolor
		* @return void
		*/
	public function setPopupbackgroundcolor($popupbackgroundcolor) {
		$this->popupbackgroundcolor = $popupbackgroundcolor;
	}
	
	/**
		* Returns the popupborderwidth
		*
		* @return \integer $popupborderwidth
		*/
	public function getPopupborderwidth() {
		return $this->popupborderwidth;
	}
	
	/**
		* Sets the popupborderwidth
		*
		* @param \integer $popupborderwidth
		* @return void
		*/
	public function setPopupborderwidth($popupborderwidth) {
		$this->popupborderwidth = $popupborderwidth;
	}
	
	/*
		* Returns the active
		*
		* @return \string $active
		*/
	public function getActive() {
		return $this->active;
	}
	
	/**
		* Sets the active
		*
		* @param \string $active
		* @return void
		*/
	public function setActive($active) {
		$this->active = $active;
	}	
	
}