<?php

namespace App\Providers;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

           try {
               
            // $mail = DB::table('setting_company')->first();
            // if (!empty($mail)) //checking if table is not empty
            // {
                $config = array(
                    'driver'     => 'smtp',
                    'host'       => getSettingValue('smtp_host'),
                    'port'       => getSettingValue('smtp_port'),
                    'from'       => array('address' => getSettingValue('smtp_noreply'), 'name' => 'E-Sign'),
                    'encryption' => 'tls',
                    'username'   => getSettingValue('smtp_username'),
                    'password'   => getSettingValue('smtp_password'),
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                Config::set('mail', $config);
            //}
           } catch (\Exception $e) {
            $res = array('code' => 201, 'msg' => 'Something went wrong! Try again');
        }
        
    }
}