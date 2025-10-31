<?php

namespace LotteryBundle\Exception;

use Tourze\JsonRPC\Core\Exception\JsonRpcException;

/**
 * JSON-RPC 执行过程中, 可统一对外的异常
 * 适用范围: RPC的procedures中可抛出
 */
class LotteryException extends JsonRpcException
{
    /**
     * @param string|array<int, int|string> $mixed
     * @param array<string, mixed> $data
     */
    public function __construct(string|array $mixed = '', int $code = 0, array $data = [], ?\Throwable $previous = null)
    {
        if (is_array($mixed)) {
            $message = (string) $mixed[1];
            $code = (int) $mixed[0];
        } else {
            $message = $mixed;
        }

        parent::__construct($code, $message, $data, previous: $previous);
    }
}
