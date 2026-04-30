<?php

namespace App\Helpers;

use Carbon\Carbon;

class Helper
{

    public static function getCurrentFinancialYearIndia()
    {
        $date = Carbon::now();

        if ($date->month >= 4) {
            $startYear = $date->year;
            $endYear   = $date->year + 1;
        } else {
            $startYear = $date->year - 1;
            $endYear   = $date->year;
        }

        return "{$startYear}-{$endYear}";
    }
    public static function getMonthColumn($integerValue)
    {
        //echo $integerValue;die;
        if (is_numeric($integerValue)) {
            $month_number = intval($integerValue);
            if ($month_number >= 1 && $month_number <= 12) {
                $month_val = $month_number;
            }
        } else {
            $month_number = date('n', strtotime($integerValue));
            $month_val = $month_number;
        }

        switch ($month_val) {
            case "1":
                $lot_column = 'jan_lot_no';
                $lot_status = 'jan_lot_status';
                $lot_type = 'jan_lot_type';
                $lot_eligible = 'jan_is_eligible';
                $lot_no = 'jan_lot_no';
                $lot_eligible_amount = 'jan_eligible_amount';
                $lot_payment_amount = 'jan_payment_amount';
                break;
            case "2":
                $lot_column = 'feb_lot_no';
                $lot_status = 'feb_lot_status';
                $lot_type = 'feb_lot_type';
                $lot_eligible = 'feb_is_eligible';
                $lot_no = 'feb_lot_no';
                $lot_eligible_amount = 'feb_eligible_amount';
                $lot_payment_amount = 'feb_payment_amount';
                break;
            case "3":
                $lot_column = 'mar_lot_no';
                $lot_status = 'mar_lot_status';
                $lot_type = 'mar_lot_type';
                $lot_eligible = 'mar_is_eligible';
                $lot_no = 'mar_lot_no';
                $lot_eligible_amount = 'mar_eligible_amount';
                $lot_payment_amount = 'mar_payment_amount';
                break;
            case "4":
                $lot_column = 'apr_lot_no';
                $lot_status = 'apr_lot_status';
                $lot_type = 'apr_lot_type';
                $lot_eligible = 'apr_is_eligible';
                $lot_no = 'apr_lot_no';
                $lot_eligible_amount = 'apr_eligible_amount';
                $lot_payment_amount = 'apr_payment_amount';
                break;
            case "5":
                $lot_column = 'may_lot_no';
                $lot_status = 'may_lot_status';
                $lot_type = 'may_lot_type';
                $lot_eligible = 'may_is_eligible';
                $lot_no = 'may_lot_no';
                $lot_eligible_amount = 'may_eligible_amount';
                $lot_payment_amount = 'may_payment_amount';
                break;
            case "6":
                $lot_column = 'jun_lot_no';
                $lot_status = 'jun_lot_status';
                $lot_type = 'jun_lot_type';
                $lot_eligible = 'jun_is_eligible';
                $lot_no = 'jun_lot_no';
                $lot_eligible_amount = 'jun_eligible_amount';
                $lot_payment_amount = 'jun_payment_amount';
                break;
            case "7":
                $lot_column = 'jul_lot_no';
                $lot_status = 'jul_lot_status';
                $lot_type = 'jul_lot_type';
                $lot_eligible = 'jul_is_eligible';
                $lot_no = 'jul_lot_no';
                $lot_eligible_amount = 'jul_eligible_amount';
                $lot_payment_amount = 'jul_payment_amount';
                break;
            case "8":
                $lot_column = 'aug_lot_no';
                $lot_status = 'aug_lot_status';
                $lot_type = 'aug_lot_type';
                $lot_eligible = 'aug_is_eligible';
                $lot_no = 'aug_lot_no';
                $lot_eligible_amount = 'aug_eligible_amount';
                $lot_payment_amount = 'aug_payment_amount';
                break;
            case "9":
                $lot_column = 'sep_lot_no';
                $lot_status = 'sep_lot_status';
                $lot_type = 'sep_lot_type';
                $lot_eligible = 'sep_is_eligible';
                $lot_no = 'sep_lot_no';
                $lot_eligible_amount = 'sep_eligible_amount';
                $lot_payment_amount = 'sep_payment_amount';
                break;
            case "10":
                $lot_column = 'oct_lot_no';
                $lot_status = 'oct_lot_status';
                $lot_type = 'oct_lot_type';
                $lot_eligible = 'oct_is_eligible';
                $lot_no = 'oct_lot_no';
                $lot_eligible_amount = 'oct_eligible_amount';
                $lot_payment_amount = 'oct_payment_amount';
                break;
            case "11":
                $lot_column = 'nov_lot_no';
                $lot_status = 'nov_lot_status';
                $lot_type = 'nov_lot_type';
                $lot_eligible = 'nov_is_eligible';
                $lot_no = 'nov_lot_no';
                $lot_eligible_amount = 'nov_eligible_amount';
                $lot_payment_amount = 'nov_payment_amount';
                break;
            case "12":
                $lot_column = 'dec_lot_no';
                $lot_status = 'dec_lot_status';
                $lot_type = 'dec_lot_type';
                $lot_eligible = 'dec_is_eligible';
                $lot_no = 'dec_lot_no';
                $lot_eligible_amount = 'dec_eligible_amount';
                $lot_payment_amount = 'dec_payment_amount';
                break;
            case "13":
                $lot_column = 'arrear_lot_no';
                $lot_status = 'arrear_lot_status';
                $lot_type = 'arrear_lot_type';
                $lot_eligible = 'arrear_is_eligible';
                $lot_eligible_amount = 'arrear_eligible_amount';
                $lot_payment_amount = 'arrear_payment_amount';
                break;
            default:
                $lot_column = '';
                $lot_status = '';
                $lot_type = '';
        }
        $response = array('lot_status' => $lot_status, 'lot_column' => $lot_column, 'lot_type' => $lot_type, 'lot_eligible' => $lot_eligible, 'lot_no' => $lot_no, 'lot_eligible_amount' => $lot_eligible_amount, 'lot_payment_amount' => $lot_payment_amount);
        return $response;
    }
}
