<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class CircleMenu extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $circle = $this->config['circle'];

        return view("widgets.circle_menu", [
            'config' => $this->config,
            'circle' => $circle
        ]);
    }
}
