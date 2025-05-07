<?php

namespace LotteryBundle\Exception;

use Tourze\JsonRPC\Core\Exception\JsonRpcException;

/**
 * JSON-RPC 执行过程中, 可统一对外的异常
 * 适用范围: RPC的procedures中可抛出
 */
class LotteryException extends JsonRpcException
{
    public function __construct($mixed = '', $code = 0, array $data = [], ?\Throwable $previous = null)
    {
        if (is_array($mixed)) {
            $message = $mixed[1];
            $code = $mixed[0];
        } else {
            $message = $mixed;
        }

        parent::__construct($code, $message, $data, previous: $previous);
    }
}
