<?php

/**
 * php-libui (http://toknot.com)
 *
 * @copyright  Copyright (c) 2019 Szopen Xiao (Toknot.com)
 * @license    http://toknot.com/LICENSE.txt New BSD License
 * @link       https://github.com/chopins/php-libui
 * @version    0.1
 */

namespace UI\Control;

use UI\Control;
use FFI\CData;
use UI\Control\Attribute;
use UI\Struct\ForEachStatus;
use UI\Struct\AttributeType;

/**
 * @method string string()
 * @method int len()
 * @method void appendUnattributed(string $str)
 * @method void insertAtUnattributed(string $str, int $at)
 * @method void delete(int $start, int $end)
 * @method int numGraphemes()
 * @method int byteIndexToGrapheme(int $pos)
 * @method int graphemeToByteIndex(int $post)
 */
class AttributeString extends Control
{
    const CTL_NAME = 'string';
    const IS_CONTROL = false;
    const TYPE_MAP = [
        'color' =>  AttributeType::ATTRIBUTE_TYPE_COLOR,
        'bgcolor' => AttributeType::ATTRIBUTE_TYPE_BACKGROUND,
        'font' => AttributeType::ATTRIBUTE_TYPE_FAMILY,
        'control' => AttributeType::ATTRIBUTE_TYPE_FEATURES,
        'italic' => AttributeType::ATTRIBUTE_TYPE_ITALIC,
        'size' => AttributeType::ATTRIBUTE_TYPE_SIZE,
        'stretch' => AttributeType::ATTRIBUTE_TYPE_STRETCH,
        'underline' => AttributeType::ATTRIBUTE_TYPE_UNDERLINE,
        'underlineColor' => AttributeType::ATTRIBUTE_TYPE_UNDERLINE_COLOR,
        'weight' => AttributeType::ATTRIBUTE_TYPE_WEIGHT
    ];

    protected $callPrefix = 'attributedString';
    protected $callPrefixFuncList = [
        'string', 'len', 'appendUnattributed', 'insertAtUnattributed',
        'delete', 'numGraphemes', 'byteIndexToGrapheme', 'graphemeToByteIndex'
    ];

    protected function newControl(): CData
    {
        $this->instance = self::$ui->newAttributedString($this->attr['string']);
        $this->setAllAttr();
        return $this->instance;
    }

    public function free()
    {
        $this->freeAttributedString();
    }

    public function len()
    {
        return strlen($this->attr['string']);
    }

    protected function setAllAttr()
    {
        foreach (self::TYPE_MAP as $k => $t) {
            if (isset($this->attr[$k])) {
                $config = ['widget' => 'attribute', 'type' => $t];
                $config[$k] = $this->attr[$k];
                if ($k == 'underlineColor') {
                    $config['underlineColorType'] = $this->attr['underlineColorType'];
                }
                $attType = $this->build->createItem($config);
                $this->setAttribute($attType, 0, $this->len());
            }
        }
    }
    public function addAttr(string $k, $value, $start, $end)
    {
        $config = ['widget' => 'attribute', 'type' => self::TYPE_MAP[$k]];
        $config[$k] = $value;
        if ($k == 'underlineColor') {
            $config['underlineColor'] = $value[0];
            $config['underlineColorType'] = $value[1];
        }
        $attType = $this->build->createItem($config);
        $this->setAttribute($attType, $start, $end);
    }

    public function setAttribute(Attribute $a, int $start, int $end)
    {
        $this->attributedStringSetAttribute($a->getUIInstance(), $start, $end);
    }

    public function forEachAttribute($callable, $data = null)
    {
        $func = function ($s, $a, $start, $end, $cdata) use ($callable, $data): ForEachStatus {
            $handle = self::$ui->controlHandle($a);
            $ac = $this->build->getControlByHandle($handle);
            $attr = $ac ?? new Attribute($this->build, [], $a);
            return $callable($this, $attr, $start, $end, $data);
        };
        $this->attributedStringForEachAttribute($func, null);
    }
}
