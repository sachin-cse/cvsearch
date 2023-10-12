@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ $page_title }}</h1>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{route('gig_order.admin.bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::INCOMPLETE }}">{{__("Incomplete")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::IN_PROGRESS }}">{{__("In Progress")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::DELIVERED }}">{{__("Delivered")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::IN_REVISION }}">{{__("In Revision")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::COMPLETED }}">{{__("Completed")}}</option>
                                <option value="{{ \Modules\Gig\Models\GigOrder::CANCELLED }}">{{__("Cancelled")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-left">
                <form method="get" action="{{ route('gig_order.admin.index') }}" class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    <select name="status" class="form-control">
                        <option value="">{{__(" All ")}}</option>
                        <option  @if(Request()->status == \Modules\Gig\Models\GigOrder::INCOMPLETE) selected @endif value="{{ \Modules\Gig\Models\GigOrder::INCOMPLETE }}">{{__("Incomplete")}}</option>
                        <option @if(Request()->status == \Modules\Gig\Models\GigOrder::IN_PROGRESS) selected @endif value="{{ \Modules\Gig\Models\GigOrder::IN_PROGRESS }}">{{__("In Progress")}}</option>
                        <option @if(Request()->status == \Modules\Gig\Models\GigOrder::DELIVERED) selected @endif value="{{ \Modules\Gig\Models\GigOrder::DELIVERED }}">{{__("Delivered")}}</option>
                        <option @if(Request()->status == \Modules\Gig\Models\GigOrder::IN_REVISION) selected @endif value="{{ \Modules\Gig\Models\GigOrder::IN_REVISION }}">{{__("In Revision")}}</option>
                        <option @if(Request()->status == \Modules\Gig\Models\GigOrder::COMPLETED) selected @endif value="{{ \Modules\Gig\Models\GigOrder::COMPLETED }}">{{__("Completed")}}</option>
                        <option @if(Request()->status == \Modules\Gig\Models\GigOrder::CANCELLED) selected @endif value="{{ \Modules\Gig\Models\GigOrder::CANCELLED }}">{{__("Cancelled")}}</option>
                    </select>
                    <?php
                        $author = \App\User::find(Request()->input('author_id'));
                        \App\Helpers\AdminForm::select2('author_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url' => route('user.admin.getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('-- Select Author --')
                            ]
                        ], !empty($author->id) ? [
                            $author->id,
                            $author->getDisplayName()
                        ] : false)
                    ?>
                    <?php
                    $customer = \App\User::find(Request()->input('customer_id'));
                    \App\Helpers\AdminForm::select2('customer_id', [
                        'configs' => [
                            'ajax'        => [
                                'url' => route('user.admin.getForSelect2'),
                                'dataType' => 'json'
                            ],
                            'allowClear'  => true,
                            'placeholder' => __('-- Select Customer --')
                        ]
                    ], !empty($customer->id) ? [
                        $customer->id,
                        $customer->getDisplayName()
                    ] : false)
                    ?>
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>{{__('Title')}}</th>
                                <th width="200px"> {{__('Author')}}</th>
                                <th width="200px"> {{__('Customer')}}</th>
                                <th width="200px"> {{__('Created')}}</th>
                                <th width="200px"> {{__("Price")}}</th>
                                <th width="200px"> {{__("Status")}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($rows->total() > 0)
                                @foreach($rows as $row)
                                    <tr class="{{$row->status}}">
                                        <td><input type="checkbox" name="ids[]" class="check-item" value="{{$row->id}}">
                                        </td>
                                        <td class="title">
                                            <a href="{{$row->gig ? $row->gig->getDetailUrl() : '#'}}">{{$row->gig->title ?? ''}}</a>
                                        </td>
                                        <td>{{ $row->author ? $row->author->getDisplayName() : '' }}</td>
                                        <td>{{ $row->customer ? $row->customer->getDisplayName() : '' }}</td>
                                        <td>{{display_date($row->created_at)}}</td>
                                        <td>{{format_money($row->price)}}</td>
                                        <td><span class="">{{$row->status_text}}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">{{__("No order gig found")}}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection
