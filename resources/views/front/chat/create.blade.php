@extends('layouts.app')
@section('content')
    <fieldset class="row">
        <fieldset class="column small-12">
            <h3 class="text-center">{{trans('chat.start_chatting')}}</h3>
            {!! Form::open(['route' => 'chat.store']) !!}
            <label> {{trans('chat.message')}}
                <textarea name="message" rows="3" placeholder="{{trans('chat.message')}}" class="{{ $errors->has('message') ? ' is-invalid-input' : '' }}"></textarea>
                @if ($errors->has('message'))
                    <span class="form-error is-visible">{{ $errors->first('message') }}</span>
                @endif
            </label>
            @if($users->count() > 0)
                <fieldset class="fieldset users-fieldset">
                    <h4>{{trans('chat.select_recipients')}}</h4>
                    @if ($errors->has('recipients'))
                        <span class="form-error is-visible">{{ $errors->first('recipients') }}</span>
                    @endif
                    <div class="scrollable-users">
                        @foreach($users as $user)
                            <fieldset>
                                <input id="recipient_{{$user->id}}" name="recipients[]" value="{{$user->id}}" type="checkbox">
                                <label for="recipient_{{$user->id}}">{{$user->display_name}}</label>
                            </fieldset>
                        @endforeach
                        {{($users->links())}}
                    </div>
                </fieldset>
            @endif
            <button type="submit" class="info button expanded">{{trans('chat.send')}}</button>
        </fieldset>
        {!! Form::close() !!}
@stop