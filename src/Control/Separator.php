<?php

namespace UI\Control;

use UI\Control;
use FFI\CData;

/**
 * @property-read string $type  value is 'hr' or 'vr'
 */
class Separator extends Control
{
    const CTL_NAME = 'sep';
    public function newControl(): CData
    {
        if ($this->attr['type'] == 'hr') {
            $this->instance = self::$ui->newHorizontalSeparator();
        } elseif ($this->att['type'] == 'vr') {
            $this->instance = self::$ui->newVerticalSeparator();
        }
        return $this->instance;
    }
}
