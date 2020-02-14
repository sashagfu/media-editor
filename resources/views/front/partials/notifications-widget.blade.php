<div class="row notifications-scroll">
    @foreach($notifications as $notification)
    <div class="notification-container__link" data-link="{{$notification->data['notification_url']}}" data-id="{{$notification->id}}">
        <div class="row notification-container__notification {{$notification->read_at ? '' : 'notification-container__new-notification'}}">
            <div class="small-2 columns"><img class="thumbnail" src="{{$notification->data['notifier_avatar']}}" alt="user_avatar"></div>
            <div class="small-10 columns"><p class="notification-container__text">{{$notification->data['notification_text']}}</p>
                @if(isset($notification->data['action']) && $notification->data['action'] == 'circle_invite')
                    <a class="success button circle-invite__btn invite-accept" data-id="{{$notification->data['invite_id']}}">{{trans('circles.approve')}}</a>
                    <a class="alert button circle-invite__btn invite-cancel" data-id="{{$notification->data['invite_id']}}">{{trans('circles.cancel')}}</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    {{$notifications->links()}}
</div>