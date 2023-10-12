@extends('layouts.user')

@section('content')

    <div class="row">
        <div class="col-md-9">
            <div class="upper-title-box">
                <h3>{{ __("All Applicants") }}</h3>
                <div class="text">{{ __("Ready to jump back in?") }}</div>
            </div>
        </div>
        <div class="col-md-3 text-right">
            <a class="theme-btn btn-style-one" href="{{ route('user.applicants.create') }}">{{__("Add new applicant")}}</a>
        </div>
    </div>
    @include('admin.message')
    <div class="row">
        <div class="col-lg-12">
            <!-- Ls widget -->
            <div class="ls-widget">
                <div class="tabs-box">
                    <div class="widget-title">
                        <h4>{{ __("Applicants") }}</h4>

                        <div class="chosen-outer">
                            <form method="get" class="default-form form-inline" action="{{ route('user.applicants') }}">
                                <!--Tabs Box-->

                                <a href="{{ route('user.applicants.export') }}" target="_blank" title="{{ __("Export to excel") }}" class="theme-btn btn-style-two mr-1">{{ __("Export") }}</a>

                                <div class="chosen-outer frontend-select-2 mr-1">
                                    @php
                                        $job = \Modules\Job\Models\Job::find(Request()->input('job_id'));
                                        \App\Helpers\AdminForm::select2('job_id', [
                                            'configs' => [
                                                'ajax'        => [
                                                    'url' => route('job.admin.getForSelect2'),
                                                    'dataType' => 'json'
                                                ],
                                                'allowClear'  => true,
                                                'placeholder' => __('-- Select Job --')
                                            ]
                                        ], !empty($job->id) ? [
                                            $job->id,
                                            $job->title . ' (#' . $job->id . ')'
                                        ] : false);
                                    @endphp
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
                                    <th>{{ __("Candidate") }}</th>
                                    <th>{{ __("Job Title") }}</th>
                                    <th>{{ __("CV") }}</th>
                                    <th>{{ __("Date Applied") }}</th>
                                    <th>{{ __("Status") }}</th>
                                    <th class="text-center">{{ __("Actions") }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @if($rows->total() > 0)
                                    @foreach($rows as $row)
                                        <tr class="{{$row->status}} applicant-item">
                                            <td>
                                                @if($row->user)
                                                    <a href="{{ $row->candidateInfo->getDetailUrl().'?apply_id='.$row->id }}" target="_blank" class="candidate">
                                                        <img src="{{ $row->user->getAvatarUrl() }}" style="border-radius: 50%" class="company-logo" />
                                                        {{ !empty($row->user->getDisplayName()) ? $row->user->getDisplayName() : $row->user->id }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="title">
                                                <a href="{{ $row->jobInfo->getDetailUrl() }}" target="_blank">{{$row->jobInfo->title}}</a>
                                            </td>
                                            <td>
                                                @if(!empty($row->cvInfo->file_id))
                                                    @php $file = (new \Modules\Media\Models\MediaFile())->findById($row->cvInfo->file_id) @endphp
                                                    <a href="{{ \Modules\Media\Helpers\FileHelper::url($row->cvInfo->file_id) }}" target="_blank" download>
                                                        {{ $file->file_name.'.'.$file->file_extension }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ display_date($row->created_at) }}</td>
                                            <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                            <td>
                                                <div class="option-box">
                                                    <ul class="option-list">
                                                        <li><a class="dropdown-item" href="{{ route('candidate.detail', ['slug' => $row->candidateInfo->slug]) }}" data-text="{{ __("View Candidate Profile") }}"><span class="la la-user"></span></a></li>
                                                        <li><a href="#modal-applied-{{ $row->id }}" class="bc-call-modal" data-text="{{ __("View Application") }}" ><span class="la la-eye"></span></a></li>
                                                        <li><a href="{{ route('user.applicants.changeStatus', ['status' => 'approved', 'id' => $row->id]) }}" data-text="{{ __("Approve Application") }}"><span class="la la-check"></span></a></li>
                                                        <li><a href="{{ route('user.applicants.changeStatus', ['status' => 'rejected', 'id' => $row->id]) }}" data-text="{{ __("Reject Application") }}"><span class="la la-times-circle"></span></a></li>
                                                        <li><a href="{{ route('user.applicants.changeStatus', ['status' => 'delete', 'id' => $row->id]) }}" data-text="{{ __("Delete Application") }}" class="bc-delete-item" data-confirm="{{__("Do you want to delete?")}}"><span class="la la-trash"></span></a></li>
                                                    </ul>
                                                </div>

                                                <div class="model bc-model applied-modal" id="modal-applied-{{ $row->id }}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <h4 class="modal-title">{{ __("Applied Detail") }}</h4>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="info-form">
                                                                    <div class="applied-list">
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __("Candidate:") }}</div>
                                                                            <div class="val">
                                                                                @if(!empty($row->candidateInfo->user->getDisplayName()))
                                                                                    <a href="{{ $row->candidateInfo->getDetailUrl().'?apply_id='.$row->id }}" target="_blank" class="candidate">
                                                                                        <img src="{{ $row->candidateInfo->user->getAvatarUrl() }}" style="border-radius: 50%" class="company-logo" />
                                                                                        {{$row->candidateInfo->user->getDisplayName() ?? ''}}
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __('Job Title:') }}</div>
                                                                            <div class="val">
                                                                                <a href="{{ $row->jobInfo->getDetailUrl() }}" target="_blank">{{$row->jobInfo->title}}</a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __("CV:") }}</div>
                                                                            <div class="val">
                                                                                @if(!empty($row->cvInfo->file_id))
                                                                                    @php $file = (new \Modules\Media\Models\MediaFile())->findById($row->cvInfo->file_id) @endphp
                                                                                    <a href="{{ \Modules\Media\Helpers\FileHelper::url($row->cvInfo->file_id) }}" target="_blank" download>
                                                                                        {{ $file->file_name.'.'.$file->file_extension }}
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __("Message:") }}</div>
                                                                            <div class="val">{{ $row->message }}</div>
                                                                        </div>
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __("Date Applied:") }}</div>
                                                                            <div class="val">{{ display_date($row->created_at) }}</div>
                                                                        </div>
                                                                        <div class="applied-item">
                                                                            <div class="label">{{ __("Status:") }}</div>
                                                                            <div class="val"><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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


    </div>
@endsection

@section('footer')
@endsection
