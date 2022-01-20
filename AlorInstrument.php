<?php


namespace Src\BrokerAPI\Alor;

use Src\BrokerAPI\Alor\Interfaces\AlorConstants;

class AlorInstrument
{
    /**
     * @var string
     */
    public $ticker;

    /**
     * @var string
     */
    public $exchange;

    /**
     * Конструктор AlorInstrument
     *
     * @param string $ticker
     * @param string $exchange
     */
    public function __construct(
        $ticker,
        $exchange = AlorConstants::EXCHANGE_DEFAULT
    ) {
        $this->ticker = $ticker;
        $this->exchange = $exchange;
    }

    /**
     * Получение в виде массива
     *
     * @return array
     */
    public function asArray()
    {
        return [
            'symbol' => $this->ticker,
            'exchange' => $this->exchange,
        ];
    }

    /**
     * Получение в виде строки
     *
     * @return string
     */
    public function __toString()
    {
        return $this->exchange . ':' . $this->ticker;
    }
}
