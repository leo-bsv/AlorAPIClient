<?php


namespace Src\BrokerAPI\Alor;


class MarketOrder extends AlorOrder
{
    /**
     * Конструктор MarketOrder
     *
     * @param AlorInstrument $instrument
     * @param AlorUser $user
     * @param AlorClient $alorApiClient
     * @param int $orderEndUnixTime
     */
    public function __construct(
        AlorInstrument $instrument,
        AlorUser $user,
        AlorClient $alorApiClient,
        $orderEndUnixTime = 0,
        $tradeServerCode = self::SERVER_CODE_TRADE
    ) {
        parent::__construct($instrument, $user, $alorApiClient, $orderEndUnixTime, $tradeServerCode);
        $this->type = self::ORDER_TYPE_MARKET;
        $this->commandapi = true;
    }
}
