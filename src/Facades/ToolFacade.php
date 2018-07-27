<?php
namespace GFL\Tool\Facades;

use Illuminate\Support\Facades\Facade;

class ToolFacade extends Facade
{
    protected static function getFacadeAccessor() {
      return 'Tool';
    }
}
