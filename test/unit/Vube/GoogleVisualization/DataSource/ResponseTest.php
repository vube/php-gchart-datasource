<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test;

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Exception;
use Vube\GoogleVisualization\DataSource\OutputType;
use Vube\GoogleVisualization\DataSource\Render\iRenderer;
use Vube\GoogleVisualization\DataSource\Render\JsonRenderer;
use Vube\GoogleVisualization\DataSource\Request;
use Vube\GoogleVisualization\DataSource\Response;


class MockRenderer implements iRenderer {
	public $renderCount = 0;
	public function getOutput($i) {
		return "render #".$i;
	}
	public function render(Response $response) {
		$this->renderCount++;
		return $this->getOutput($this->renderCount);
	}
};


class MockBrokenRenderer implements iRenderer {
	public function render(Response $response) {
		throw new Exception("Renderer is broken for test purposes");
	}
}


/**
 * ResponseTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$request = new Request();
		$response = new Response($request);
		$getRequest = $response->getRequest();
		$getDataTable = $response->getDataTable();

		$this->assertEquals($request, $getRequest, "Request must match");
		$this->assertTrue($getDataTable instanceof DataTable, "Empty DataTable must be returned");
	}

	public function testSetDataTable()
	{
		$request = new Request();
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('textfield', ValueType::STRING));
		$data->addColumn(new ColumnDescription('intfield', ValueType::NUMBER));
		$response = new Response($request);
		$response->setDataTable($data);
		$getRequest = $response->getRequest();
		$getDataTable = $response->getDataTable();

		$this->assertEquals($request, $getRequest, "Request must match");
		$this->assertEquals($data, $getDataTable, "DataTable must match");
	}

	public function testGetRendererTsvExcelThrowsNotImplementedException()
	{
		// A request asking for TSV_EXCEL OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request', array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue(new OutputType(OutputType::TSV_EXCEL)));

		$response = new Response($request);

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\NotImplementedException');
		$response->getRenderer();
	}

	public function testGetRendererCsvThrowsNotImplementedException()
	{
		// A request asking for CSV OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request', array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue(new OutputType(OutputType::CSV)));

		$response = new Response($request);

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\NotImplementedException');
		$response->getRenderer();
	}

	public function testGetRendererHtmlThrowsNotImplementedException()
	{
		// A request asking for HTML OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request', array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue(new OutputType(OutputType::HTML)));

		$response = new Response($request);

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\NotImplementedException');
		$response->getRenderer();
	}

	public function testGetRendererNewUnknownOutputTypeThrowsException()
	{
		// An OutputType object that returns a new OutputType
		// that the response object isn't yet aware of.
		$outputType = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\OutputType',
			array('getCode'), array(), '', false);
		$outputType->expects($this->any())
			->method('getCode')
			->will($this->returnValue('new-unknown-output-type'));

		// A request that returns this new OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request',
			array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue($outputType));

		$response = new Response($request);

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception');
		$response->getRenderer();
	}

	public function testGetRendererJson()
	{
		// A request asking for JSON OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request', array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue(new OutputType(OutputType::JSON)));

		$response = new Response($request);
		$renderer = $response->getRenderer();

		$this->assertTrue($renderer instanceof JsonRenderer, "JsonRenderer used for JSON output");
	}

	public function testGetRendererJsonp()
	{
		// A request asking for JSONP OutputType
		$request = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Request', array('getOutputType'));
		$request->expects($this->any())
			->method('getOutputType')
			->will($this->returnValue(new OutputType(OutputType::JSONP)));

		$response = new Response($request);
		$renderer = $response->getRenderer();

		$this->assertTrue($renderer instanceof JsonRenderer, "JsonRenderer used for JSONP output");
	}

	public function testToString()
	{
		$mockRenderer = new MockRenderer();
		$request = new Request();

		// A response that uses the MockRenderer to generate output
		$response = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Response',
			array('getRenderer'), array($request));
		$response->expects($this->once())
			->method('getRenderer')
			->will($this->returnValue($mockRenderer));

		$expected = $mockRenderer->getOutput(1);
		$output = $response->__toString();
		$this->assertSame($expected, $output, "Response->__toString should return rendered output");
	}

	public function testExceptionDuringRenderIsCaughtAndPropagated()
	{
		$mockRenderer = new MockBrokenRenderer();
		$request = new Request();

		// A response that uses the MockRenderer to generate output
		$response = $this->getMock('\\Vube\\GoogleVisualization\\DataSource\\Response',
			array('getRenderer'), array($request));
		$response->expects($this->once())
			->method('getRenderer')
			->will($this->returnValue($mockRenderer));

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\RenderFailureException');
		$output = $response->__toString();
	}
}