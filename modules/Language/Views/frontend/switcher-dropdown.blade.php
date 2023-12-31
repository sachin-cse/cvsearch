@php
    $languages = \Modules\Language\Models\Language::getActive();
    $locale = session('website_locale',app()->getLocale());
@endphp
{{--Multi Language--}}
@if(!empty($languages) && setting_item('site_enable_multi_lang'))
    <li class="dropmenu-right dropdown show">
        @foreach($languages as $language)
            @if($locale == $language->locale)
                <a href="#" id="dropdownLang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="is_login dropdown-toggle">
                    @if($language->flag)
                        <span class="flag-icon flag-icon-{{$language->flag}}"></span>
                    @endif
                    <span class="lang-text">{{$language->name}}</span>
                    <i class="flaticon-down-arrow"></i>
                </a>
                @break;
            @endif
        @endforeach
        <ul class="dropdown-menu text-left">
            @foreach($languages as $language)
                @if($locale != $language->locale)
                    <li>
                        <a href="{{get_lang_switcher_url($language->locale)}}" class="is_login">
                            @if($language->flag)
                                <span class="flag-icon flag-icon-{{$language->flag}}"></span>
                            @endif
                            {{$language->name}}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
{{--End Multi language--}}
