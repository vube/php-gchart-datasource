<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource;


/**
 * Date class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class Date extends \DateTime
{
	/**
	 * @param int|string $time [optional]
	 * @param \DateTimeZone $timezone [optional] UTC by default
	 */
	public function __construct($time = "now", \DateTimeZone $timezone = null)
	{
		if(is_int($time))
			$time = '@'.$time;

		if($timezone === null)
			$timezone = new \DateTimeZone('UTC');

		parent::__construct($time, $timezone);
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		$year = $this->format("Y");
		return (int)$year;
	}

	/**
	 * Zero-based Month index
	 * @return int 0..11
	 */
	public function getMonth()
	{
		$month = -1 + $this->format("n"); // 0..11
		return $month;
	}

	/**
	 * @return int 1..31
	 */
	public function getMonthDay()
	{
		$day = $this->format("j"); // 1..31
		return (int)$day;
	}

	/**
	 * @return int
	 */
	public function getHours()
	{
		$hours = $this->format("G"); // 0..23
		return (int)$hours;
	}

	/**
	 * @return int
	 */
	public function getMinutes()
	{
		$mins = 0 + $this->format("i"); // remove leading zeros
		return $mins;
	}

	/**
	 * @return int
	 */
	public function getSeconds()
	{
		$secs = 0 + $this->format("s"); // remove leading zeros
		return $secs;
	}

	/**
	 * @return int
	 */
	public function getMicroseconds()
	{
		$micros = 0 + $this->format("u"); // remove leading zeros
		return $micros;
	}

	/**
	 * @return int
	 */
	public function getMilliseconds()
	{
		// Round to the nearest millisecond
		$millis = ((int)($this->getMicroseconds()/1000));
		return $millis;
	}

	public function getTimezoneOffsetString()
	{
		$tzo = $this->format("O");
		return $tzo;
	}

	/**
	 * Return a predictable string representation
	 * @return string
	 */
	public function __toString()
	{
		$micros = $this->getMicroseconds();
		$string = $this->format('Y-m-d\TH:i:s');
		if($micros > 0)
			$string .= "." . $micros;
		$string .= $this->getTimezoneOffsetString();
		return $string;
	}
}