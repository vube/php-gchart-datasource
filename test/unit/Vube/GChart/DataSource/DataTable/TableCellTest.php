<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\test;

use Vube\GChart\DataSource\DataTable\TableCell;
use Vube\GChart\DataSource\DataTable\Value\NumberValue;
use Vube\GChart\DataSource\DataTable\Value\TextValue;


/**
 * TableCellTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableCellTest extends \PHPUnit_Framework_TestCase
{
	public function testTableCellValue()
	{
		$value = new TextValue('foo');
		$cell = new TableCell($value);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame(null, $cell->getFormattedValue(), "Table cell must have null formatted value");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertEquals(0, count($properties), "properties must be an empty array");
	}

	public function testTableCellValueWithFormat()
	{
		$value = new NumberValue(1.29159386);
		$formattedValue = sprintf("%0.2f", $value->getValue());
		$cell = new TableCell($value, $formattedValue);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame($formattedValue, $cell->getFormattedValue(), "Table cell formatted value must match input");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertEquals(0, count($properties), "properties must be an empty array");
	}

	public function testTableCellValueWithProperties()
	{
		$value = new TextValue('foo');
		$properties = array('a' => 'enabled');
		$cell = new TableCell($value, $value->getValue(), $properties);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame($value->getValue(), $cell->getFormattedValue(), "Table cell formatted value must match input");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertArrayHasKey('a', $properties, "properties[a] must exist");
		$this->assertSame($properties['a'], $cell->getCustomProperty('a'), "getCustomProperty must return expected value");
	}

	public function testSetCustomProperty()
	{
		$propertyName = 'property-name';
		$propertyValue = 'value';
		$cell = new TableCell(new TextValue('test'));
		$cell->setCustomProperty($propertyName, $propertyValue);
		$this->assertSame($propertyValue, $cell->getCustomProperty($propertyName), "getCustomProperty must return the property name");
	}

	public function testGetCustomNonexistentProperty()
	{
		$cell = new TableCell(new TextValue('test'));
		$this->assertNull($cell->getCustomProperty('no-such-property'), "Expect null return for no-such-property");
	}
}