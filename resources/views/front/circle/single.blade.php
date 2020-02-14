@extends('layouts.app')
@section('content')
    <circle-single-page id="circle-single-page"
                  :circle="{{$circle}}"
                  @can('seeFeed', $circle) :feed-items="{{$feed_items->toJson()}}" @endcan
                  @can('updateSettings', $circle) :can-update-settings="true" @endcan
                  @can('updateSettings', $circle) :requests="{{$requests}}" @endcan
                  @can('seeMembers', $circle) :can-see-members="true" @endcan
                  @can('addPost', $circle) :can-add-post="true"
                        :form-type="'circle'"
                        :show-form="true"
                        @endcan
                  @can('join', $circle) :can-join="true" @endcan
                  @if($circle ->isRequesting()) :is-requestioning="true" @endif
                  @if($circle->isMember() && !$circle->isCreator()) :is-member="true" @endif
                  :members="{{$circle->members}}"
    >

    </circle-single-page>
@endsection