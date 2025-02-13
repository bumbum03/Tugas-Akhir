<?php 

namespace App\Services\Midtrans;

use Midtrans\Config;

class Midtrans
{
    protected $serverKey;
    protected $isProduction;

    public function __construct(){
        $this->serverKey = config('MIDTRANS_SERVER_KEY');
        $this->isProduction = false;

        $this->_configureMidtrans();
    }

    public function _configureMidtrans(){
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = false;
    }
}