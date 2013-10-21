<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Exception;

use Vube\GChart\DataSource\Exception;


/**
 * InvalidReasonType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class InvalidReasonTypeException extends Exception {

	/**
	 * @param string $reasonType A string that does not describe a valid ReasonType code
	 */
	public function __construct($reasonType) {
		$message = "Invalid Reason Type: ".$reasonType;
		parent::__construct($message);
	}
}