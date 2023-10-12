@extends('layouts.user')

@section('content')
    @php
        $languages = \Modules\Language\Models\Language::getActive();
    @endphp
    <form method="post" action="{{ route('seller.gig.store', ['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')] ) }}" class="default-form" >
        @csrf
        <input type="hidden" name="id" value="{{$row->id}}">
        <div class="upper-title-box">
            <div class="row">
                <div class="col-md-9">
                    <h3>{{$row->id ? __('Edit: ').$row->title : __('Add new gig')}}</h3>
                    <div class="text">
                        @if($row->slug)
                            <p class="item-url-demo">{{__("Permalink")}}: {{ url('gig' ) }}/<a href="#" class="open-edit-input" data-name="slug">{{$row->slug}}</a>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    @if($row->slug)
                        <a class="theme-btn btn-style-one" href="{{$row->getDetailUrl(request()->query('lang'))}}" target="_blank">{{__("View Gig")}}</a>
                    @endif
                </div>
            </div>
        </div>
        @include('admin.message')

        @if($row->id)
            @include('Language::admin.navigation')
        @endif

        <div class="row">
            <div class="col-xl-9">
                <!-- Ls widget -->
                <div class="ls-widget">
                    <div class="tabs-box">
                        <div class="widget-title"><h4>{{ __("Overview") }}</h4></div>
                        <div class="widget-content">
                            <div class="form-group">
                                <label>{{__("Title")}} <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{old('title',$translation->title)}}" required placeholder="{{__("Name of the gig")}}" class="form-control">
                            </div>

                            @if(is_default_lang())
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>{{__("Category Level 1")}} <span class="text-danger">*</span></label>
                                        <select name="cat_id" required class="form-control">
                                            <option value="">{{__("-- Select a Category--")}}</option>
                                            <?php
                                            $items = \Modules\Gig\Models\GigCategory::query()->whereNull('parent_id')->get();
                                            ?>
                                            @foreach($items as $item)
                                                <option @if(old('cat_id',$row->cat_id) == $item->id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{__("Category Level 2")}} <span class="text-danger">*</span></label>
                                        <select name="cat2_id" required class="form-control">
                                            <option value="">{{__("-- Select a Subcategory --")}}</option>
                                            <?php
                                            $items = \Modules\Gig\Models\GigCategory::query()->withDepth()->having('depth', '=', 1)->get();
                                            ?>
                                            @foreach($items as $item)
                                                <option data-parent="{{$item->parent_id}}" @if(old('cat2_id',$row->cat2_id) == $item->id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{__("Category Level 3")}} <span class="text-danger">*</span></label>
                                        <select name="cat3_id" required class="form-control">
                                            <option value="">{{__("-- Select a Subject--")}}</option>
                                            <?php
                                            $items = \Modules\Gig\Models\GigCategory::query()->withDepth()->having('depth', '=', 2)->get();
                                            ?>
                                            @foreach($items as $item)
                                                <option data-parent="{{$item->parent_id}}" @if(old('cat3_id',$row->cat3_id) == $item->id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{__("Search Tags")}}</label>
                                    <div class="">
                                        <input type="text" data-role="tagsinput" value="{{$row->tag}}" placeholder="{{ __('Enter tag')}}" name="tag" class="form-control tag-input">
                                        <div class="show_tags">
                                            @if(!empty($tags))
                                                @foreach($tags as $tag)
                                                    <span class="tag_item">{{$tag->name}}<span data-role="remove"></span>
                                                        <input type="hidden" name="tag_ids[]" value="{{$tag->id}}">
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-right mb-0"><small>{{__("10 tags maximum")}}</small></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $packages = old('packages',$translation->packages);
                @endphp
                <div class="ls-widget">
                    <div class="tabs-box">
                        <div class="widget-title"><h4>{{ __("Scope & Pricing") }}</h4></div>
                        <div class="widget-content">
                            <div class="form-group">
                                <label>{{ __("Packages") }}</label>
                                <div class="form-group-item">
                                    <div class="g-items-header">
                                        <div class="row">
                                            <div class="col-md-3">&nbsp;</div>
                                            <div class="col-md-3">{{__("Basic")}}</div>
                                            <div class="col-md-3">{{__("Standard")}}</div>
                                            <div class="col-md-3">{{__("Premium")}}</div>
                                        </div>
                                    </div>
                                    <div class="g-items">
                                        <div class="item">
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <strong>{{__(" Name")}} <span class="text-danger">*</span></strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" required name="packages[0][name]" class="form-control" value="{{$packages[0]['name'] ?? 'Basic'}}" placeholder="{{__('Name your package')}}">
                                                    <input type="hidden" name="packages[0][key]" value="basic">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="packages[1][name]" class="form-control" value="{{$packages[1]['name'] ?? 'Standard'}}" placeholder="{{__('Name your package')}}">
                                                    <input type="hidden" name="packages[1][key]" value="standard">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="packages[2][name]" class="form-control" value="{{$packages[2]['name'] ?? 'Premium'}}" placeholder="{{__('Name your package')}}">
                                                    <input type="hidden" name="packages[2][key]" value="premium">
                                                </div>
                                            </div>
                                        </div>
                                        @if(is_default_lang())
                                            <div class="item">
                                                <div class="row">
                                                    <div class="col-md-3 text-center">
                                                        <strong>{{__("Price")}} <span class="text-danger">*</span></strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="any" name="basic_price" min="5" class="form-control" required value="{{$row->basic_price}}" placeholder="{{__('Package Price')}}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="any" name="standard_price" class="form-control" value="{{$row->standard_price}}" placeholder="{{__('Package Price')}}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="any" name="premium_price" class="form-control" value="{{$row->premium_price}}" placeholder="{{__('Package Price')}}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="item">
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <strong>{{__("Desc")}} <span class="text-danger">*</span></strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <textarea name="packages[0][desc]" class="form-control" required placeholder="{{__('Describe the details of your offering')}}" cols="30" rows="6">{{$packages[0]['desc'] ?? ''}}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <textarea name="packages[1][desc]" class="form-control" placeholder="{{__('Describe the details of your offering')}}" cols="30" rows="6">{{$packages[1]['desc'] ?? ''}}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <textarea name="packages[2][desc]" class="form-control" placeholder="{{__('Describe the details of your offering')}}" cols="30" rows="6">{{$packages[2]['desc'] ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @if(is_default_lang())
                                            <div class="item">
                                                <div class="row">
                                                    <div class="col-md-3 text-center">
                                                        <strong>{{__("Delivery Time")}} <span class="text-danger">*</span></strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[0][delivery_time]" required class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[0]['delivery_time'] ?? '') == $i) selected @endif value="{{$i}}">{{__(":count Day(s)",['count'=>$i])}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[1][delivery_time]" class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[1]['delivery_time'] ?? '') == $i) selected @endif value="{{$i}}">{{__(":count Day(s)",['count'=>$i])}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[2][delivery_time]" class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[2]['delivery_time'] ?? '') == $i) selected @endif value="{{$i}}">{{__(":count Day(s)",['count'=>$i])}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item">
                                                <div class="row">
                                                    <div class="col-md-3 text-center">
                                                        <strong>{{__("Revisions")}} <span class="text-danger">*</span></strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[0][revision]" required class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[0]['revision'] ?? '') == $i) selected @endif value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                            <option value="-1">{{__("Unlimited")}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[1][revision]" class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[1]['revision'] ?? '') == $i) selected @endif value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                            <option value="-1">{{__("Unlimited")}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select name="packages[2][revision]" class="form-control">
                                                            <option value="">{{__("-- Please Select --")}}</option>
                                                            @for($i = 1; $i <= 10; $i++)
                                                                <option @if(($packages[2]['revision'] ?? '') == $i) selected @endif value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                            <option value="-1">{{__("Unlimited")}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __("Package Compare") }}</label>
                                <div class="form-group-item">
                                    <div class="g-items-header">
                                        <div class="row">
                                            <div class="col-md-5">{{__("Name")}}</div>
                                            <div class="col-md-2">{{__('Basic')}}</div>
                                            <div class="col-md-2">{{__('Standard')}}</div>
                                            <div class="col-md-2">{{__('Premium')}}</div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </div>
                                    <div class="g-items">
                                        <?php $old = old('package_compare',$translation->package_compare ?? []);
                                        if(empty($old)) $old = [[]];
                                        ?>
                                        @if(!empty($old))
                                            @foreach($old as $key=>$extra_price)
                                                <div class="item" data-number="{{$key}}">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                                                @foreach($languages as $language)
                                                                    <?php $key_lang = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                                    <div class="g-lang">
                                                                        <div class="title-lang">{{$language->name}}</div>
                                                                        <input type="text" name="package_compare[{{$key}}][name{{$key_lang}}]" class="form-control" value="{{$extra_price['name'.$key_lang] ?? ''}}" placeholder="{{__('Attribute Name')}}">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <input type="text" name="package_compare[{{$key}}][name]" class="form-control" value="{{$extra_price['name'] ?? ''}}" placeholder="{{__('Attribute Name')}}">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text"  name="package_compare[{{$key}}][content]" class="form-control" value="{{$extra_price['content'] ?? ''}}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text"  name="package_compare[{{$key}}][content1]" class="form-control" value="{{$extra_price['content1'] ?? ''}}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text"  name="package_compare[{{$key}}][content2]" class="form-control" value="{{$extra_price['content2'] ?? ''}}">
                                                        </div>
                                                        <div class="col-md-1">
                                                            @if(is_default_lang())
                                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if(is_default_lang())
                                            <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                                        @endif
                                    </div>
                                    <div class="g-more hide">
                                        <div class="item" data-number="__number__">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                                        @foreach($languages as $language)
                                                            <?php $key = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                            <div class="g-lang">
                                                                <div class="title-lang">{{$language->name}}</div>
                                                                <input type="text" __name__="package_compare[__number__][name{{$key}}]" class="form-control" value="" placeholder="{{__('Attribute name')}}">
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <input type="text" __name__="package_compare[__number__][name]" class="form-control" value="" placeholder="{{__('Attribute Name')}}">
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" __name__="package_compare[__number__][content]" class="form-control" value="">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" __name__="package_compare[__number__][content1]" class="form-control" value="">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" __name__="package_compare[__number__][content2]" class="form-control" value="">
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __("Add Extra Services") }}</label>
                                <div class="form-group-item">
                                    <div class="g-items-header">
                                        <div class="row">
                                            <div class="col-md-5">{{__("Name")}}</div>
                                            <div class="col-md-3">{{__('Price')}}</div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </div>
                                    <div class="g-items">
                                        <?php $old = old('extra_price',$row->extra_price ?? []);
                                        if(empty($old)) $old = [[]];
                                        ?>
                                        @if(!empty($old))
                                            @foreach($old as $key=>$extra_price)
                                                <div class="item" data-number="{{$key}}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                                                @foreach($languages as $language)
                                                                    <?php $key_lang = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                                    <div class="g-lang">
                                                                        <div class="title-lang">{{$language->name}}</div>
                                                                        <input type="text" name="extra_price[{{$key}}][name{{$key_lang}}]" class="form-control" value="{{$extra_price['name'.$key_lang] ?? ''}}" placeholder="{{__('Extra price name')}}">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <input type="text" name="extra_price[{{$key}}][name]" class="form-control" value="{{$extra_price['name'] ?? ''}}" placeholder="{{__('Extra price name')}}">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" @if(!is_default_lang()) disabled @endif min="0" name="extra_price[{{$key}}][price]" class="form-control" value="{{$extra_price['price'] ?? ''}}">
                                                        </div>
                                                        <div class="col-md-1">
                                                            @if(is_default_lang())
                                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if(is_default_lang())
                                            <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                                        @endif
                                    </div>
                                    <div class="g-more hide">
                                        <div class="item" data-number="__number__">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                                        @foreach($languages as $language)
                                                            <?php $key = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                            <div class="g-lang">
                                                                <div class="title-lang">{{$language->name}}</div>
                                                                <input type="text" __name__="extra_price[__number__][name{{$key}}]" class="form-control" value="" placeholder="{{__('Extra price name')}}">
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <input type="text" __name__="extra_price[__number__][name]" class="form-control" value="" placeholder="{{__('Extra price name')}}">
                                                    @endif
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" min="0" __name__="extra_price[__number__][price]" class="form-control" value="">
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ls-widget">
                    <div class="widget-title"><h4>{{ __("Description") }}</h4></div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label>{{ __("Briefly Describe Your Gig") }}</label>
                            <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{ $translation->content }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __("Frequently Asked Questions") }}</label>
                            <div class="form-group-item">
                                <div class="g-items-header">
                                    <div class="row">
                                        <div class="col-md-11">{{__("Add Questions & Answers for Your Buyers.")}}</div>
                                        <div class="col-md-1"></div>
                                    </div>
                                </div>
                                <div class="g-items">
                                    <?php $old = old('faqs',$row->faqs ?? []);
                                    if(empty($old)) $old = [[]];
                                    ?>
                                    @if(!empty($old))
                                        @foreach($old as $key=>$faq)
                                            <div class="item" data-number="{{$key}}">
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <input type="text" name="faqs[{{$key}}][title]" class="form-control" value="{{$faq['title'] ?? ''}}" placeholder="{{__('Add a Question: i.e. Do you translate to English as well?')}}">
                                                        <textarea name="faqs[{{$key}}][content]" class="form-control" placeholder="{{__('Add an Answer: i.e. Yes, I also translate from English to Hebrew.')}}">{{$faq['content'] ?? ''}}</textarea>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add FAQ')}}</span>
                                </div>
                                <div class="g-more hide">
                                    <div class="item" data-number="__number__">
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="{{__('Add a Question: i.e. Do you translate to English as well?')}}">
                                                <textarea __name__="faqs[__number__][content]" class="form-control" placeholder="{{__('Add an Answer: i.e. Yes, I also translate from English to Hebrew.')}}"></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ls-widget">
                    <div class="widget-title"><h4>{{ __("Requirements") }}</h4></div>
                    <div class="widget-content">
                        <div class="form-group-item mb-4">
                            <div class="g-items-header">
                                <div class="row">
                                    <div class="col-md-11">{{__("Add Question")}}</div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>
                            <div class="g-items">
                                <?php $old = old('requirements',$row->requirements ?? []);
                                if(empty($old)) $old = [];
                                ?>
                                @if(!empty($old))
                                    @foreach($old as $key=>$rq)
                                        <div class="item" data-number="{{$key}}">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <textarea name="requirements[{{$key}}][content]" class="form-control" placeholder="{{__('Request necessary details such as dimensions, brand guidelines, and more.')}}">{{$rq['content'] ?? ''}}</textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <label ><input type="checkbox" @if($rq['required'] ?? '') checked @endif name="requirements[{{$key}}][required]"  value="1" > {{__("Required")}}</label>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add question')}}</span>
                            </div>
                            <div class="g-more hide">
                                <div class="item" data-number="__number__">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <textarea __name__="requirements[__number__][content]" class="form-control" placeholder="{{__('Request necessary details such as dimensions, brand guidelines, and more.')}}"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <label ><input type="checkbox" __name__="requirements[__number__][required]"  value="1" > {{__("Required")}}</label>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="la la-trash"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ls-widget">
                    <div class="widget-title"><h4>{{ __("Gallery") }}</h4></div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label>{{__("Gallery")}}</label>
                            {!! \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery',$row->gallery) !!}
                        </div>
                        <div class="form-group">
                            <label>{{__("Youtube Video")}}</label>
                            <input type="text" name="video_url" class="form-control" value="{{old('video_url',$row->video_url)}}" placeholder="{{__("Youtube link video")}}">
                        </div>
                    </div>
                </div>

                @include('Core::frontend/seo-meta/seo-meta')

                <div class="mb-4 d-none d-md-block">
                    <button class="theme-btn btn-style-one" type="submit"><i class="fa fa-save" style="padding-right: 5px"></i> {{__('Save Changes')}}</button>
                </div>

            </div>

            <div class="col-xl-3">
                <div class="ls-widget">
                    <div class="widget-title"><h4>{{ __("Publish") }}</h4></div>
                    <div class="widget-content">
                        <div class="form-group">
                            @if(is_default_lang())
                                <div>
                                    <label><input @if($row->status=='publish') checked @endif @if(!is_admin() && setting_item('gig_need_approve')) disabled @endif type="radio" name="status" value="publish"> {{__("Publish")}}
                                    </label></div>
                                <div>
                                    <label><input @if($row->status=='draft') checked @endif @if(!is_admin() && setting_item('gig_need_approve')) disabled @endif type="radio" name="status" value="draft"> {{__("Draft")}}
                                    </label></div>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="text-right">
                                <button class="theme-btn btn-style-one" type="submit"><i class="fa fa-save"></i> {{__('Save Changes')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ls-widget">
                    <div class="widget-title"><h4>{{ __("Feature Image") }}</h4></div>
                    <div class="widget-content">
                        <div class="form-group">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
                        </div>
                    </div>
                </div>

                @foreach ($attributes as $attribute)
                    <div class="ls-widget">
                        <div class="widget-title"><strong>{{__('Attribute: :name',['name'=>$attribute->name])}}</strong></div>
                        <div class="widget-content">
                            <div class="form-group terms-scrollable">
                                @foreach($attribute->terms as $term)
                                    <label class="term-item">
                                        <input @if(!empty($selected_terms) and $selected_terms->contains($term->id)) checked @endif type="checkbox" name="terms[]" value="{{$term->id}}">
                                        <span class="term-name">{{$term->name}}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>
@endsection

@section('footer')
    <script>
        jQuery(function ($) {
            "use strict"
            var on_load = true;
            $('[name=cat_id]').on('change',function (){
                $('[name="cat2_id"] option').show().hide();
                $('[name="cat2_id"] [data-parent="'+$(this).val()+'"]').show();
                if(!on_load){
                    $('[name="cat2_id"] option:eq(0)').prop('selected', true);
                    $('[name="cat3_id"] option:eq(0)').prop('selected', true);
                }
                $('[name="cat2_id"]').trigger("change");
                on_load = false;
            }).trigger('change')
            $('[name=cat2_id]').on('change',function (){
                $('[name="cat3_id"] option').show().hide();
                $('[name="cat3_id"] [data-parent="'+$(this).val()+'"]').show();
            }).trigger('change');

            $('.open-edit-input').on('click', function (e) {
                e.preventDefault();
                $(this).replaceWith('<input type="text" name="' + $(this).data('name') + '" value="' + $(this).html() + '">');
            });

            // Tag input
            $('.tag-input').on('keypress',function (e) {
                if(e.keyCode == 13){
                    var val = $(this).val();
                    if(val){
                        var html = '<span class="tag_item">' + val +
                            '       <span data-role="remove"></span>\n' +
                            '          <input type="hidden" name="tag_name[]" value="'+val+'">\n' +
                            '       </span>';
                        $(this).parent().find('.show_tags').append(html);
                        $(this).val('');
                    }
                    e.preventDefault();
                    return false;
                }
            });

            $(document).on('click','[data-role=remove]',function () {
                $(this).closest('.tag_item').remove();
            });

            $(".form-group-item").each(function () {
                let container = $(this);
                $(this).on('click','.btn-remove-item',function () {
                    $(this).closest(".item").remove();
                });

                $(this).on('press','input,select',function () {
                    let value = $(this).val();
                    $(this).attr("value",value);
                });
            });
            $(".form-group-item .btn-add-item").on('click',function () {
                var p = $(this).closest(".form-group-item").find(".g-items");

                let number = $(this).closest(".form-group-item").find(".g-items .item:last-child").data("number");
                if(number === undefined) number = 0;
                else number++;
                let extra_html = $(this).closest(".form-group-item").find(".g-more").html();
                extra_html = extra_html.replace(/__name__=/gi, "name=");
                extra_html = extra_html.replace(/__number__/gi, number);
                p.append(extra_html);

                if(extra_html.indexOf('dungdt-select2-field-lazy') >0 ){

                    p.find('.dungdt-select2-field-lazy').each(function () {
                        var configs = $(this).data('options');
                        $(this).select2(configs);
                    });
                }
            });

        })
    </script>
@endsection
