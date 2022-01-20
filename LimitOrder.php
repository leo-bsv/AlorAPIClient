<?php


namespace Src\BrokerAPI\Alor;


use Src\BrokerAPI\Alor\Traits\TraitPrice;

class LimitOrder extends AlorOrder
{
    use TraitPrice;

    /**
     * Конструктор LimitOrder
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
        $orderEndUnixTime = 0
    ) {
        parent::__construct($instrument, $user, $alorApiClient, $orderEndUnixTime);
        $this->type = self::ORDER_TYPE_LIMIT;
        $this->commandapi = true;
    }
}
