<?php


namespace Src\BrokerAPI\Alor;

class AlorUser
{

    /**
     * @var string
     */
    public $account;

    /**
     * @var string
     */
    public $portfolio;

    /**
     * Конструктор AlorUser
     *
     * @param string $account
     * @param string $portfolio
     */
    public function __construct(
        $account,
        $portfolio
    ) {
        $this->account = $account;
        $this->portfolio = $portfolio;
    }

    /**
     * Получение в виде массива
     *
     * @return array
     */
    public function asArray(): array
    {
        return [
            'account' => $this->account,
            'portfolio' => $this->portfolio,
        ];
    }
}
