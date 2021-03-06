<?php

namespace App\Com;

use App\Exception\BusinessException;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;

/**
 * Class Log
 * @package App\Com
 * @method static Log info($context = [], $message = "") Alias for info()
 * @method static Log warning($context = [], $message = "") Alias for warning()
 * @method static Log error($context = [], $message = "") Alias for error()
 */
class Log
{

    /**
     * 获取日志栈ID
     * @param $id
     * @return mixed|null
     * @return string
     */
    private static function getId($id)
    {
        if (empty($id)) {
            $id = Context::get('request_uuid');
            if (empty($id)) {
                return '';
            }
        }
        $id = "log_append:{$id}";
        return $id;
    }

    /**
     * 追加日志栈
     * @param $key
     * @param $value
     * @param string $id
     * @return mixed
     */
    public static function append($key, $value, $id = '')
    {
        $id = self::getId($id);
        if (empty($id)) {
            return false;
        }
        $data = Context::get($id, []);
        if (isset($data[$key])) {
            $data[$key][] = $value;
        } else {
            $data[$key] = (array)$value;
        }

        Context::set($id, $data);
    }

    /**
     * 释放日志栈并返回日志数据
     * @param string $id
     * @return array
     */
    public static function destroyAppend($id = '')
    {
        $id = self::getId($id);
        if (empty($id)) {
            return [];
        }

        $data = (array)Context::get($id, []);

        Context::destroy($id);

        return $data;
    }

    /**
     * 释放日志栈并将日志数据写入
     * @param string $type
     * @param string $message
     * @param string $id
     * @return mixed
     */
    public static function logAppend($type = 'info', $message = '', $id = '')
    {
        $data = self::destroyAppend($id);

        if (empty($data)) {
            return false;
        }

        self::{$type}($data, $message);
    }


    /**
     * @param $name
     * @param $arguments
     * @method info
     */
    public static function __callStatic($name, $arguments)
    {
        if (!in_array($name, ['info','warning','error'])) {
            throw new BusinessException(ResponseCode::LOG_PARMAS_ERROR);
        }

        $context = array($arguments[0]);
        $message = (string)$arguments[1];

        $logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get("default");
        $logger->{$name}($message, $context);

    }

}