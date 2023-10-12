@extends('layouts.user')

@section('content')
    @php
        $languages = \Modules\Language\Models\Language::getActive();
    @endphp
    <div class="bravo_user_profile">
        <form method="post" action="{{ route('user.company.update' ) }}" class="default-form" >
            @csrf
            <div class="upper-title-box">
                <h3>{{ __('Edit: ').$row->name }}</h3>
                <div class="text">
                    @if($row->slug)
                        <p class="item-url-demo">{{__("Permalink")}}: {{ url(config('companies.companies_route_prefix') ) }}/<a href="#" class="open-edit-input" data-name="slug">{{$row->slug}}</a></p>
                    @endif
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
                            <div class="widget-title"><h4>{{ __("Company Info") }}</h4></div>
                            <div class="widget-content">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__("Company name")}} <span class="text-danger">*</span></label>
                                            <input type="text" required value="{{old('name',$translation->name)}}" name="name" placeholder="{{__("Company name")}}" class="form-control">
                                        </div>
                                    </div>
                                    @if(is_default_lang())
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('E-mail')}} <span class="text-danger">*</span></label>
                                                <input type="email" required value="{{old('email',$row->email)}}" placeholder="{{ __('Email')}}" name="email" class="form-control"  >
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Phone Number')}} <span class="text-danger">*</span></label>
                                            <input type="text" value="{{old('phone',$row->phone)}}" placeholder="{{ __('Phone')}}" name="phone" class="form-control" required   >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__("Website")}}</label>
                                            <input type="text" value="{{old('website',$row->website)}}" name="website" placeholder="{{__("Website")}}" class="form-control">
                                        </div>
                                    </div>
                                    @if(is_default_lang())
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Est. Since')}}</label>
                                                <input type="text" value="{{ old('founded_in',$row->founded_in ? date("d/m/Y",strtotime($row->founded_in)) :'') }}" placeholder="{{ __('Est. Since')}}" name="founded_in" class="form-control has-datepicker input-group date">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Address')}}</label>
                                            <input type="text" value="{{old('address',$row->address)}}" placeholder="{{ __('Address')}}" name="address" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__("City")}}</label>
                                            <input type="text" value="{{old('city',$row->city)}}" name="city" placeholder="{{__("City")}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__("State")}}</label>
                                            <input type="text" value="{{old('state',$row->state)}}" name="state" placeholder="{{__("State")}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="">{{__("Country")}}</label>
                                            <select name="country" class="form-control" id="country-sms-testing">
                                                <option value="">{{__('-- Select --')}}</option>
                                                @foreach(get_country_lists() as $id=>$name)
                                                    <option @if($row->country==$id) selected @endif value="{{$id}}">{{$name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__("Zip Code")}}</label>
                                            <input type="text" value="{{old('zip_code',$row->zip_code)}}" name="zip_code" placeholder="{{__("Zip Code")}}" class="form-control">
                                        </div>
                                    </div>
                                    @if(is_default_lang())
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input @if($row->allow_search) checked @endif type="checkbox" name="allow_search" value="1" class="form-control">
                                                <label>{{__("Allow In Search & Listing")}}</label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('About Company')}}</label>
                                            <div class="">
                                                <textarea name="about" class="d-none has-ckeditor" cols="30" rows="10">{{old('about',$translation->about)}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ls-widget">
                        <div class="tabs-box">
                            <div class="widget-title"><h4>{{ __("Company Location") }}</h4></div>
                            <div class="widget-content">
                                <div class="form-group">
                                    <label class="control-label">{{__("Location")}}</label>
                                    @if(!empty($is_smart_search))
                                        <div class="form-group-smart-search">
                                            <div class="form-content">
                                                <?php
                                                $location_name = "";
                                                $list_json = [];
                                                $traverse = function ($locations, $prefix = '') use (&$traverse, &$list_json , &$location_name,$row) {
                                                    foreach ($locations as $location) {
                                                        $translate = $location->translateOrOrigin(app()->getLocale());
                                                        if ($row->location_id == $location->id){
                                                            $location_name = $translate->name;
                                                        }
                                                        $list_json[] = [
                                                            'id' => $location->id,
                                                            'title' => $prefix . ' ' . $translate->name,
                                                        ];
                                                        $traverse($location->children, $prefix . '-');
                                                    }
                                                };
                                                $traverse($company_location);
                                                ?>
                                                <div class="smart-search">
                                                    <input type="text" class="smart-search-location parent_text form-control" placeholder="{{__("-- Please Select --")}}" value="{{ $location_name }}" data-onLoad="{{__("Loading...")}}"
                                                           data-default="{{ json_encode($list_json) }}">
                                                    <input type="hidden" class="child_id" name="location_id" value="{{$row->location_id ?? Request::query('location_id')}}">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="">
                                            <select name="location_id" class="form-control">
                                                <option value="">{{__("-- Please Select --")}}</option>
                                                <?php
                                                $traverse = function ($locations, $prefix = '') use (&$traverse, $row) {
                                                    foreach ($locations as $location) {
                                                        $selected = '';
                                                        if ($row->location_id == $location->id)
                                                            $selected = 'selected';
                                                        printf("<option value='%s' %s>%s</option>", $location->id, $selected, $prefix . ' ' . $location->name);
                                                        $traverse($location->children, $prefix . '-');
                                                    }
                                                };
                                                $traverse($company_location);
                                                ?>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{__("The geographic coordinate")}}</label>
                                    <div class="control-map-group">
                                        <div id="map_content"></div>
                                        <input type="text" placeholder="{{__("Search by name...")}}" class="bravo_searchbox form-control" autocomplete="off" onkeydown="return event.key !== 'Enter';">
                                        <div class="g-control">
                                            <div class="form-group">
                                                <label>{{__("Map Latitude")}}:</label>
                                                <input type="text" name="map_lat" class="form-control" value="{{$row->map_lat}}" onkeydown="return event.key !== 'Enter';">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__("Map Longitude")}}:</label>
                                                <input type="text" name="map_lng" class="form-control" value="{{$row->map_lng}}" onkeydown="return event.key !== 'Enter';">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__("Map Zoom")}}:</label>
                                                <input type="text" name="map_zoom" class="form-control" value="{{$row->map_zoom ?? "8"}}" onkeydown="return event.key !== 'Enter';">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                        <label><input @if($row->status=='publish') checked @endif type="radio" name="status" value="publish"> {{__("Publish")}}
                                        </label></div>
                                    <div>
                                        <label><input @if($row->status=='draft' or !$row->status) checked @endif type="radio" name="status" value="draft"> {{__("Draft")}}
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

                    @if(is_default_lang())
                        <div class="ls-widget">
                            <div class="widget-title"><h4>{{ __("Categories") }}</h4></div>
                            <div class="widget-content">
                                <div class="form-group">
                                    <select id="cat_id" class="form-control" name="category_id">
                                        <?php
                                        $selectedIds = !empty($row->category_id) ? explode(',', $row->category_id) : [];
                                        $traverse = function ($categories, $prefix = '') use (&$traverse, $selectedIds) {
                                            foreach ($categories as $category) {
                                                $selected = '';
                                                if (in_array($category->id, $selectedIds))
                                                    $selected = 'selected';
                                                printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                                                $traverse($category->children, $prefix . '-');
                                            }
                                        };
                                        $traverse($categories);
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(is_default_lang())
                        @foreach ($attributes as $attribute)
                        <div class="ls-widget">
                            <div class="widget-title"><h4>{{__('Attribute: :name',['name'=>$attribute->name])}}</h4></div>
                            <div class="widget-content">
                                <div class="terms-scrollable mb-4">
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
                    @endif

                    @if(is_default_lang())
                        <div class="ls-widget">
                            <div class="widget-title"><h4>{{ __('Logo')}} </h4></div>
                            <div class="widget-content pb-4">
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id',$row->avatar_id) !!}
                                <p><i>({{__('Recommended size 330px x 300px')}})</i></p>
                            </div>
                        </div>
                    @endif

                    @if(is_default_lang())
                        <div class="ls-widget">
                            <div class="widget-title"><h4>{{ __("Social Media") }}</h4></div>
                            <div class="widget-content">
                                <?php $socialMediaData = $row->social_media; ?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-skype"><i class="la la-skype"></i></span>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" name="social_media[skype]" value="{{ $socialMediaData['skype'] ?? '' }}" placeholder="{{__('Skype')}}" aria-label="{{__('Skype')}}" aria-describedby="social-skype">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-facebook"><i class="la la-facebook"></i></span>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off"  name="social_media[facebook]" value="{{ $socialMediaData['facebook'] ?? '' }}" placeholder="{{__('Facebook')}}" aria-label="{{__('Facebook')}}" aria-describedby="social-facebook">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-twitter"><i class="la la-twitter"></i></span>
                                    </div>
                                    <input type="text" class="form-control"autocomplete="off" name="social_media[twitter]" value="{{$socialMediaData['twitter'] ?? ''}}" placeholder="{{__('Twitter')}}" aria-label="{{__('Twitter')}}" aria-describedby="social-twitter">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-instagram"><i class="la la-instagram"></i></span>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" name="social_media[instagram]" value="{{$socialMediaData['instagram'] ?? ''}}" placeholder="{{__('Instagram')}}" aria-label="{{__('Instagram')}}" aria-describedby="social-instagram">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-linkedin"><i class="la la-linkedin"></i></span>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" name="social_media[linkedin]" value="{{$socialMediaData['linkedin'] ?? ''}}" placeholder="{{__('Linkedin')}}" aria-label="{{__('Linkedin')}}" aria-describedby="social-linkedin">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="social-google"><i class="la la-google"></i></span>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" name="social_media[google]" value="{{@$socialMediaData['google'] ?? ''}}" placeholder="{{__('Google')}}" aria-label="{{__('Google')}}" aria-describedby="social-google">
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script type="text/javascript" src="{{ asset('libs/daterange/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterange/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('libs/select2/js/select2.min.js') }}" ></script>
    <script>
        $('.has-datepicker').daterangepicker({
            singleDatePicker: true,
            showCalendar: false,
            autoUpdateInput: false,
            sameDate: true,
            autoApply: true,
            disabledPast: true,
            enableLoading: true,
            showEventTooltip: true,
            classNotAvailable: ['disabled', 'off'],
            disableHightLight: true,
            locale: {
                format: superio.date_format
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format(superio.date_format));
        });
    </script>
    <script>

        let mapLat = {{ !empty($row->candidate) ? ($row->candidate->map_lat ?? "51.505") : "51.505" }};
        let mapLng = {{ !empty($row->candidate) ? ($row->candidate->map_lng ?? "-0.09") : "-0.09" }};
        let mapZoom = {{ !empty($row->candidate) ? ($row->candidate->map_zoom ?? "8") : "8" }};

        jQuery(function ($) {
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [mapLat, mapLng],
                zoom: mapZoom,
                ready: function (engineMap) {
                    engineMap.addMarker([mapLat, mapLng], {
                        icon_options: {}
                    });
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").attr("value", zoom);
                    });
                    engineMap.searchBox($('#customPlaceAddress'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.searchBox($('.bravo_searchbox'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                }
            });

        });

        jQuery(function ($) {
            "use strict"
            $('.open-edit-input').on('click', function (e) {
                e.preventDefault();
                $(this).replaceWith('<input type="text" name="' + $(this).data('name') + '" value="' + $(this).html() + '">');
            });
        })
    </script>
@endsection
