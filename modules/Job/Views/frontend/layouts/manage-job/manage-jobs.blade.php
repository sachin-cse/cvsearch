@extends('layouts.user')

@section('content')

    <div class="row">
        <div class="col-md-9">
            <div class="upper-title-box">
                <h3>{{ __("Manage Jobs") }}</h3>
                <div class="text">{{ __("Ready to jump back in?") }}</div>
            </div>
        </div>
        <div class="col-md-3 text-right">
            <a class="theme-btn btn-style-one" href="{{ route('user.create.job') }}">{{__("Add new job")}}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="row">
        <div class="col-lg-12">
            <!-- Ls widget -->
            <div class="ls-widget">
                <div class="tabs-box">
                    <div class="widget-title">
                        <h4>{{ __("Manage Jobs") }}</h4>

                        <div class="chosen-outer">
                            <form method="get" class="default-form form-inline" action="{{ route('user.manage.jobs') }}">
                                <!--Tabs Box-->
                                <div class="form-group mb-0 mr-2">
                                    <input type="text" name="s" value="{{ request()->input('s') }}" placeholder="{{__('Search by name')}}" class="form-control">
                                </div>
                                <button type="submit" class="theme-btn btn-style-one">{{ __("Search") }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="table-outer">
                            <table class="default-table manage-job-table">
                                <thead>
                                <tr>
                                    <th>{{ __("Title") }}</th>
                                    <th width="200px">{{ __('Location')}}</th>
                                    <th width="150px">{{ __('Category')}}</th>
                                    <th width="100px">{{ __('Status')}}</th>
                                    <th width="100px">{{ __('Date')}}</th>
                                    @if(setting_item('job_need_approve'))
                                        <th width="100px"> {{ __('Approved')}}</th>
                                    @endif
                                    <th width="100px"></th>
                                </tr>
                                </thead>
                                <tbody>

                                @if($rows->total() > 0)
                                    @foreach($rows as $row)
                                        <tr class="{{$row->status}}">
                                            <td class="title">
                                                <a href="{{ route('user.edit.job', ['id' => $row->id]) }}">{{$row->title}}</a>
                                            </td>
                                            <td>{{$row->location->name ?? ''}}</td>
                                            <td>{{$row->category->name ?? ''}}</td>
                                            <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                            <td>{{ display_date($row->updated_at)}}</td>
                                            @if(setting_item('job_need_approve'))
                                            <td> @if($row->is_approved == 'approved') 
                                                <span class="badge badge-success"> {{ __('Approved')}} </span>
                                                @elseif($row->is_approved == 'draft')
                                                <span class="badge badge-secondary"> {{ __('Draft') }} </span>
                                                @elseif($row->is_approved == 'waiting')
                                                <span class="badge badge-warning"> {{ __('Waiting for Approval') }} </span>
                                                @endif
                                            </td>
                                        @endif
                                            <td>
                                                <div class="option-box">
                                                    <ul class="option-list">
                                                        <li><a href="{{ $row->getDetailUrl() }}" target="_blank" data-text="{{ __("View Job") }}" ><span class="la la-eye"></span></a></li>
                                                        <li><a href="{{ route('user.edit.job', ['id' => $row->id]) }}" data-text="{{ __("Edit Job") }}"><span class="la la-pencil"></span></a></li>
                                                        <li><a href="{{ route('user.delete.job', ['id' => $row->id]) }}" data-text="{{ __("Delete Job") }}" class="bc-delete-item" data-confirm="{{__("Do you want to delete?")}}"><span class="la la-trash"></span></a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7">{{__("No data")}}</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>

                        <div class="ls-pagination">
                            {{$rows->appends(request()->query())->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('footer')
@endsection
