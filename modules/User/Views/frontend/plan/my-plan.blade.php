@extends('layouts.user')
@section('head')

@endsection
@section('content')
    <div class="upper-title-box">
        <h3>{{__("My Current Plan")}}</h3>
        <div class="text">{{ __("Ready to jump back in?") }}</div>
    </div>
    @include('admin.message')
    <div class="row justify-content-center">

        <div class="ui-block col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="ui-item">
                <div class="left">
                    <i class="icon flaticon-briefcase"></i>
                </div>
                <div class="right">
                    <h4>
                        @if(is_employer())
                            @if($user->company) {{$user->company->jobs()->count('id')}} @endif
                        @else
                            {{$user->gigs()->count('id')}}
                        @endif
                    </h4>
                    <p>{{__("Total posted")}}</p>
                </div>
            </div>
        </div>
        <div class="ui-block col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="ui-item ui-green">
                <div class="left">
                    <i class="icon la la-bookmark-o"></i>
                </div>
                <div class="right">
                    <h4>
                        @if(is_employer())
                            @if($user->company) {{$user->company->jobsPublish()->count('id')}} @endif
                        @else
                            {{$user->gigsPublish()->count('id')}}
                        @endif
                    </h4>
                    <p>{{__("Total published")}}</p>
                </div>
            </div>
        </div>
        <div class="ui-block col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="ui-item ui-yellow">
                <div class="left">
                    <i class="icon la la-comment-o"></i>
                </div>
                <div class="right">
                    <h4>{{auth()->user()->getMaximumPublishItems()}}</h4>
                    <p>{{__("Maximum published")}}</p>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ls-widget">
                <div class="tabs-box">
                    <div class="widget-title">
                        <h4>{{__("My Current Plan")}}</h4>
                    </div>
                    <div class="widget-content">
                        @php
                            $user_plans = $user->userPlans;
                        @endphp
                        <table class="default-table manage-job-table mb-5">
                            <thead>
                            <tr>
                                <th>{{__("Plan ID")}}</th>
                                <th>{{__("Plan Name")}}</th>
                                <th>{{__("Expiry")}}</th>
                                <th>{{__("Publish Allowed")}}</th>
                                <th>{{__("Price")}}</th>
                                <th>{{__("Status")}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if($user_plans && count($user_plans) > 0)
                                @foreach($user_plans as $user_plan)
                                    <tr>
                                        <td>#{{$user_plan->plan_id}}</td>
                                        <td class="trans-id">{{$user_plan->plan->title ?? ''}}</td>
                                        <td class="total-jobs">{{display_datetime($user_plan->end_date)}}</td>
                                        <td class="used">@if(!$user_plan->max_service) {{__("Unlimited")}} @else {{$user_plan->max_service}} @endif</td>
                                        <td class="remaining">{{format_money($user_plan->price)}}</td>
                                        <td >
                                            @if($user_plan->is_valid)
                                                <span class="text-success">{{__('Active')}}</span>
                                            @else
                                                <div class="text-danger mb-3">{{__('Expired')}}</div>
                                                <div>
                                                    <a href="{{route('plan')}}" class="btn btn-warning">{{__('Renew')}}</a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{__("No Items")}}
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
@endsection
