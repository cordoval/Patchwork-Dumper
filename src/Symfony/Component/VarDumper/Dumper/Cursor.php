<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Represents the current state of a dumper while dumping.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Cursor
{
    const HASH_INDEXED = Stub::ARRAY_INDEXED;
    const HASH_ASSOC = Stub::ARRAY_ASSOC;
    const HASH_OBJECT = Stub::TYPE_OBJECT;
    const HASH_RESOURCE = Stub::TYPE_RESOURCE;

    public $depth = 0;
    public $refIndex = false;
    public $softRefTo = false;
    public $hardRefTo = false;
    public $hashType;
    public $hashKey;
    public $hashIndex = 0;
    public $hashLength = 0;
    public $hashCut = 0;
    public $stop = false;
}
