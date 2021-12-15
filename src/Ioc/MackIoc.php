<?php
/**
 *
 * User: daikai
 * Date: 2021/7/13
 */

namespace ClearSwitch\Http\Ioc;


/**
 * Class MackIoc
 * 类的依赖注入
 * @package ClearSwitch\Http\Ioc
 */
class MackIoc
{
    protected $instances = [];

    public function __construct()
    {
    }

    public function getInstances($abstract)
    {
        $reflector = new \ReflectionClass($abstract);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            return new $abstract();
        }
        $dependencies = $constructor->getParameters();
        if (!$dependencies) {
            return new $abstract();
        }
        foreach ($dependencies as $dependency) {
            if (!is_null($dependency->getClass())) {
                $p[] = $this->make($dependency->getClass()->name);
            }
        }
        //创建一个类的新实例,给出的参数将传递到类的构造函数
        return $reflector->newInstanceArgs($p);
    }

    /***
     * Date: 2021/7/8 上午10:15
     * @param string $className
     * @return UpdateOrInsertTrackingFactory
     * @author clearSwitch
     */
    public function make(string $className)
    {
        return $this->getInstances($className);
    }

}
