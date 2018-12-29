<?php

namespace GYL\CuentaCorrienteBundle\Twig;

/**
 * Description of CuentaCorrienteExtension
 *
 * @author Emiliano Abarca
 * created 25/09/2017
 * 
 */
class CuentaCorrienteExtension extends \Twig_Extension {

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'twig_extension';
    }

    /**
     * 
     * @return type 
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('money_format', array($this, 'moneyFormat')),
            new \Twig_SimpleFilter('currency_format', array($this, 'currencyFormat')),
        );
    }

    /**
     * 
     * @param type $number
     * @param type $simbol
     * @param type $exchageRate
     * @param type $decimals
     * @param type $decPoint
     * @param type $thousandsSep
     * @return string
     */
    public function moneyFormat($number, $simbol = '$', $exchageRate = 1, $decimals = 2, $decPoint = ',', $thousandsSep = '.') {

        if ($number >= 0){
            $valued = $number * $exchageRate;
            $price = number_format($valued, $decimals, $decPoint, $thousandsSep);
            $price = $simbol . ' ' . $price;
        }else{
            $number *= -1;
            $valued = $number * $exchageRate;
            $price = number_format($valued, $decimals, $decPoint, $thousandsSep);
            $price = $simbol . ' ' . $price . ' - ';
        }
        
        return $price;
    }

    /**
     * 
     * @param type $number
     * @param type $decimals
     * @param type $decPoint
     * @param type $thousandsSep
     * @return type
     */
    public function currencyFormat($number, $decimals = 2, $decPoint = ',', $thousandsSep = '.') {

        $currency = number_format($number, $decimals, $decPoint, $thousandsSep);

        return $currency;
    }
}
