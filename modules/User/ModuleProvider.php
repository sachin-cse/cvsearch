<?php
namespace Modules\User;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Modules\Gig\Models\Gig;
use Modules\ModuleServiceProvider;
use Modules\Payout\Models\VendorPayout;
use Modules\User\Models\Plan;
use Modules\Vendor\Models\VendorRequest;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        Blade::directive('has_permission', function ($expression) {
            return "<?php if(auth()->user()->hasPermission({$expression})): ?>";
        });
        Blade::directive('end_has_permission', function ($expression) {
            return "<?php endif; ?>";
        });

    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    public static function getBookableServices()
    {
        return ['plan'=>Plan::class];
    }

    public static function getAdminMenu()
    {
        $noti_verify = User::countVerifyRequest();
        $noti = $noti_verify;

        $options = [
            "position"=>100,
            'url'        => 'admin/module/user',
            'title'      => __('Users :count',['count'=>$noti ? sprintf('<span class="badge badge-warning">%d</span>',$noti) : '']),
            'icon'       => 'icon ion-ios-contacts',
            'permission' => 'user_manage',
            'children'   => [
                'user'=>[
                    'url'   => 'admin/module/user',
                    'title' => __('All Users')
                ],
                'role'=>[
                    'url'        => 'admin/module/user/role',
                    'title'      => __('Role Manager'),
                    'permission' => 'role_manage'
                ],
                'subscriber'=>[
                    'url'        => 'admin/module/user/subscriber',
                    'title'      => __('Subscribers'),
                    'permission' => 'newsletter_manage',
                ],
                'company_request'=>[
                    'url'        => route('user.admin.upgrade'),
                    'title'      => __("Company Request"),
                    'permission' => 'user_manage'
                ]
            ]
        ];
        return [
            'users'=> $options,
            'plan'=>[
                "position"=>50,
                'url'        => route('user.admin.plan.index'),
                'title'      => __('User Plans'),
                'icon'       => 'icon ion-ios-contacts',
                'permission' => 'user_manage',
                'children'   => [
                    'user-plan'=>[
                        'url'   => route('user.admin.plan.index'),
                        'title' => __('User Plans'),
                        'permission' => 'user_manage',
                    ],
                    'employer-plan'=>[
                        'url'        => route('user.admin.plan_report.index'),
                        'title'      => __('Plan Report'),
                        'permission' => 'user_manage',
                    ],
                ]
            ]
        ];
    }

    public static function getUserFrontendMenu()
    {
        $configs = [
        ];
        return $configs;
    }

    public static function getUserMenu()
    {
        /**
         * @var $user User
         */
        $res = [

            'user_dashboard' => [
                'url' => 'user/dashboard',
                'title' => __("Dashboard"),
                'icon' => 'la la-home',
                'position' => 20
            ],
            'my_orders' => [
                'url' => 'user/order',
                'title' => __("My Orders"),
                'icon' => 'la la-luggage-cart',
                'permission' => 'employer_manage',
                'enable' => true
            ],
            'my_contact' => [
                'url' => 'user/my-contact',
                'title' => __("My Contact"),
                'icon' => 'la la-envelope',
                'enable' => true
            ],
            'change_password' => [
                'url' => 'user/profile/change-password',
                'title' => __("Change Password"),
                'icon' => 'la la-lock',
                'enable' => true
            ]
        ];

        return $res;
    }
}
