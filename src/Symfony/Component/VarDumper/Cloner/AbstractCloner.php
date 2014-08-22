<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Cloner;

use Symfony\Component\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = array(
        'o:Closure'        => 'Symfony\Component\VarDumper\Caster\ReflectionCaster::castClosure',
        'o:Reflector'      => 'Symfony\Component\VarDumper\Caster\ReflectionCaster::castReflector',

        'o:Doctrine\Common\Persistence\ObjectManager' => 'Symfony\Component\VarDumper\Caster\DoctrineCaster::castObjectManager',
        'o:Doctrine\Common\Proxy\Proxy'               => 'Symfony\Component\VarDumper\Caster\DoctrineCaster::castCommonProxy',
        'o:Doctrine\ORM\Proxy\Proxy'                  => 'Symfony\Component\VarDumper\Caster\DoctrineCaster::castOrmProxy',
        'o:Doctrine\ORM\PersistentCollection'         => 'Symfony\Component\VarDumper\Caster\DoctrineCaster::castPersistentCollection',

        'o:DOMException'             => 'Symfony\Component\VarDumper\Caster\DOMCaster::castException',
        'o:DOMStringList'            => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLength',
        'o:DOMNameList'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLength',
        'o:DOMImplementation'        => 'Symfony\Component\VarDumper\Caster\DOMCaster::castImplementation',
        'o:DOMImplementationList'    => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLength',
        'o:DOMNode'                  => 'Symfony\Component\VarDumper\Caster\DOMCaster::castNode',
        'o:DOMNameSpaceNode'         => 'Symfony\Component\VarDumper\Caster\DOMCaster::castNameSpaceNode',
        'o:DOMDocument'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castDocument',
        'o:DOMNodeList'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLength',
        'o:DOMNamedNodeMap'          => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLength',
        'o:DOMCharacterData'         => 'Symfony\Component\VarDumper\Caster\DOMCaster::castCharacterData',
        'o:DOMAttr'                  => 'Symfony\Component\VarDumper\Caster\DOMCaster::castAttr',
        'o:DOMElement'               => 'Symfony\Component\VarDumper\Caster\DOMCaster::castElement',
        'o:DOMText'                  => 'Symfony\Component\VarDumper\Caster\DOMCaster::castText',
        'o:DOMTypeinfo'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castTypeinfo',
        'o:DOMDomError'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castDomError',
        'o:DOMLocator'               => 'Symfony\Component\VarDumper\Caster\DOMCaster::castLocator',
        'o:DOMDocumentType'          => 'Symfony\Component\VarDumper\Caster\DOMCaster::castDocumentType',
        'o:DOMNotation'              => 'Symfony\Component\VarDumper\Caster\DOMCaster::castNotation',
        'o:DOMEntity'                => 'Symfony\Component\VarDumper\Caster\DOMCaster::castEntity',
        'o:DOMProcessingInstruction' => 'Symfony\Component\VarDumper\Caster\DOMCaster::castProcessingInstruction',
        'o:DOMXPath'                 => 'Symfony\Component\VarDumper\Caster\DOMCaster::castXPath',

        'o:ErrorException' => 'Symfony\Component\VarDumper\Caster\ExceptionCaster::castErrorException',
        'o:Exception'      => 'Symfony\Component\VarDumper\Caster\ExceptionCaster::castException',
        'o:Symfony\Component\VarDumper\Exception\ThrowingCasterException'
                           => 'Symfony\Component\VarDumper\Caster\ExceptionCaster::castThrowingCasterException',

        'o:PDO'            => 'Symfony\Component\VarDumper\Caster\PdoCaster::castPdo',
        'o:PDOStatement'   => 'Symfony\Component\VarDumper\Caster\PdoCaster::castPdoStatement',

        'o:ArrayObject'         => 'Symfony\Component\VarDumper\Caster\SplCaster::castArrayObject',
        'o:SplDoublyLinkedList' => 'Symfony\Component\VarDumper\Caster\SplCaster::castDoublyLinkedList',
        'o:SplFixedArray'       => 'Symfony\Component\VarDumper\Caster\SplCaster::castFixedArray',
        'o:SplHeap'             => 'Symfony\Component\VarDumper\Caster\SplCaster::castHeap',
        'o:SplObjectStorage'    => 'Symfony\Component\VarDumper\Caster\SplCaster::castObjectStorage',
        'o:SplPriorityQueue'    => 'Symfony\Component\VarDumper\Caster\SplCaster::castHeap',

        'r:curl'           => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castCurl',
        'r:dba'            => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castDba',
        'r:dba persistent' => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castDba',
        'r:gd'             => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castGd',
        'r:mysql link'     => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castMysqlLink',
        'r:process'        => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castProcess',
        'r:stream'         => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castStream',
        'r:stream-context' => 'Symfony\Component\VarDumper\Caster\ResourceCaster::castStreamContext',
    );

    protected $maxItems = 250;
    protected $maxString = -1;

    private $casters = array();
    private $data = array(array(null));
    private $prevErrorHandler;
    private $classInfo = array();

    /**
     * @param callable[]|null $casters A map of casters.
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Objects class are to be prefixed with a `o:`,
     * resources type are to be prefixed with a `r:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters.
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[strtolower($type)][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the first level in nested structures.
     *
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = (int) $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     *
     * @param int $maxString
     */
    public function setMaxString($maxString)
    {
        $this->maxString = (int) $maxString;
    }

    /**
     * {@inheritdoc}
     */
    public function cloneVar($var)
    {
        $this->prevErrorHandler = set_error_handler(array($this, 'handleError'));
        try {
            if (!function_exists('iconv')) {
                $this->maxString = -1;
            }
            $data = $this->doClone($var);
        } catch (\Exception $e) {
        }
        restore_error_handler();
        $this->prevErrorHandler = null;

        if (isset($e)) {
            throw $e;
        }

        return new Data($data);
    }

    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable.
     *
     * @return array The cloned variable represented in an array.
     */
    abstract protected function doClone($var);

    /**
     * Casts an object to an array representation.
     *
     * @param string $class    The class of the object.
     * @param object $obj      The object itself.
     * @param bool   $isNested True if the object is nested in the dumped structure.
     * @param int    &$cut     After the cast, number of items removed from $obj.
     *
     * @return array The object casted as array.
     */
    protected function castObject($class, $obj, $isNested, &$cut)
    {
        if (isset($this->classInfo[$class])) {
            $classInfo = $this->classInfo[$class];
        } else {
            $classInfo = array(
                method_exists($class, '__debugInfo'),
                new \ReflectionClass($class),
                array_reverse(array($class => $class) + class_parents($class) + class_implements($class) + array('*' => '*')),
            );

            $this->classInfo[$class] = $classInfo;
        }

        if ($classInfo[0]) {
            $a = $this->callCaster(array($obj, '__debugInfo'), $obj, array(), $isNested);
        } else {
            $a = (array) $obj;
        }
        $cut = 0;

        foreach ($a as $k => $p) {
            if (!isset($k[0]) || ("\0" !== $k[0] && !$classInfo[1]->hasProperty($k))) {
                unset($a[$k]);
                $a["\0+\0".$k] = $p;
            }
        }

        foreach ($classInfo[2] as $p) {
            if (!empty($this->casters[$p = 'o:'.strtolower($p)])) {
                foreach ($this->casters[$p] as $p) {
                    $a = $this->callCaster($p, $obj, $a, $isNested, $cut);
                }
            }
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param string   $type     The type of the resource.
     * @param resource $res      The resource.
     * @param bool     $isNested True if the object is nested in the dumped structure.
     *
     * @return array The resource casted as array.
     */
    protected function castResource($type, $res, $isNested)
    {
        $a = array();

        if (!empty($this->casters['r:'.$type])) {
            foreach ($this->casters['r:'.$type] as $c) {
                $a = $this->callCaster($c, $res, $a, $isNested);
            }
        }

        return $a;
    }

    /**
     * Calls a custom caster.
     *
     * @param callable        $callback The caster.
     * @param object|resource $obj      The object/resource being casted.
     * @param array           $a        The result of the previous cast for chained casters.
     * @param bool            $isNested True if $obj is nested in the dumped structure.
     * @param int             &$cut     After the cast, number of items removed from $obj.
     *
     * @return array The casted object/resource.
     */
    private function callCaster($callback, $obj, $a, $isNested, &$cut = 0)
    {
        try {
            $cast = call_user_func_array($callback, array($obj, $a, $isNested, &$cut));

            if (is_array($cast)) {
                $a = $cast;
            }
        } catch (\Exception $e) {
            $a["\0~\0⚠"] = new ThrowingCasterException($callback, $e);
        }

        return $a;
    }

    /**
     * Special handling for errors: cloning must be fail-safe.
     *
     * @internal
     */
    public function handleError($type, $msg, $file, $line, $context)
    {
        if (E_RECOVERABLE_ERROR === $type || E_USER_ERROR === $type) {
            // Cloner never dies
            throw new \ErrorException($msg, 0, $type, $file, $line);
        }

        if ($this->prevErrorHandler) {
            return call_user_func($this->prevErrorHandler, $type, $msg, $file, $line, $context);
        }

        return false;
    }
}
