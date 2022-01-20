<?php


namespace Src\BrokerAPI\Alor\Interfaces;


interface AlorConstants
{
    const CONTENT_TYPE_JSON     = 'application/json';

    // типы запросов REST
    const REQUEST_TYPE_GET      = 'GET';
    const REQUEST_TYPE_POST     = 'POST';
    const REQUEST_TYPE_PUT      = 'PUT';
    const REQUEST_TYPE_DELETE   = 'DELETE';

    // формат возвращаемых данных
    const FORMAT_TV             = 'TV';
    const FORMAT_BOT            = 'BOT';
    const FORMAT_APP            = 'APP';
    const FORMAT_SIMPLE         = 'Simple';

    // биржа
    const EXCHANGE_MOEX         = 'MOEX';
    const EXCHANGE_SPBX         = 'SPBX';
    const EXCHANGE_DEFAULT      = self::EXCHANGE_MOEX;

    // таймфрейм
    const TF_15_SEC             = 15;
    const TF_1_MIN              = 60;
    const TF_5_MIN              = 300;
    const TF_15_MIN             = 900;
    const TF_1_HOUR             = 3600;
    const TF_1_DAY              = 86400;

    // сектор - имя торговой системы [FORTS, FOND, CURR]
    const SECTOR_FORTS          = 'FORTS';
    const SECTOR_FOND           = 'FOND';
    const SECTOR_CURRENCY       = 'CURR';

    // код торгового сервера
    const SERVER_CODE_TRADE     = 'TRADE';
    const SERVER_CODE_FUT1      = 'FUT1';

    // не рабочие дни суббота и воскресенье
    const WEEKEND_DAYS = [0, 6];

    // время дневной торговой сессии
    const TRADING_SESSION_DAY = [
        'from' => '10:00:00',
        'to'   => '18:59:59'
    ];
}
