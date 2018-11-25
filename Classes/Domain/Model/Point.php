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
 
class Point extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
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
		* num
		*
		* @lazy
		* @var \integer
		*/
	protected $num;
	
	/**
		* x
		*
		* @lazy
		* @var \integer
		*/
	protected $x;
	
	/**
		* y
		*
		* @lazy
		* @var \integer
		*/
	protected $y;
	
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
		* Returns the num
		*
		* @return \integer $num
		*/
	public function getNum() {
		return $this->num;
	}
	
	/**
		* Sets the num
		*
		* @param \integer $num
		* @return void
		*/
	public function setNum($num) {
		$this->num = $num;
	}
	
	/**
		* Returns the x
		*
		* @return \integer $x
		*/
	public function getX() {
		return $this->x;
	}
	
	/**
		* Sets the x
		*
		* @param \integer $x
		* @return void
		*/
	public function setX($x) {
		$this->x = $x;
	}
	
	/**
		* Returns the y
		*
		* @return \integer $y
		*/
	public function getY() {
		return $this->y;
	}
	
	/**
		* Sets the y
		*
		* @param \integer $y
		* @return void
		*/
	public function setY($y) {
		$this->y = $y;
	}
	
}