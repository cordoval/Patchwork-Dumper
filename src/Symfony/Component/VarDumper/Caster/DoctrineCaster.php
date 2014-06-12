<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2014 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork\Dumper\Caster;

use Doctrine\Common\Proxy\Proxy as CommonProxy;
use Doctrine\ORM\Proxy\Proxy as OrmProxy;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DoctrineCaster
{
    public static function castCommonProxy(CommonProxy $p, array $a)
    {
        unset(
            $a['__cloner__'],
            $a['__initializer__']
        );

        return $a;
    }

    public static function castOrmProxy(OrmProxy $p, array $a)
    {
        $p = "\0".get_class($p)."\0";
        unset(
            $a[$p.'_entityPersister'],
            $a[$p.'_identifier']
        );

        return $a;
    }
}
