<?php


namespace Src\BrokerAPI\Alor\Traits;


trait TraitTriggerPrice
{
    /**
     * Цена активации ордера (лимитный выставляется по цене price, не triggerPrice, рыночный - по рынку)
     *
     * @param $price
     * @return $this
     */
    public function whenPrice($price)
    {
        $this->triggerPrice = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getTriggerPrice()
    {
        return $this->triggerPrice;
    }
}
