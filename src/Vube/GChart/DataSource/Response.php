<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;

use Vube\GChart\DataSource\Base\ResponseStatus;
use Vube\GChart\DataSource\Base\StatusType;
use Vube\GChart\DataSource\DataTable\DataTable;
use Vube\GChart\DataSource\Exception\NotImplementedException;
use Vube\GChart\DataSource\Render\iRenderer;
use Vube\GChart\DataSource\Render\JsonRenderer;


/**
 * Response class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class Response {

	/**
	 * @var Request
	 */
	private $request;
	/**
	 * @var DataTable
	 */
	private $data;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->data = new DataTable();
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return DataTable
	 */
	public function getDataTable()
	{
		return $this->data;
	}

	/**
	 * @param DataTable $data
	 */
	public function setDataTable(DataTable $data)
	{
		$this->data = $data;
	}

	/**
	 * @return iRenderer
	 * @throws Exception\NotImplementedException
	 * @throws Exception
	 */
	public function getRenderer()
	{
		$outputTypeCode = $this->request->getOutputType()->getCode();

		$renderer = null;

		switch($outputTypeCode)
		{
			case OutputType::TSV_EXCEL:
			case OutputType::CSV:
			case OutputType::HTML:
				throw new NotImplementedException($outputTypeCode." rendering is not implemented");
				break;

			case OutputType::JSON:
			case OutputType::JSONP:
				$renderer = new JsonRenderer();
				break;

			default:
				throw new Exception("Unknown Output Type: ".$outputTypeCode);
				break;
		}

		return $renderer;
	}

	/**
	 * Render the DataTable as a string
	 *
	 * This may result in different output depending on the
	 * output type in the request.
	 */
	public function toString()
	{
		$renderer = $this->getRenderer();

		// If there were any warnings generating this DataTable, we must send them
		// in the response and set the response status to WARNING
		$responseStatus = null;
		if($this->data->getNumberOfWarnings() > 0)
			$responseStatus = new ResponseStatus(new StatusType(StatusType::WARNING));

		$output = $renderer->render($this, $responseStatus);
		return $output;
	}
}
