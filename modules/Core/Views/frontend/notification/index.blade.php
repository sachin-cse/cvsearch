@extends('layouts.user')
@section ('content')
    <div id="bravo_notify" class="bravo_user_profile p-0">
        <div class="upper-title-box">
            <h3 class="title">{{__("Notifications")}}</h3>
            <div class="text">{{ __("Ready to jump back in?") }}</div>
        </div>

        <div>
            <div class="ls-widget">
                <div class="tabs-box">
                    <div class="widget-title">
                        <h4>{{ __("All Notifications") }}</h4>
                    </div>
                    <div class="widget-content pb-4">
                        <div class="row">
                            <div class="col-md-3 col-sm-12 mb-3">
                                <div class="panel">
                                    <ul class="dropdown-list-items p-0">
                                        <li class="notification @if(empty($type)) active @endif">
                                            <i class="flaticon-home fa-lg mr-2"></i> <a href="{{route('core.notification.loadNotify')}}">&nbsp;{{__('All')}}</a>
                                        </li>
                                        <li class="notification @if(!empty($type) && $type == 'unread') active @endif">
                                            <i class="flaticon-mail fa-lg mr-2"></i> <a href="{{route('core.notification.loadNotify', ['type'=>'unread'])}}">{{__('Unread')}}</a>
                                        </li>
                                        <li class="notification @if(!empty($type) && $type == 'read') active @endif">
                                            <i class="flaticon-email-2 fa-lg mr-2"></i> <a href="{{route('core.notification.loadNotify', ['type'=>'read'])}}">{{__('Read')}}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @include('Core::frontend.notification.list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
