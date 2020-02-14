<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class CircleHeader extends AbstractWidget
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

        return view(
            "widgets.circle_header",
            [
            'config' => $this->config,
            'circle' => $circle
            ]
        );
    }
}
