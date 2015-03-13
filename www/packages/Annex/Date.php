<?php
	/**
	 * Annex class for working with date
	 *
	 * @author Denis Dragomiric <den@lux-blog.org>
	 * @version Pagen 1.1.6
	 */

namespace Annex;


class Date {
	private $year;
	private $month;
	private $day;
	private $date;
	private $from = '1970-01-01';
	private $to   = '2070-01-01';
	private $format = 'Y-m-d';
	private $weekdayNames = [
				'Воскресенье',
				'Понедельник',
				'Вторник',
				'Среда',
				'Четверг',
				'Пятница',
				'Суббота'
			];
	private $monthNames = [
				null,
				'Января',
				'Февраля',
				'Марта',
				'Апреля',
				'Мая',
				'Июня',
				'Июля',
				'Августа',
				'Сентября',
				'Октября',
				'Ноября',
				'Декабря'
			];

	public function setFormat ($format) {$this->format = $format;}
	public function getYear () {return date("Y", strtotime($this->date));}
	public function getMonth () {return date("m", strtotime($this->date));}
	public function getDay () {return date("d", strtotime($this->date));}
	public function getFrom () {return $this->from;}
	public function getTo () {return $this->to;}
	public function getDate ($format = NULL) {
		$format = ($format === NULL) ? $this->format : $format;
		return date ($format, strtotime ($this->date));
	}

	/**
	 * Construct instance & set date
	 *
	 * @param null $year
	 * @param null $month
	 * @param null $day
	 *
	 * OR
	 *
	 * @param $date <####-##-##>
	 */
	public function __construct ($year = NULL, $month = NULL, $day = NULL) {
		$this->year  = $year;
		$this->month = $month;
		$this->day   = $day;
		$this->date  = $this->year.'-'.$this->month.'-'.$this->day;
		$this->date  = date ('Y-m-d', strtotime ($this->date));
		if ($month === NULL and $day === NULL) {
			$this->date = date ('Y-m-d', strtotime ($year));
		}
	}

	/**
	 *  $date1 = new \Annex\Date ('2012');
	 *  $date2 = new \Annex\Date ('2012', '2');
	 *  $date3 = new \Annex\Date ('2012', '2', '14');
	 *
	 *  $date1->placeConditionDate ();
	 *  $date2->placeConditionDate ();
	 *  $date3->placeConditionDate ();
	 *
	 *  echo $date1->from; // 01-01-2012
	 *  echo $date1->to;   // 31-12-2012
	 *
	 *  echo $date2->from; // 01-02-2012
	 *  echo $date2->to;   // 28-02-2012
	 *
	 *  echo $date3->from; // 14-02-2012
	 *  echo $date3->to;   // 14-02-2012
	 *
	 *  You can use
	 *  echo $date3->getFrom ();
	 *  echo $date3->getTo ();
	 */
	public function placeConditionDate () {
		if (!is_null($this->year)) {
			$this->from = $this->year;
			$this->to   = $this->year;
			if (!is_null($this->month)) {
				if (!is_null($this->day)) {
					$this->from .= '-'.$this->month.'-'.$this->day;
					$this->to   .= '-'.$this->month.'-'.$this->day;
				} else {
					$this->from .= '-'.$this->month.'-01';
					if ($this->month == 12) {
						$this->to .= '-12-31';
					} else
						$this->to .= '-'.($this->month + 1).'-0';
				}
			} else {
				$this->from .= '-01-01';
				$this->to   .= '-12-31';
			}
		}
		$this->from = date ('Y-m-d', strtotime ($this->from));
		$this->to   = date ('Y-m-d', strtotime ($this->to));
	}

	/**
	 * Check is year leap
	 *
	 * @return bool
	*/
	public function isLeapYear () {
		return (($this->year % 4 == 0 and $this->year % 100 != 0) or ($this->year % 400 == 0));
	}

	/**
	 * Return day of week name
	 *
	 * @return string
	*/
	public function getWeekDay () {
		return $this->weekdayNames [date("w", strtotime($this->date))];
	}

	/**
	 * Return russian name of month at the generic case
	 *
	 * @return string
	*/
	public function getMonthName () {
		return $this->monthNames [(int) date("m", strtotime($this->date))];
	}
} 