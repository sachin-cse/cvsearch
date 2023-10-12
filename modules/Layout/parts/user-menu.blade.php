<li class="menu-hr"><a href="{{route('user.dashboard')}}">{{__("Dashboard")}}</a></li>

@if(is_employer())
    <li class="dropdown-divider"></li>
    <li class="dropdown-header">{{__("Employer")}}</li>
    <li class="menu-hr"><a href="{{ route('user.company.profile') }}">{{__("Company profile")}}</a></li>

    <li class="menu-hr"><a href="{{route('user.manage.jobs')}}">{{__("Manage Jobs")}}</a></li>
    <li class="menu-hr"><a href="{{route('user.applicants')}}">{{__("All Applicants")}}</a></li>
    <li class="menu-hr"><a href="{{route('user.wishList.index')}}"> {{__("Shortlisted")}}</a></li>
@endif
@if(Modules\Gig\Models\Gig::isEnable())
    <li class="dropdown-divider"></li>
    <li class="dropdown-header">{{__("Gigs")}}</li>
    <li >
        @has_permission('gig_manage')
        <a href="{{route('seller.all.gigs')}}">{{__("All Gigs")}}</a>
        <a href="{{route('seller.dashboard')}}">{{__("Seller Dashboard")}}</a>
        <a href="{{route('seller.orders')}}">{{__("Gig Orders")}}</a>
        @else
            <a href="{{route('buyer.orders')}}">{{__("Gig Orders")}}</a>
            @end_has_permission
    </li>
@endif
@if(is_candidate() && !is_admin())
    <li class="dropdown-divider"></li>
    <li class="dropdown-header">{{__("Candidate")}}</li>
    <li class="menu-hr dd"><a href="{{ route('user.candidate.index') }}">{{__("My profile")}}</a></li>
    @if(\Modules\Gig\Models\Gig::isEnable() && \Modules\Payout\Models\VendorPayout::isEnable())
        <li class="menu-hr"><a href="{{route('payout.candidate.index')}}">{{__("Payouts")}}</a></li>
    @endif
    <li class="menu-hr"><a href="{{route('user.applied_jobs')}}">{{__("Applied Jobs")}}</a></li>
    <li class="menu-hr"><a href="{{route('user.wishList.index')}}"> {{__("Shortlisted")}}</a></li>
    <li class="menu-hr"><a href="{{route('user.following.employers')}}">{{__("Following")}}</a></li>
@endif
<li class="dropdown-divider"></li>
<li class="menu-hr"><a href="{{route('user.plan')}}">{{__("My Plans")}}</a></li>
<li class="menu-hr"><a href="{{route('user.my-contact')}}">{{__("My Contact")}}</a></li>
<li class="menu-hr"><a href="{{route('user.change_password')}}">{{__("Change password")}}</a></li>

@if(is_admin())
    <li class="dropdown-divider"></li>
    <li class="menu-hr"><a href="{{url('/admin')}}">{{__("Admin Dashboard")}}</a></li>
@endif
<li class="dropdown-divider"></li>
<li class="menu-hr">
    <a  href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{__('Logout')}}</a>
</li>
