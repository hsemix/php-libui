<?php

namespace UI\Control;

use RuntimeException;
use UI\Control;
use UI\Control\MenuItem;
use FFI\CData;

/**
 * Create window menu
 */
class Menu extends Control
{
    protected $childs = [];
    public function newControl(): CData
    {
        static $i = 0;
        if (empty($this->attr['title'])) {
            throw new RuntimeException('menu title can not empty');
        }
        $this->attr['id'] =  $this->attr['id'] ?? '_win_menu_' . $i;
        $i++;
        return self::$ui->newMenu($this->attr['title']);
    }

    public function pushChilds()
    {
        $this->attr['childs'] = $this->attr['childs'] ?? [];
        foreach ($this->attr['childs'] as $child) {
            if (is_array($child)) {
                $this->childs[] = $this->addMenuItem($child);
            } else if ($child == 'hr') {
                $this->addSep();
            }
        }
    }

    public function addMenuItem($menus)
    {
        $menus['parent'] = $this;
        $menus['parent_id'] = $this->attr['id'];
        $menus['idx'] = count($this->childs);
        $item = new MenuItem($this->build, $menus);
        $this->childs[] = $item;
        return $item;
    }

    public function addSep()
    {
        self::$ui->menuAppendSeparator($this->getUIInstance());
    }

    public function getChilds()
    {
        return $this->childs;
    }
}
