<?php


namespace Src\BrokerAPI\Alor\Interfaces;


interface AlorOrderInterface
{
    // направление трейда
    const
        SIDE_BUY  = 'buy',
        SIDE_SELL = 'sell';

    // тип ордера
    const
        ORDER_TYPE_MARKET           = 'market',
        ORDER_TYPE_LIMIT            = 'limit',
        ORDER_TYPE_STOP             = 'stop',             // по рынку
        ORDER_TYPE_STOP_LIMIT       = 'stoplimit',        // лимитный стоп
        ORDER_TYPE_STOPLOSS         = 'stopLoss',         // по рынку, синоним STOP
        ORDER_TYPE_STOPLOSS_LIMIT   = 'stopLossLimit',    // лимитный стоп, синоним STOP LIMIT
        ORDER_TYPE_TAKEPROFIT       = 'takeProfit',       // по рынку
        ORDER_TYPE_TAKEPROFIT_LIMIT = 'takeProfitLimit';  // лимитный тейк-профит

    // статусы ордера
    const
        STATUS_WORKING  = 'working',  // На исполнении
        STATUS_FILLED   = 'filled',   // Исполнена
        STATUS_CANCELED = 'canceled', // Отменена
        STATUS_REJECTED = 'rejected'; // Отклонена

    // активные и закрытые ордера
    const
        ACTIVE_STATUSES = [
            self::STATUS_WORKING,
        ],
        CLOSED_STATUSES = [
            self::STATUS_FILLED,
            self::STATUS_CANCELED,
            self::STATUS_REJECTED,
        ];
}
