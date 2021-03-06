<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Exception;


/**
 * Value Type Mismatch Exception class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueTypeMismatchException extends Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param ValueType $expectedType [required] The expected data type or an array of expected types.
	 * @param int $columnIndex [optional] Index of the column with the wrong data type.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct(ValueType $expectedType, $columnIndex = 0, \Exception $previous = null)
	{
		$message = "Value type mismatch, expected ".$expectedType->getTypeName();

		parent::__construct($message, $columnIndex, $previous);
	}

}
