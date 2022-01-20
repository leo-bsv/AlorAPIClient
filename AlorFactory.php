<?php


namespace Src\BrokerAPI\Alor;

/**
 * Class AlorFactory
 *
 * Фабрика ордера Алор с обязательной фиксацией
 * параметров через параметры методов создания.
 */
class AlorFactory
{
    /**
     * @var AlorInstrument
     */
    private $instrument;

    /**
     * @var AlorUser
     */
    private $user;

    /**
     * @var AlorClient
     */
    private $client;

    /**
     * @param AlorClient $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(AlorClient $client) {
        $this->client = $client;
    }

    /**
     * @param AlorUser $user
     * @return $this
     */
    public function setUser(AlorUser $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param AlorInstrument $instrument
     */
    public function setInstrument(AlorInstrument $instrument)
    {
        $this->instrument = $instrument;
    }

    /**
     * @return  AlorInstrument
     */
    public function getInstrument(): AlorInstrument
    {
        return $this->instrument;
    }

    /**
     * Инстанс базового ордера
     *
     * @var string $tradeServerCode
     * @return AlorOrder
     */
    public function getOrderInstance($tradeServerCode = AlorOrder::SERVER_CODE_TRADE)
    {
        $order = new AlorOrder($this->instrument, $this->user, $this->client, 0, $tradeServerCode);
        return $order;
    }

    /**
     * Лимитный ордер на покупку
     *
     * @param int $quantity
     * @param float $price
     * @return LimitOrder
     */
    public function limitBuy($quantity, $price)
    {
        $order = new LimitOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity)->byPrice($price);
    }

    /**
     * Лимитный ордер на продажу
     *
     * @param int $quantity
     * @param float $price
     * @return LimitOrder
     */
    public function limitSell($quantity, $price)
    {
        $order = new LimitOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity)->byPrice($price);
    }

    /**
     * Лимитный стоп-ордер на покупку
     *
     * @param int $quantity
     * @param float $price
     * @param float $triggerPrice
     * @return StopLimitOrder
     */
    public function stopLimitBuy($quantity, $price, $triggerPrice)
    {
        $order = new StopLimitOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity)->byPrice($price)->whenPrice($triggerPrice);
    }

    /**
     * Лимитный стоп-ордер на продажу
     *
     * @param int $quantity
     * @param float $price
     * @param float $triggerPrice
     * @return StopLimitOrder
     */
    public function stopLimitSell($quantity, $price, $triggerPrice)
    {
        $order = new StopLimitOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity)->byPrice($price)->whenPrice($triggerPrice);
    }

    /**
     * Лимитный тейк-профит ордер на покупку
     *
     * @param int $quantity
     * @param float $price
     * @param float $triggerPrice
     * @return TakeProfitLimitOrder
     */
    public function takeProfitLimitBuy($quantity, $price, $triggerPrice)
    {
        $order = new TakeProfitLimitOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity)->byPrice($price)->whenPrice($triggerPrice);
    }

    /**
     * Лимитный тейк-профит ордер на продажу
     *
     * @param int $quantity
     * @param float $price
     * @param float $triggerPrice
     * @return TakeProfitLimitOrder
     */
    public function takeProfitLimitSell($quantity, $price, $triggerPrice)
    {
        $order = new TakeProfitLimitOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity)->byPrice($price)->whenPrice($triggerPrice);
    }

    /**
     * Рыночный ордер на покупку
     *
     * @param int $quantity
     * @return MarketOrder
     */
    public function marketBuy($quantity)
    {
        $order = new MarketOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity);
    }

    /**
     * Рыночный ордер на продажу
     *
     * @param int $quantity
     * @return MarketOrder
     */
    public function marketSell($quantity)
    {
        $order = new MarketOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity);
    }

    /**
     * Cтоп-ордер на покупку
     *
     * @param int $quantity
     * @param float $triggerPrice
     * @return StopOrder
     */
    public function stopBuy($quantity, $triggerPrice)
    {
        $order = new StopOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity)->whenPrice($triggerPrice);
    }

    /**
     * Cтоп-ордер на продажу
     *
     * @param int $quantity
     * @param float $triggerPrice
     * @return StopOrder
     */
    public function stopSell($quantity, $triggerPrice)
    {
        $order = new StopOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity)->whenPrice($triggerPrice);
    }

    /**
     * Рыночный тейк-профит ордер на покупку
     *
     * @param int $quantity
     * @param float $triggerPrice
     * @return TakeProfitMarketOrder
     */
    public function takeProfitMarketBuy($quantity, $triggerPrice)
    {
        $order = new TakeProfitMarketOrder($this->instrument, $this->user, $this->client);
        return $order->buy($quantity)->whenPrice($triggerPrice);
    }

    /**
     * Рыночный тейк-профит ордер на продажу
     *
     * @param int $quantity
     * @param float $triggerPrice
     * @return TakeProfitMarketOrder
     */
    public function takeProfitMarketSell($quantity, $triggerPrice)
    {
        $order = new TakeProfitMarketOrder($this->instrument, $this->user, $this->client);
        return $order->sell($quantity)->whenPrice($triggerPrice);
    }
}
