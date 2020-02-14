<div class="row">
    <div class="small-12 columns circle-header">
        <img width="100%" class="circles-header__cover-img" src="{{$circle->lastCover}}" alt="cover">
        <div class="circle-header__info">
            <a href="{{route('circle.single', ['slug' => $circle->slug])}}"><h3 class="text-white circle-header__title">{{$circle->title}}</h3></a>
            <h4 class="text-white circle-header__description">{{$circle->description}}</h4>
            <p class="text-white">{{trans('circles.type')}}: <span class="text-capitalize">{{$circle->type}}</span></p>
            @can('join', $circle)
                <a class="button small circle-header__membership {{$circle->type == 'closed' ? 'request' : ''}}" data-slug="{{$circle->slug}}" href="#">{{trans('circles.join')}}</a>
            @else
                @if($circle ->isRequesting())
                    <a class="button small alert disabled" href="#">{{trans('circles.request-pending')}}</a>
                    <a class="button small cancel-self-request" data-slug="{{$circle->slug}}" href="#">{{trans('circles.cancel_request')}}</a>
                @endif
                @if($circle->isMember() && !$circle->isCreator())
                    <a class="button small alert circle-header__membership" data-slug="{{$circle->slug}}" href="#">{{trans('circles.leave')}}</a>
                @endif
            @endcan
        </div>
    </div>
</div>