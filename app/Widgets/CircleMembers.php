<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class CircleMembers extends AbstractWidget
{
    const MEMBERS_COUNT = 5;
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
        $members = $circle->members()->latest()->limit(self::MEMBERS_COUNT)->get();

        return view(
            "widgets.circle_members",
            [
            'config' => $this->config,
            'circle' => $circle,
            'members' => $members
            ]
        );
    }
}
