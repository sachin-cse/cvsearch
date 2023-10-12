@extends('layouts.user')

@section('content')
    <div class="row">
        <div class="col-md-9">
            <div class="upper-title-box">
                <h3>{{ __("All Gigs") }}</h3>
                <div class="text">{{ __("Ready to jump back in?") }}</div>
            </div>
        </div>
        <div class="col-md-3 text-right">
            <a class="theme-btn btn-style-one" href="{{ route('seller.gig.create') }}">{{__("Add new gig")}}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="row">
        <div class="col-lg-12">
            <!-- Ls widget -->
            <div class="ls-widget">
                <div class="tabs-box">

                    <div class="widget-title">
                        <h4>{{ __("All Gigs") }}</h4>

                        <div class="chosen-outer">
                            <form method="get" class="default-form form-inline" action="{{ route('seller.all.gigs') }}">
                                <div class="form-group mb-0 mr-1">
                                    <input type="text" name="s" placeholder="{{ __("Search by name,...") }}" value="{{ request()->get('s') }}" class="form-control">
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
                                    <th style="width: 20%">{{ __("Name") }}</th>
                                    <th>{{ __("Price") }}</th>
                                    <th>{{ __("Category") }}</th>
                                    <th>{{ __("Status") }}</th>
                                    <th>{{ __("Reviews") }}</th>
                                    <th>{{ __("Date") }}</th>
                                    <th style="width: 100px;">{{ __("Action") }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @if($rows->count() > 0)
                                    @foreach($rows as $row)
                                        <tr>
                                            <td><a href="{{ route('seller.gig.edit', ['id' => $row->id]) }}">{{ $row->title }}</a></td>
                                            <td>{{ format_money($row->basic_price) }}</td>
                                            <td>
                                                {{$row->cat->name ?? ''}}<br>
                                                 - {{$row->cat2->name ?? ''}}<br>
                                                  -- {{$row->cat3->name ?? ''}}
                                            </td>
                                            <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                            <td>
                                                <span class="review-count-approved">
                                                    {{ $row->getNumberReviewsInService() }}
                                                </span>
                                            </td>
                                            <td>{{ display_date($row->updated_at)}}</td>
                                            <td>
                                                <div class="option-box">
                                                    <ul class="option-list">
                                                        <li><a href="{{ route('seller.gig.edit', ['id' => $row->id]) }}" data-text="Edit" ><span class="la la-pencil-alt"></span></a></li>
                                                        <li>
                                                            <form method="post" action="{{ route('seller.gig.delete') }}">
                                                                @csrf
                                                                <input type="hidden" name="gig_id" value="{{ $row->id }}" />
                                                                <button type="submit" data-text="Delete" class="bc-btn-delete bc-delete-item" data-confirm="{{ __("Do you want to delete?") }}" ><span class="la la-trash"></span></button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7" class="text-center">{{ __("No Items") }}</td></tr>
                                @endif
                                </tbody>
                            </table>
                            <div class="ls-pagination">
                                {{$rows->appends(request()->query())->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('footer')
@endsection
