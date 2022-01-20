<?php


namespace Src\BrokerAPI\Alor;

use DateTime;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Src\BrokerAPI\Alor\Interfaces\AlorConstants;
use Src\BrokerAPI\Alor\Interfaces\AlorOrderInterface;

class AlorOrder implements AlorConstants, AlorOrderInterface
{
    /**
     * @const string
     */
    const COMMANDAPI_PREFIX = '/commandapi';

    const COMMANDAPI_USAGE = [
        self::ORDER_TYPE_MARKET           => true,
        self::ORDER_TYPE_LIMIT            => true,
        self::ORDER_TYPE_STOP             => false,
        self::ORDER_TYPE_STOP_LIMIT       => false,
        self::ORDER_TYPE_STOPLOSS         => false,
        self::ORDER_TYPE_STOPLOSS_LIMIT   => false,
        self::ORDER_TYPE_TAKEPROFIT       => false,
        self::ORDER_TYPE_TAKEPROFIT_LIMIT => false,
    ];

    const
        ORDER_TYPES_WITH_TRIGGER_PRICE = [
            self::ORDER_TYPE_STOP,
            self::ORDER_TYPE_STOP_LIMIT,
            self::ORDER_TYPE_STOPLOSS,
            self::ORDER_TYPE_STOPLOSS_LIMIT,
            self::ORDER_TYPE_TAKEPROFIT_LIMIT,
            self::ORDER_TYPE_TAKEPROFIT,
        ],
        ORDER_TYPES_WITH_PRICE = [
            self::ORDER_TYPE_LIMIT,
            self::ORDER_TYPE_STOP_LIMIT,
            self::ORDER_TYPE_TAKEPROFIT_LIMIT,
        ],
        ORDER_TYPES_WITH_END_UNIX_TIME = [
            self::ORDER_TYPE_STOP,
            self::ORDER_TYPE_STOPLOSS_LIMIT,
            self::ORDER_TYPE_TAKEPROFIT,
            self::ORDER_TYPE_TAKEPROFIT_LIMIT
        ];

    /**
     * @var string
     */
    protected $requestId;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var AlorClient
     */
    protected $client;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $side;

    /**
     * @var float
     */
    protected $triggerPrice;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var AlorInstrument
     */
    protected $instrument;

    /**
     * @var AlorUser
     */
    protected $user;

    /**
     * @var int|mixed
     */
    protected $orderEndUnixTime;

    /**
     * @var bool
     */
    protected $commandapi;

    /**
     * @var string
     */
    protected $responseMessage;

    /**
     * @var string
     */
    protected $tradeServerCode;

    /**
     * Конструктор AlorOrder
     *
     * @param AlorInstrument $instrument
     * @param AlorUser $user
     * @param AlorClient $alorApiClient
     * @param $orderEndUnixTime
     */
    public function __construct(
        AlorInstrument $instrument,
        AlorUser $user,
        AlorClient $alorApiClient,
        $orderEndUnixTime = 0,
        $tradeServerCode = self::SERVER_CODE_TRADE
    ) {
        $this->instrument = $instrument;
        $this->user = $user;
        $this->client = $alorApiClient;
        $this->orderEndUnixTime = $orderEndUnixTime;
        $this->commandapi = false;
        $this->tradeServerCode = $tradeServerCode;
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return AlorInstrument
     */
    public function getInstrument(): AlorInstrument
    {
        return $this->instrument;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getSide(): string
    {
        return $this->side;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return is_null($this->price) ? 0 : $this->price;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    /**
     * @return string
     */
    private function getCommandApi()
    {
        return self::COMMANDAPI_USAGE[$this->type]
            ? self::COMMANDAPI_PREFIX
            : '';
    }

    /**
     * Генерация уникальной строки для идентификации запроса
     *
     * @param string $portfolio
     * @return string
     */
    public function getAlorReqId($portfolio): string
    {
        return $portfolio . ';' . rand(1000, 9999) . '-' . substr(hash('sha256', uniqid()), 0 , 5);
    }

    /**
     * Ордер на покупку
     *
     * @param int $quantity
     * @return $this
     */
    public function buy($quantity)
    {
        $this->quantity = $quantity;
        $this->side = self::SIDE_BUY;
        return $this;
    }

    /**
     * Ордер на продажу
     *
     * @param int $quantity
     * @return $this
     */
    public function sell($quantity)
    {
        $this->quantity = $quantity;
        $this->side = self::SIDE_SELL;
        return $this;
    }

    /**
     * Получение в виде массива
     *
     * @return array
     */
    public function asArray(): array
    {
        $data = [];
        $data['quantity'] = $this->quantity;
        $data['side'] = $this->side;

        if ($this->type != self::ORDER_TYPE_TAKEPROFIT) {
            $data['type'] = $this->type;
        }

        if (in_array($this->type, self::ORDER_TYPES_WITH_TRIGGER_PRICE)) {
            $data['triggerPrice'] = $this->triggerPrice;
        }

        if (in_array($this->type, self::ORDER_TYPES_WITH_PRICE)) {
            $data['price'] = $this->price;
        }

        $data['instrument'] = $this->instrument->asArray();
        $data['user'] = $this->user->asArray();

        if (in_array($this->type, self::ORDER_TYPES_WITH_END_UNIX_TIME)) {
            $data['orderEndUnixTime'] = $this->orderEndUnixTime;
        }

        return $data;
    }

    /**
     * Получение в виде json-объекта
     *
     * @return false|string
     */
    public function asJSON()
    {
        return json_encode($this->asArray(), JSON_FORCE_OBJECT);
    }

    /**
     * Отправка ордера
     *
     * @var string $errorMessage
     * @return bool
     * @throws Exception
     */
    public function send(): bool
    {
        $orderType = $this->type;
        $tradeServerCode = $this->tradeServerCode;
        $this->requestId = $this->getAlorReqId($this->user->portfolio);
        $commandapi = $this->getCommandApi();
        $client = $this->client;

        try {
            $client->sendRequest(
                "$commandapi/warptrans/$tradeServerCode/v2/client/orders/actions/$orderType",
                [
                    'headers' => [
                        'Content-Type' => self::CONTENT_TYPE_JSON,
                        'X-ALOR-REQID' => $this->requestId,
                    ],
                    'body' => $this->asJSON(),
                ],
                true,
                self::REQUEST_TYPE_POST
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $this->responseMessage = '';

            if ($response->getStatusCode() == Httpstatuscodes::HTTP_UNAUTHORIZED) {
                $this->responseMessage = $response->getStatusCode() . ' ' . $response->getReasonPhrase();
                return false;
            }

            if ($respArr = AlorClient::getResponseBodyAsArray($response->getBody())) {
                foreach ($respArr as $key => $value) {
                    $this->responseMessage .= empty($this->responseMessage) ? '' : PHP_EOL;
                    $this->responseMessage .= "$key : $value";
                }
            } else if ($respStr = $response->getReasonPhrase()) {
                $this->responseMessage = $respStr;
            }

            return false;
        } catch (Exception $e) {
            $this->responseMessage = $e->getMessage();
            return false;
        }

        if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_OK)) {
            $result = $client->getResponseAsArray();

            if (!empty($result['orderNumber'])) {
                $this->orderId = (int) $result['orderNumber'];
            }

            if (!empty($result['message'])) {
                $this->responseMessage = $result['message'];
                preg_match('/{(.*)}/', $this->responseMessage,$matches);

                if (isset($matches[1])) {
                    $this->orderEndUnixTime = DateTime::createFromFormat('d.m.y H:i:s', $matches[1])->getTimestamp();
                }
            }

            return true;
        } else if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_BAD_REQUEST)) {
            $result = $client->getResponseAsArray();

            if (!empty($result['message'])) {
                $this->responseMessage = $result['message'];
            } else {
                $this->responseMessage = $client->getResponseAsString();
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * Изменение ордера
     *
     * @return bool
     * @throws Exception
     */
    public function change(): bool
    {
        if (empty($this->orderId)) {
            throw new Exception('Can\'t change order - id not set!');
        }

        $orderType = $this->type;
        $tradeServerCode = $this->tradeServerCode;
        $this->requestId = $this->getAlorReqId($this->user->portfolio);
        $orderId = $this->orderId;
        $commandapi = $this->getCommandApi();
        $client = $this->client;

        try {
            $client->sendRequest(
                "$commandapi/warptrans/$tradeServerCode/v2/client/orders/actions/$orderType/$orderId",
                [
                    'headers' => [
                        'Content-Type' => self::CONTENT_TYPE_JSON,
                        'X-ALOR-REQID' => $this->requestId,
                    ],
                    'body' => $this->asJSON(),
                ],
                true,
                self::REQUEST_TYPE_PUT
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $respArr = AlorClient::getResponseBodyAsArray($response->getBody());
            $this->responseMessage = '';

            foreach ($respArr as $key => $value) {
                $this->responseMessage .= empty($this->responseMessage) ? '' : PHP_EOL;
                $this->responseMessage .= "$key : $value";
            }

            return false;
        } catch (Exception $e) {
            $this->responseMessage = $e->getMessage();
            return false;
        }

        if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_OK)) {
            $result = $client->getResponseAsArray();

            if (!empty($result['orderNumber'])) {
                $this->orderId = (int) $result['orderNumber'];
            }

            if (!empty($result['message'])) {
                $this->responseMessage = $result['message'];
                preg_match('/{(.*)}/', $this->responseMessage,$matches);

                if (isset($matches[1])) {
                    $this->orderEndUnixTime = DateTime::createFromFormat('d.m.y H:i:s', $matches[1])->getTimestamp();
                }
            }

            return true;
        } else if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_BAD_REQUEST)) {
            $result = $client->getResponseAsArray();

            if (!empty($result['message'])) {
                $this->responseMessage = $result['message'];
            } else {
                $this->responseMessage = $client->getResponseAsString();
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * Удаление ордера
     *
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        if (empty($this->orderId)) {
            throw new Exception('Can\'t delete order - id not set!');
        }

        $tradeServerCode = $this->tradeServerCode;
        $this->requestId = $this->getAlorReqId($this->user->portfolio);
        $orderId = $this->orderId;

        if (in_array($this->type, [self::ORDER_TYPE_MARKET, self::ORDER_TYPE_LIMIT])) {
            $commandapi = self::COMMANDAPI_PREFIX;
            $options = [
                'query' => [
                    'account' => $this->user->account,
                    'portfolio' => $this->user->portfolio,
                    'exchange' => $this->instrument->exchange,
                    'stop' => 'false',
                    'format' => self::FORMAT_SIMPLE,
                ]
            ];
        } else {
            $commandapi =  '';
            $options = [
                'query' => [
                    'portfolio' => $this->user->portfolio,
                    'stop' => 'true',
                    'X-ALOR-REQID' => $this->requestId,
                ]
            ];
        }

        $client = $this->client;

        try {
            $this->client->sendRequest(
                "$commandapi/warptrans/$tradeServerCode/v2/client/orders/$orderId",
                $options,
                true,
                self::REQUEST_TYPE_DELETE
            );
        } catch (Exception $e) {
            $response = $e->getResponse();
            $respArr = AlorClient::getResponseBodyAsArray($response->getBody());
            $this->responseMessage = '';

            foreach ($respArr as $key => $value) {
                $this->responseMessage .= empty($this->responseMessage) ? '' : PHP_EOL;
                $this->responseMessage .= "$key : $value";
            }

            return false;
        }

        if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_OK)) {
            $this->responseMessage = $client->getResponseAsString();
            return true;
        } else if ($client->isResponseStatusCode(Httpstatuscodes::HTTP_BAD_REQUEST)) {
            $this->responseMessage = $client->getResponseAsString();
            return false;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getPortfolio()
    {
        return $this->user->portfolio;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "AlorOrder:
requestId = {$this->requestId}
orderId = {$this->orderId}
type = {$this->type}
quantity = {$this->quantity}
side = {$this->side}
triggerPrice = {$this->triggerPrice}
price = {$this->price}
instrument = {$this->instrument}
orderEndUnixTime = {$this->orderEndUnixTime}
        ";
    }
}
