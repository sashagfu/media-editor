<div>
    <ul class="menu vertical">
        @if($circle->isCreator())
            <li>
                <a class="circle-menu__settings"
                   href="{{route('circle.settings', ['slug' => $circle->slug])}}">{{trans('circles.settings')}}</a>
            </li>
            @if($circle->type == 'closed')
                <li>
                    <a class="circle-menu__requests"
                       href="{{route('circle.requests', ['slug' => $circle->slug])}}">{{trans('circles.requests')}}
                        @if($circle->requestingUsers()->count() > 0)
                            <span class="badge circle-menu__requests-count">{{$circle->requestingUsers()->count()}}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        @can('seeMembers', $circle)
        <li>
            <a class="circle-menu__settings"
               href="{{route('circle.members', ['slug' => $circle->slug])}}">{{trans('circles.members')}}</a>
        </li>
        @endcan
    </ul>
</div>
<hr>
