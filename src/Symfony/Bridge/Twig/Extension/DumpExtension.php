<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Extension;

use Symfony\Bridge\Twig\TokenParser\DumpTokenParser;
use Symfony\Bridge\Twig\TokenParser\DebugTokenParser;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;


/**
 * Provides integration of the dump() function with Twig.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpExtension extends \Twig_Extension
{
    public function __construct(ClonerInterface $cloner = null)
    {
        $this->cloner = $cloner;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dump', array($this, 'dump'), array('is_safe' => array('html'), 'needs_context' => true, 'needs_environment' => true)),
        );
    }

    public function getTokenParsers()
    {
        return array(new DumpTokenParser(), new DebugTokenParser());
    }

    public function getName()
    {
        return 'dump';
    }

    public function dump(\Twig_Environment $env, $context)
    {
        if (!$env->isDebug() || !$this->cloner) {
            return;
        }

        if (2 === func_num_args()) {
            $vars = array();
            foreach ($context as $key => $value) {
                if (!$value instanceof \Twig_Template) {
                    $vars[$key] = $value;
                }
            }

            $vars = array($vars);
        } else {
            $vars = func_get_args();
            unset($vars[0], $vars[1]);
        }

        $html = '';
        $dumper = new HtmlDumper(function ($line, $depth) use (&$html) {
            if (-1 !== $depth) {
                $html .= str_repeat('  ', $depth).$line."\n";
            }
        });

        foreach ($vars as $value) {
            $dumper->dump($this->cloner->cloneVar($value));
        }

        return $html;
    }
}
