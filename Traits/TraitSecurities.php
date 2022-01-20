<?php


namespace Src\BrokerAPI\Alor\Traits;

use Exception;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Src\BrokerAPI\Alor\AlorInstrument;

trait TraitSecurities
{
    /**
     * Запрос информации о торговых инструментах
     *
     * @param string $query (ticker)
     * @param string $sector
     * @param int $limit
     * @param string $cficode
     * @param string $exchange
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getSecurities(
        $query,
        $limit = 3,
        $sector = null,
        $cficode = null,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $params = [
            'query' => [
                'query' => $query,
                'limit' => $limit,
                'exchange' => $exchange,
                'format' => $format,
            ],
        ];

        if ($sector) $params['query']['sector'] = $sector;
        if ($cficode) $params['query']['cficode'] = $cficode;

        $this->sendRequest('/md/v2/securities', $params);

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации об инструментах на выбранной бирже
     *
     * @param string $exchange
     * @return array
     * @throws Exception
     */
    public function getSecuritiesByExchange(
        $exchange = self::EXCHANGE_DEFAULT
    ): array {
        $this->sendRequest(
            "/md/v2/securities/$exchange"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос информации о выбранном финансовом инструменте на бирже
     *
     * @param string $ticker
     * @param string $exchange
     * @return array
     * @throws Exception
     */
    public function getSecuritiesByTicker(
        $ticker,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/securities/$exchange/$ticker",
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
     * Запрос информации о котировках для выбранных инструментов и бирж
     *
     * @param AlorInstrument[] $symbols
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getQuotes(
        array $symbols,
        $format = self::FORMAT_SIMPLE
    ): array {
        $symbols = implode(',', $symbols);

        $this->sendRequest(
            "/md/v2/securities/$symbols/quotes",
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
     * Запрос биржевого стакана
     *
     * @param string $ticker
     * @param string $exchange
     * @param int $depth
     * @param string $format
     * @return array
     */
    public function getOrderbooks(
        $ticker,
        $exchange = self::EXCHANGE_DEFAULT,
        $depth = 20,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/orderbooks/$exchange/$ticker",
            [
                'query' => [
                    'depth' => (int) $depth,
                    'format' => $format,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запросить данные о всех сделках (лента) по ценным бумагам за сегодняшний день
     *
     * @param string $ticker
     * @param int $timeFrom
     * @param int $timeTo
     * @param string $exchange
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getAllTrades(
        $ticker,
        $timeFrom,
        $timeTo,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/securities/$exchange/$ticker/alltrades",
            [
                'query' => [
                    'format' => $format,
                    'from' => (int) $timeFrom,
                    'to' => (int) $timeTo,
                ],
            ]
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * Запрос котировки по ближайшему фьючерсу (только по коду, без даты)
     *
     * @param string $ticker
     * @param string $exchange
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getActualFuturesQuote(
        $ticker,
        $exchange = self::EXCHANGE_DEFAULT,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            "/md/v2/securities/$exchange/$ticker/actualFuturesQuote",
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
     * Запрос истории рынка для выбранных биржи и финансового инструмента.
     * Данные имеют задержку в 15 минут, если запрос не авторизован.
     * Для авторизованных клиентов задержка не применяется.
     *
     * @param string $ticker
     * @param int $timeFrom unixtime
     * @param int $timeTo unixtime
     * @param string $exchange
     * @param int $tf
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getHistory(
        $ticker,
        $timeFrom,
        $timeTo,
        $exchange = self::EXCHANGE_DEFAULT,
        $tf = self::TF_1_MIN,
        $format = self::FORMAT_SIMPLE
    ): array {
        $this->sendRequest(
            '/md/v2/history',
            [
                'query' => [
                    'symbol' => $ticker,
                    'exchange' => $exchange,
                    'tf' => $tf,
                    'from' => $timeFrom,
                    'to' => $timeTo,
                    'format' => $format,
                ]
            ],
            false
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }

    /**
     * @deprecated
     * Запрос информации о лоте для выбранных биржи и финансового инструмента
     *
     * @param string $ticker
     * @param string $exchange
     * @return array
     * @throws Exception
     */
    public function getInfo(
        $ticker,
        $exchange = self::EXCHANGE_DEFAULT
    ): array {
        $this->sendRequest(
            "/md/securities/$exchange/$ticker"
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? $this->getResponseAsArray()
            : [];
    }
}
