<form class="form bravo-form-register" method="post">
    @csrf
    <form method="post" action="#">
        <div class="form-group">
            <div class="btn-box row">
                <div class="col-lg-6 col-md-12">
                    <input class="checked" type="radio" name="type" id="checkbox1" value="candidate" checked/>
                    <label for="checkbox1" class="theme-btn btn-style-four"><i class="la la-user"></i> {{ __("Candidate") }}</label>
                </div>
                <div class="col-lg-6 col-md-12">
                    <input class="checked" type="radio" name="type" id="checkbox2" value="employer"/>
                    <label for="checkbox2" class="theme-btn btn-style-four"><i class="la la-briefcase"></i> {{ __("Employer") }}</label>
                </div>
            </div>
        </div>


        <div class="row form-group">
            <div class="col-lg-6 col-md-12">
                <div class="form-group">
                    <label>{{__('First Name')}}</label>
                    <input type="text" class="form-control" name="first_name" autocomplete="off" placeholder="{{__("First Name")}}">
                    <i class="input-icon field-icon icofont-waiter-alt"></i>
                    <span class="invalid-feedback error error-first_name"></span>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="form-group">
                    <label>{{__('Last Name')}}</label>
                    <input type="text" class="form-control" name="last_name" autocomplete="off" placeholder="{{__("Last Name")}}">
                    <i class="input-icon field-icon icofont-waiter-alt"></i>
                    <span class="invalid-feedback error error-last_name"></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>{{__('Email address')}}</label>
            <input type="email" name="email" placeholder="{{__('Email address')}}" required>
            <span class="invalid-feedback error error-email"></span>
        </div>

        <div class="form-group">
            <label>{{ __("Password") }}</label>
            <input id="password-field" type="password" name="password" value="" placeholder="{{ __("Password") }}">
            <span class="invalid-feedback error error-password"></span>
        </div>
        <div class="form-group">
            <label>{{ __("Re-Password") }}</label>
            <input id="re-password-field" type="password" name="password_confirmation" value="" placeholder="{{ __("Re-Password") }}">
            <span class="invalid-feedback error error-password_confirmation"></span>
        </div>

        @if(setting_item("recaptcha_enable"))
            <div class="form-group">
                {{recaptcha_field($captcha_action ?? 'register')}}
                <span class="invalid-feedback error error-recaptcha"></span>
            </div>
        @endif

        <div class="form-group">
            <button class="theme-btn btn-style-one " type="submit" name="Register">{{ __('Sign Up') }}
                <span class="spinner-grow spinner-grow-sm icon-loading" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </form>
    @if(setting_item('facebook_enable') or setting_item('google_enable') or setting_item('twitter_enable'))
        <div class="bottom-box">
            <div class="divider"><span>or</span></div>
            <div class="btn-box row">
                @if(setting_item('facebook_enable'))
                    <div class="col-lg-6 col-md-12">
                        <a href="{{url('/social-login/facebook')}}" class="theme-btn social-btn-two facebook-btn btn_login_fb_link"><i class="fab fa-facebook-f"></i>{{__('Facebook')}}</a>
                    </div>
                @endif
                @if(setting_item('google_enable'))
                    <div class="col-lg-6 col-md-12">
                        <a href="{{url('social-login/google')}}" class="theme-btn social-btn-two google-btn btn_login_gg_link"><i class="fab fa-google"></i>{{__('Google')}}</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</form>
