<?php

namespace App\Services;

/**
 * サービスマネージャー抽象クラス
 * Class ServiceManagerAbstract
 * @package App\Services
 */
abstract class ServiceManagerAbstract
{
    /**
     * @var array
     */
    private $services;

    /**
     * ServiceManagerAbstract constructor.
     * @param array $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * サービス取得
     * @param $class_name
     * @return mixed
     * @throws \Exception
     */
    public function getService($class_name)
    {
        foreach ($this->services as $service) {
            if ($service instanceof $class_name) {
                return $service;
            }
        }
        throw new \Exception("class [{$class_name}] not found!");
    }
}