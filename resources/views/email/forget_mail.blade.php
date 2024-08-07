<!DOCTYPE>


<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>

<body style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">
    <style>
        @media  only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media  only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;">
        <tr>
            <td align="center" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;">
                    <!-- <tr>
                        <td class="header" style="box-sizing: border-box;position: relative; padding: 25px 0; text-align: center;">
                            <a href="#" style="box-sizing: border-box;position: relative; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none; display: inline-block;">
                                <img src="http://localhost/now/admin/images/logo.png" class="logo" alt="Laravel Logo">
                            </a>
                        </td>
                    </tr> -->

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box;position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box;position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="box-sizing: border-box;position: relative; max-width: 100vw; padding: 32px;">
                                        <h1 style="box-sizing: border-box;position: relative; color: #3d4852; font-size: 20px; font-weight: bold; margin-top: 0; text-align: left;"> Hello  </h1>
                                        <p style="box-sizing: border-box;position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">{!! $content ?? '' !!}</p>
                                        <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box;position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 30px auto; padding: 0; text-align: center; width: 100%;">
                                            <tr>
                                                <td align="center" style="box-sizing: border-box;position: relative;">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box;position: relative;">
                                                        <tr>
                                                            <td align="center" style="box-sizing: border-box;position: relative;">
                                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box;position: relative;">
                                                                    <tr>
                                                                        <td style="box-sizing: border-box;position: relative;">
                                                                            <a href="{{route('password.reset', $token)}}" class="button button-primary" target="_blank" rel="noopener" style="box-sizing: border-box;position: relative;-webkit-text-size-adjust: none;border-radius: 4px;color: #fff;display: inline-block;overflow: hidden;text-decoration: none;background-color: #2d3748;border-bottom: 8px solid #2d3748;border-left: 18px solid #2d3748;border-right: 18px solid #2d3748;border-top: 8px solid #2d3748;padding: 10px 50px;font-size: 22px;font-weight: 500;">Reset Password</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="box-sizing: border-box;position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">If you did not request a password reset, no further action is required.</p>
                                        <p style="box-sizing: border-box;position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Regards,<br>
                                        Team {{Constant::APP_NAME}} </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="box-sizing: border-box;position: relative;">
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box;position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;">
                                <tr>
                                    <td class="content-cell" align="center" style="box-sizing: border-box;position: relative; max-width: 100vw; padding: 32px;">
                                        <p style="box-sizing: border-box;position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;"> ©2021 Now I want it. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>