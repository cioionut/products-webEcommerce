<?php

class My_Class_ExchangeService implements Zend_Currency_CurrencyInterface
{
    public function getRate($from, $to)
    {
        $currencyMapper = new Application_Model_CurrencyMapper();
        if(!$to) $to = $currencyMapper->getDefaultCurrency()->code;
        $rate_from = $currencyMapper->getDbTable()->fetchRow($currencyMapper->getDbTable()->select()->where('code = ?', $from))->rate;
        $rate_to = $currencyMapper->getDbTable()->fetchRow($currencyMapper->getDbTable()->select()->where('code = ?', $to))->rate;
        if(!($rate_from || $rate_to)) new Exception('Currency Code is not in database');
        return $rate_to/$rate_from;
    }
}