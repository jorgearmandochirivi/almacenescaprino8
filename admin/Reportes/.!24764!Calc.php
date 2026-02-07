<?php

// The constant telling us what day starts the week. Monday (1) is the
// international standard. Redefine this to 0 if you want weeks to
// begin on Sunday.
define('DATE_CALC_BEGIN_WEEKDAY', 1);

/**
 * Date_Calc is a calendar class used to calculate and
 * manipulate calendar dates and retrieve dates in a calendar
 * format. It does not rely on 32-bit system date stamps, so
 * you can display calendars and compare dates that date
 * pre 1970 and post 2038.
 *
 * This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available at through the world-wide-web at
 * http://www.php.net/license/2_02.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * Copyright (c) 1999, 2000 ispi
 *
 * @access public
 *
 * @version 1.2.4
 * @author Monte Ohrt <monte@ispi.net>
 */

class Date_Calc
{
    /**
     * Returns the current local date. NOTE: This function
     * retrieves the local date using strftime(), which may
     * or may not be 32-bit safe on your system.
     *
     * @param string the strftime() format to return the date
     *
     * @access public
     *
     * @return string the current date in specified format
     */

    function dateNow($format="%Y%m%d")
    {
        return(strftime($format,time()));

    } // end func dateNow

     /**
     * Returns true for valid date, false for invalid date.
     *
     * @param string year in format CCYY
     * @param string month in format MM
     * @param string day in format DD
     *
     * @access public
     *
     * @return boolean true/false
     */

    function isValidDate($day, $month, $year)
    {
        if($year < 0 || $year > 9999)
            return false;
        if(!checkdate($month,$day,$year))
            return false;

        return true;
    } // end func isValidDate

     /**
     * Returns true for a leap year, else false
     *
     * @param string year in format CCYY
     *
     * @access public
     *
     * @return boolean true/false
     */

    function isLeapYear($year="")
    {

        if(empty($year))
            $year = Date_Calc::dateNow("%Y");

        if(strlen($year) != 4)
            return false;

        if(preg_match("/\D/",$year))
            return false;

        return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);

    } // end func isLeapYear

    /**
     * Determines if given date is a future date from now.
     *
     * @param string year in format CCYY
     * @param string month in format MM
     * @param string day in format DD
     *
     * @access public
     *
     * @return boolean true/false
     */

    function isFutureDate($day,$month,$year)
    {
        $this_year = Date_Calc::dateNow("%Y");
        $this_month = Date_Calc::dateNow("%m");
        $this_day = Date_Calc::dateNow("%d");


        if($year > $this_year)
            return true;
        elseif($year == $this_year)
            if($month > $this_month)
                return true;
            elseif($month == $this_month)
                if($day > $this_day)
                    return true;

        return false;

    } // end func isFutureDate

    /**
     * Determines if given date is a past date from now.
     *
     * @param string year in format CCYY
     * @param string month in format MM
     * @param string day in format DD
     *
     * @access public
     *
     * @return boolean true/false
     */

    function isPastDate($day,$month,$year)
    {
        $this_year = Date_Calc::dateNow("%Y");
        $this_month = Date_Calc::dateNow("%m");
        $this_day = Date_Calc::dateNow("%d");


        if($year < $this_year)
            return true;
        elseif($year == $this_year)
            if($month < $this_month)
                return true;
            elseif($month == $this_month)
                if($day < $this_day)
                    return true;

        return false;

    } // end func isPastDate

    /**
     * Returns day of week for given date, 0=Sunday
     *
     * @param string year in format CCYY, default is current local year
     * @param string month in format MM, default is current local month
     * @param string day in format DD, default is current local day
     *
     * @access public
     *
     * @return int $weekday_number
     */

    function dayOfWeek($day="",$month="",$year="")
    {

        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        if($month > 2)
            $month -= 2;
        else
        {
            $month += 10;
            $year--;
        }

        $day =     ( floor((13 * $month - 1) / 5) +
                $day + ($year % 100) +
                floor(($year % 100) / 4) +
                floor(($year / 100) / 4) - 2 *
                floor($year / 100) + 77);

        $weekday_number = (($day - 7 * floor($day / 7)));

        return $weekday_number;

    } // end func dayOfWeek

    /**
     * Returns week of the year, first Sunday is first day of first week
     *
     * @param string day in format DD
     * @param string month in format MM
     * @param string year in format CCYY
     *
     * @access public
     *
     * @return integer $week_number
     */

    function weekOfYear($day,$month,$year)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $week_year = $year - 1501;
        $week_day = $week_year * 365 + floor($week_year / 4) - 29872 + 1
                - floor($week_year / 100) + floor(($week_year - 300) / 400);

        $week_number =
                ceil((Date_Calc::julianDate($day,$month,$year) + floor(($week_day + 4) % 7)) / 7);

        return $week_number;

    } // end func weekOfYear

    /**
     * Returns number of days since 31 December of year before given date.
     *
     * @param string year in format CCYY, default is current local year
     * @param string month in format MM, default is current local month
     * @param string day in format DD, default is current local day
     *
     * @access public
     *
     * @return int $julian
     */

    function julianDate($day="",$month="",$year="")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = array(0,31,59,90,120,151,181,212,243,273,304,334);

        $julian = ($days[$month - 1] + $day);

        if($month > 2 && Date_Calc::isLeapYear($year))
            $julian++;

        return($julian);

    } // end func julianDate

    /**
     * Returns quarter of the year for given date
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     *
     * @access public
     *
     * @return int $year_quarter
     */

    function quarterOfYear($day="",$month="",$year="")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $year_quarter = (intval(($month - 1) / 3 + 1));

        return $year_quarter;

    } // end func quarterOfYear

    /**
     * Returns date of begin of next month of given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfNextMonth($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        if($month < 12)
        {
            $month++;
            $day=1;
        }
        else
        {
            $year++;
            $month=1;
            $day=1;
        }

        return Date_Calc::dateFormat($day,$month,$year,$format);

    } // end func beginOfNextMonth

    /**
     * Returns date of the last day of next month of given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function endOfNextMonth($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");


        if($month < 12)
        {
            $month++;
        }
        else
        {
            $year++;
            $month=1;
        }

        $day = Date_Calc::daysInMonth($month,$year);

        return Date_Calc::dateFormat($day,$month,$year,$format);

    } // end func endOfNextMonth

    /**
     * Returns date of the first day of previous month of given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfPrevMonth($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        if($month > 1)
        {
            $month--;
            $day=1;
        }
        else
        {
            $year--;
            $month=12;
            $day=1;
        }
        
        return Date_Calc::dateFormat($day,$month,$year,$format);

    } // end func beginOfPrevMonth

    /**
     * Returns date of the last day of previous month for given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function endOfPrevMonth($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        if($month > 1)
        {
            $month--;
        }
        else
        {
            $year--;
            $month=12;
        }

        $day = Date_Calc::daysInMonth($month,$year);

        return Date_Calc::dateFormat($day,$month,$year,$format);

    } // end func endOfPrevMonth

    /**
     * Returns date of the next weekday of given date,
     * skipping from Friday to Monday.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     *
     *Habiles = 1 Devuelve la fecha sin los sabados ni los domingos
     */

    function nextWeekday($day="",$month="",$year="",$format="%Y%m%d", $Habiles = "0")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);
		if( $Habiles == 1 )
		{
	        if(Date_Calc::dayOfWeek($day,$month,$year) == 5)
	            $days += 3;
	        elseif(Date_Calc::dayOfWeek($day,$month,$year) == 6)
	            $days += 2;
	        else
	            $days += 1;
		}
		else
			$days += 1;
        return(Date_Calc::daysToDate($days,$format));

    } // end func nextWeekday

    /**
     * Returns date of the previous weekday,
     * skipping from Monday to Friday.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function prevWeekday($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);

        if(Date_Calc::dayOfWeek($day,$month,$year) == 1)
            $days -= 3;
        elseif(Date_Calc::dayOfWeek($day,$month,$year) == 0)
            $days -= 2;
        else
            $days -= 1;

        return(Date_Calc::daysToDate($days,$format));

    } // end func prevWeekday

    /**
     * Returns date of the next specific day of the week
     * from the given date.
     *
     * @param int day of week, 0=Sunday
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param boolean onOrAfter if true and days are same, returns current day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function nextDayOfWeek($dow,$day="",$month="",$year="",$format="%Y%m%d",$onOrAfter=false)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);
        $curr_weekday = Date_Calc::dayOfWeek($day,$month,$year);

        if($curr_weekday == $dow)
		{
			if(!$onOrAfter)
            	$days += 7;
		}
        elseif($curr_weekday > $dow)
            $days += 7 - ( $curr_weekday - $dow );
        else
            $days += $dow - $curr_weekday;

        return(Date_Calc::daysToDate($days,$format));

    } // end func nextDayOfWeek

    /**
     * Returns date of the previous specific day of the week
     * from the given date.
     *
     * @param int day of week, 0=Sunday
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param boolean onOrBefore if true and days are same, returns current day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function prevDayOfWeek($dow,$day="",$month="",$year="",$format="%Y%m%d",$onOrBefore=false)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);
        $curr_weekday = Date_Calc::dayOfWeek($day,$month,$year);

        if($curr_weekday == $dow)
		{
			if(!$onOrBefore)
            	$days -= 7;
		}
        elseif($curr_weekday < $dow)
            $days -= 7 - ( $dow - $curr_weekday );
        else
            $days -= $curr_weekday - $dow;

        return(Date_Calc::daysToDate($days,$format));

    } // end func prevDayOfWeek

    /**
     * Returns date of the next specific day of the week
     * on or before the given date.
     *
     * @param int day of week, 0=Sunday
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function nextDayOfWeekOnOrAfter($dow,$day="",$month="",$year="",$format="%Y%m%d")
    {
        return(Date_Calc::nextDayOfWeek($dow,$day="",$month="",$year="",$format="%Y%m%d",true));
    } // end func nextDayOfWeekOnOrAfter

    /**
     * Returns date of the previous specific day of the week
     * on or before the given date.
     *
     * @param int day of week, 0=Sunday
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function prevDayOfWeekOnOrBefore($dow,$day="",$month="",$year="",$format="%Y%m%d")
    {
        return(Date_Calc::prevDayOfWeek($dow,$day="",$month="",$year="",$format="%Y%m%d",true));

    } // end func prevDayOfWeekOnOrAfter

    /**
     * Returns date of day after given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function nextDay($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);

        return(Date_Calc::daysToDate($days + 1,$format));

    } // end func nextDay

    /**
     * Returns date of day before given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function prevDay($day="",$month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");

        $days = Date_Calc::dateToDays($day,$month,$year);

        return(Date_Calc::daysToDate($days - 1,$format));

    } // end func prevDay

    /**
     * Sets century for 2 digit year.
     * 51-99 is 19, else 20
     *
     * @param string 2 digit year
     *
     * @access public
     *
     * @return string 4 digit year
     */

    function defaultCentury($year)
    {
        if(strlen($year) == 1)
            $year = "0$year";
        if($year > 50)
            return( "19$year" );
        else
            return( "20$year" );

    } // end func defaultCentury

    /**
     * Returns number of days between two given dates.
     *
     * @param string year in format CCYY
     * @param string month in format MM
     * @param string day in format DD
     * @param string year in format CCYY
     * @param string month in format MM
     * @param string day in format DD
     *
     * @access public
     *
     * @return int absolute number of days between dates,
     *      -1 if there is an error.
     */

    function dateDiff($day1,$month1,$year1,$day2,$month2,$year2)
    {
        if(!Date_Calc::isValidDate($day1,$month1,$year1))
            return -1;
        if(!Date_Calc::isValidDate($day2,$month2,$year2))
            return -1;

        return(abs((Date_Calc::dateToDays($day1,$month1,$year1))
                    - (Date_Calc::dateToDays($day2,$month2,$year2))));

    } // end func dateDiff

    /**
    * Compares two dates
    *
    * @param string $day1   day in format DD
    * @param string $month1 month in format MM
    * @param string $year1  year in format CCYY
    * @param string $day2   day in format DD
    * @param string $month2 month in format MM
    * @param string $year2  year in format CCYY
    *
    * @access public
    * @return int 0 on equality, 1 if date 1 is greater, -1 if smaller
    */
    function compareDates($day1,$month1,$year1,$day2,$month2,$year2)
    {
        $ndays1 = Date_Calc::dateToDays($day1, $month1, $year1);
        $ndays2 = Date_Calc::dateToDays($day2, $month2, $year2);
        if ($ndays1 == $ndays2) {
            return 0;
        }
        return ($ndays1 > $ndays2) ? 1 : -1;
    }

    /**
     * Find the number of days in the given month.
     *
     * @param string month in format MM, default current local month
     *
     * @access public
     *
     * @return int number of days
     */

    function daysInMonth($month="",$year="")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");

        if($month == 2)
        {
            if(Date_Calc::isLeapYear($year))
                return 29;
            else
                return 28;
        }
        elseif($month == 4 or $month == 6 or $month == 9 or $month == 11)
            return 30;
        else
            return 31;
    } // end func daysInMonth

    /**
     * Returns the number of rows on a calendar month. Useful for
     * determining the number of rows when displaying a typical
     * month calendar.
     *
     * @param string month in format MM, default current local month
     * @param string year in format YYCC, default current local year
     *
     * @access public
     *
     * @return int number of weeks
     */

    function weeksInMonth($month="",$year="",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        if($fdow == 1)
        {

            if(Date_Calc::firstOfMonthWeekday($month,$year) == 0)
                $first_week_days = 1;
            else
                $first_week_days = 7 - (Date_Calc::firstOfMonthWeekday($month,$year) - 1);

        }
        else
            $first_week_days = 7 - Date_Calc::firstOfMonthWeekday($month,$year);

        return ceil(((Date_Calc::daysInMonth($month,$year) - $first_week_days) / 7) + 1);

    } // end func weeksInMonth

    /**
     * Find the day of the week for the first of the month of given date.
     *
     * @param string year in format CCYY, default to current local year
     * @param string month in format MM, default to current local month
     *
     * @access public
     *
     * @return int number of weekday for the first day, 0=Sunday
     */

    function firstOfMonthWeekday($month="",$year="")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");

        return(Date_Calc::dayOfWeek("01",$month,$year));

    } // end func firstOfMonthWeekday

    /**
     * Return date of first day of month of given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfMonth($month="",$year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");

        return(Date_Calc::dateFormat("01",$month,$year,$format));

    } // end of func beginOfMonth

    /**
     * Find the month day of the beginning of week for given date,
     * using DATE_CALC_BEGIN_WEEKDAY. (can return weekday of prev month.)
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfWeek($day="",$month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $this_weekday = Date_Calc::dayOfWeek($day,$month,$year);

        if($fdow == 1)
        {
            if($this_weekday == 0)
                $beginOfWeek = Date_Calc::dateToDays($day,$month,$year) - 6;
            else
                $beginOfWeek = Date_Calc::dateToDays($day,$month,$year)
                    - $this_weekday + 1;
        }
        else
                $beginOfWeek = (Date_Calc::dateToDays($day,$month,$year)
                    - $this_weekday);


       /*  $beginOfWeek = (Date_Calc::dateToDays($day,$month,$year)
            - ($this_weekday - $fdow)); */

        return(Date_Calc::daysToDate($beginOfWeek,$format));

    } // end of func beginOfWeek

    /**
     * Find the month day of the end of week for given date,
     * using DATE_CALC_BEGIN_WEEKDAY. (can return weekday
     * of following month.)
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function endOfWeek($day="",$month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $this_weekday = Date_Calc::dayOfWeek($day,$month,$year);

        $last_dayOfWeek = (Date_Calc::dateToDays($day,$month,$year)
            + (6 - $this_weekday + $fdow));

        return(Date_Calc::daysToDate($last_dayOfWeek,$format));

    } // end func endOfWeek

    /**
     * Find the month day of the beginning of week after given date,
     * using DATE_CALC_BEGIN_WEEKDAY. (can return weekday of prev month.)
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfNextWeek($day="",$month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $date = Date_Calc::daysToDate(Date_Calc::dateToDays($day+7,$month,$year),"%Y%m%d");

        $next_week_year = substr($date,0,4);
        $next_week_month = substr($date,4,2);
        $next_week_day = substr($date,6,2);

        $this_weekday = Date_Calc::dayOfWeek($next_week_day,$next_week_month,$next_week_year);

        $beginOfWeek = (Date_Calc::dateToDays($next_week_day,$next_week_month,$next_week_year)
            - ($this_weekday - $fdow));

        return(Date_Calc::daysToDate($beginOfWeek,$format));

    } // end func beginOfNextWeek

    /**
     * Find the month day of the beginning of week before given date,
     * using DATE_CALC_BEGIN_WEEKDAY. (can return weekday of prev month.)
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return string date in given format
     */

    function beginOfPrevWeek($day="",$month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $date = Date_Calc::daysToDate(Date_Calc::dateToDays($day-7,$month,$year),"%Y%m%d");

        $next_week_year = substr($date,0,4);
        $next_week_month = substr($date,4,2);
        $next_week_day = substr($date,6,2);

        $this_weekday = Date_Calc::dayOfWeek($next_week_day,$next_week_month,$next_week_year);

        $beginOfWeek = (Date_Calc::dateToDays($next_week_day,$next_week_month,$next_week_year)
            - ($this_weekday - $fdow));

        return(Date_Calc::daysToDate($beginOfWeek,$format));

    } // end func beginOfPrevWeek

    /**
     * Return an array with days in week
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string day in format DD, default current local day
     * @param string format for returned date
     *
     * @access public
     *
     * @return array $week[$weekday]
     */

    function getCalendarWeek($day="",$month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if(empty($day))
            $day = Date_Calc::dateNow("%d");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $week_array = array();

        // date for the column of week

        $curr_day = Date_Calc::beginOfWeek($day,$month,$year,"%E",$fdow);

            for($counter=0; $counter <= 6; $counter++)
            {
                $week_array[$counter] = utf8_encode(Date_Calc::daysToDate($curr_day,$format));
                $curr_day++;
            }

        return $week_array;

    } // end func getCalendarWeek

    /**
     * Return a set of arrays to construct a calendar month for
     * the given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string month in format MM, default current local month
     * @param string format for returned date
     *
     * @access public
     *
     * @return array $month[$row][$col]
     */

    function getCalendarMonth($month="",$year="",$format="%Y%m%d",$fdow=null)
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");
        if(empty($month))
            $month = Date_Calc::dateNow("%m");
        if($fdow === null)
            $fdow = DATE_CALC_BEGIN_WEEKDAY;

        $month_array = array();

        // date for the first row, first column of calendar month
        if($fdow == 1)
        {
            if(Date_Calc::firstOfMonthWeekday($month,$year) == 0)
                $curr_day = Date_Calc::dateToDays("01",$month,$year) - 6;
            else
                $curr_day = Date_Calc::dateToDays("01",$month,$year)
                    - Date_Calc::firstOfMonthWeekday($month,$year) + 1;
        }
        else
                $curr_day = (Date_Calc::dateToDays("01",$month,$year)
                    - Date_Calc::firstOfMonthWeekday($month,$year));

        // number of days in this month
        $daysInMonth = Date_Calc::daysInMonth($month,$year);

        $weeksInMonth = Date_Calc::weeksInMonth($month,$year,$fdow);
        for($row_counter=0; $row_counter < $weeksInMonth; $row_counter++)
        {
            for($column_counter=0; $column_counter <= 6; $column_counter++)
            {
                $month_array[$row_counter][$column_counter] = Date_Calc::daysToDate($curr_day,$format);
                $curr_day++;
            }
        }

        return $month_array;

    } // end func getCalendarMonth

    /**
     * Return a set of arrays to construct a calendar year for
     * the given date.
     *
     * @param string year in format CCYY, default current local year
     * @param string format for returned date
     *
     * @access public
     *
     * @return array $year[$month][$row][$col]
     */

    function getCalendarYear($year="",$format="%Y%m%d")
    {
        if(empty($year))
            $year = Date_Calc::dateNow("%Y");

        $year_array = array();

        for($curr_month=0; $curr_month <=11; $curr_month++)
            $year_array[$curr_month] = Date_Calc::getCalendarMonth(sprintf("%02d",$curr_month+1),$year,$format);

        return $year_array;

    } // end func getCalendarYear

    
    /************ Nuevas  ***************************************/
    
   /************************************* Funcion getCalendar Periodo ********************************************
    *	Funcion para retornar un array con el rango de fechas mes a mes de la periodicidad  seleccionada,		 *
    *	esta funcion solo aplica a periodicidades Bimestrales,Trimestrales y Semestrales.						 *
    *	Parametros:																								 *
    *	$periodo: Numero que hace referencia a la periodicidad que se utilizara, ej: 3 se utiliza para Trimestre *
    *	$month:	Numero del mes del actual periodo																 *
