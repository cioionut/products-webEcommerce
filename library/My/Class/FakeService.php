<?php

class My_Class_FakeService implements Zend_Currency_CurrencyInterface
{
    public function getRate($from, $to)
    {
        return 1;
    }
}