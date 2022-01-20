<?php


namespace Src\BrokerAPI\Alor;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Lukasoppermann\Httpstatus\Httpstatus;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Http\Message\ResponseInterface;
use Src\BrokerAPI\Alor\Interfaces\AlorConstants;
use Src\BrokerAPI\Alor\Traits\TraitClients;
use Src\BrokerAPI\Alor\Traits\TraitOther;
use Src\BrokerAPI\Alor\Traits\TraitSecurities;

class AlorClient implements AlorConstants
{
    use TraitClients;
    use TraitSecurities;
    use TraitOther;

    /**
     * @const string
     */
    const TOKENS_FILENAME = 'tokens.json';

    /**
     * @const int
     */
    const ACCESS_TOKEN_EXPIRATION_TIME = 1800;

    /**
     * @const string
     */
    const REFRESH_TOKEN_URI = '/refresh';

    /**
     * @const bool
     */
    const DEBUG = true;

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var string $jwt
     */
    protected $jwt;

    /**
     * @var string $refreshTokenUri
     */
    protected $refreshTokenUri;

    /**
     * @var string $refreshToken
     */
    protected $refreshToken;

    /**
     * @var int $refreshExpirationAt
     */
    protected $refreshExpirationAt;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * AlorClient constructor.
     * @param string $baseUri
     * @param string $username
     * @param string $password
     * @param string $refreshTokenUri
     * @throws GuzzleException
     */
    public function __construct($baseUri, $username, $password, $refreshTokenUri)
    {
        $this->refreshTokenUri = $refreshTokenUri;
        $logger = new Logger('Logger');
        $logger->pushHandler(new StreamHandler('guzzle.log'));

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter(MessageFormatter::DEBUG)
            )
        );

        $this->client = new Client([
            'base_uri' => $baseUri,
            'handler' => $stack,
        ]);

        $this->username = $username;
        $this->password = $password;

        $this->refreshToken();
    }

    /**
     * Проверка наличия JWT-токена
     *
     * $return bool
     */
    public function hasJwt()
    {
        return !empty($this->jwt);
    }

    /**
     * Проверка наличия Refresh-токена
     *
     * $return bool
     */
    public function hasRefreshToken()
    {
        return !empty($this->refreshToken);
    }

    /**
     * Проверка наличия даты истечения Refresh-токена
     *
     * $return bool
     */
    public function hasRefreshExpirationAt()
    {
        return !empty($this->refreshExpirationAt);
    }

    /**
     * Логин в API
     *
     * @void
     * @throws GuzzleException
     */
    private function refreshToken()
    {
        $needUpdate = false;
        $tokens = file_exists(self::TOKENS_FILENAME)
            ? file_get_contents(self::TOKENS_FILENAME)
            : null;

        if ($tokens) {
            $paramsArr = $this->extractParams($tokens);
            $logoutTime = filemtime(self::TOKENS_FILENAME) + self::ACCESS_TOKEN_EXPIRATION_TIME;
            $needUpdate = true; //time() > $logoutTime;
        }

        if ($needUpdate && $this->refreshToken) {
            $client = new Client([
                'base_uri' => $this->refreshTokenUri,
            ]);

            $response = $client->post(
                self::REFRESH_TOKEN_URI,
                ['query' => ['token' => $this->refreshToken]]
            );

            $status = $response->getStatusCode();
            if ($status != Httpstatuscodes::HTTP_OK) {
                $httpStatus = new Httpstatus();
                throw new Exception('Error while refresh token. HTTP status code: ' . $status . ' - ' . $httpStatus[$status]);
            }

            $responseJSON = json_decode($response->getBody()->getContents(), true);

            if (!empty($responseJSON['AccessToken'])) {
                $this->jwt = $responseJSON['AccessToken'];
                $paramsArr['jwt'] = $this->jwt;
                file_put_contents(self::TOKENS_FILENAME, json_encode($paramsArr));
            }
        }
    }

    /**
     * Извлечение параметров аутентификации из объекта json, полученного в ответ на логин
     *
     * @param string $jsonData
     */
    private function extractParams($jsonData)
    {
        $params = json_decode($jsonData, true);

        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }

        return $params;
    }

    /**
     * Отправка запроса к Alor API
     *
     * @param string $url
     * @param array $options
     * @param bool $auth
     * @param string $requestType
     * @void
     * @throws GuzzleException
     */
    public function sendRequest(
        $url,
        $options = [],
        $auth = true,
        $requestType = self::REQUEST_TYPE_GET
    ) {
        $this->response = null;
        $checkHeaders = function () use (&$options) {
            if (!isset($options['headers'])) {
                $options['headers'] = [];
            }
        };

        if (!isset($options['headers']['Accept'])) {
            $checkHeaders();
            $options['headers']['Accept'] = self::CONTENT_TYPE_JSON;
        }

        if ($auth) {
            $checkHeaders();
            $options['headers']['Authorization'] = 'Bearer ' . $this->jwt;
        }

        if (self::DEBUG) {
            $options['debug'] = true;
        }

        $this->response = $this->client->request($requestType, $url, $options);
    }

    /**
     * Проверка наличия ответа на запрос и соответствия его статуса указанному
     *
     * @param int $statusCode
     * @return bool
     */
    public function isResponseStatusCode($statusCode): bool
    {
        return !empty($this->response) && $this->response->getStatusCode() == $statusCode;
    }

    /**
     * @param $body
     * @return mixed
     */
    public static function getResponseBodyAsArray($body)
    {
        return json_decode($body, true);
    }

    /**
     * Возврат результата запроса в виде массива
     *
     * @return array
     */
    public function getResponseAsArray(): array {
        return self::getResponseBodyAsArray($this->response->getBody());
    }

    /**
     * Возврат результата запроса в виде числа
     *
     * @return int
     */
    public function getResponseAsInteger(): int {
        return (int) $this->response->getBody();
    }

    /**
     * Возврат результата запроса в виде строки
     *
     * @return string
     */
    public function getResponseAsString(): string {
        return $this->response->getBody();
    }
}
