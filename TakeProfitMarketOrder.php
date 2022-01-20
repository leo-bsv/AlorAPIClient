<?php


namespace Src\BrokerAPI\Alor;


use Src\BrokerAPI\Alor\Traits\TraitTriggerPrice;

class TakeProfitMarketOrder extends AlorOrder
{
    use TraitTriggerPrice;

    /**
     * Конструктор TakeProfitMarketOrder
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
        $tradeServerCode = self::SERVER_CODE_FUT1
    ) {
        parent::__construct($instrument, $user, $alorApiClient, $orderEndUnixTime, $tradeServerCode);
        $this->type = self::ORDER_TYPE_TAKEPROFIT;
    }
}
