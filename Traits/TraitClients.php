<?php


namespace Src\BrokerAPI\Alor\Traits;


use Exception;
use Lukasoppermann\Httpstatus\Httpstatuscodes;

trait TraitClients
{
    /**
     * Получение списка серверов.
     * В ответе в поле tradeServerCode содержится значение которое надо использовать.
     *
     * @return array
     * @throws Exception
     */
    public function getPortfolios(): array {
        $this->sendRequest(
            "/client/v1.0/users/{$this->username}/portfolios"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о всех заявках
     *
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getOrders(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/orders",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о выбранной заявке
     *
     * @param string $orderId
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getOrder(
        $orderId,
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/orders/$orderId",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о всех стоп-заявках
     *
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getStopOrders(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/stoporders",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о выбранной стоп-заявке
     *
     * @param string $stopOrderId
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getStopOrder(
        $stopOrderId,
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/stoporders/$stopOrderId",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос сводной информации
     *
     * @param string $portfolio
     * @param string $exchange
     * @return array
     */
    public function getSummary(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/summary"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о позициях
     *
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getPositions(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/positions",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о позициях для конкретного инструмента
     *
     * @param string $ticker
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getPositionsByTicker(
        $ticker,
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/positions/$ticker",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о сделках
     *
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getTrades(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/trades",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о сделках для конкретного тикера
     *
     * @param string $ticker
     * @param string $portfolio
     * @param string $exchange
     * @param string $format
     * @return array
     */
    public function getTradesByTicker(
        $ticker,
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/$ticker/trades",
            [
                'query' => [
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о рисках FORTS
     *
     * @param string $portfolio
     * @param string $exchange
     * @return array
     */
    public function getFortsRisk(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/fortsrisk"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о рисках
     *
     * @param string $portfolio
     * @param string $exchange
     * @return array
     */
    public function getRisk(
        $portfolio,
        $exchange = self::EXCHANGE_DEFAULT
    ): array {
        $this->sendRequest(
            "/md/v2/clients/$exchange/$portfolio/risk"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }
}
