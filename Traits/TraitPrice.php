<?php


namespace Src\BrokerAPI\Alor\Traits;


trait TraitPrice
{
    /**
     * Цена ордера
     *
     * @param $price
     * @return $this
     */
    public function byPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}
