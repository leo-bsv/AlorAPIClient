<?php


namespace Src\BrokerAPI\Alor\Traits;


use DateTime;
use Exception;
use Lukasoppermann\Httpstatus\Httpstatuscodes;

trait TraitOther
{
    /**
     * Запроос текущего времени в формате Unix
     *
     * @var bool $auth
     * @return null|DateTime
     * @throws Exception
     */
    public function getTime(
        $auth = false
    ) {
        $this->sendRequest(
            '/md/v2/time',
            [],
            $auth
        );

        return $this->isResponseStatusCode(Httpstatuscodes::HTTP_OK)
            ? (new DateTime())->setTimestamp($this->getResponseAsString())
            : null;
    }
}
