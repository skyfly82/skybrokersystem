<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/api/v1/customer/login' => [[['_route' => 'api_customer_login', '_controller' => 'App\\Controller\\CustomerAuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/customer/profile' => [[['_route' => 'api_customer_profile', '_controller' => 'App\\Controller\\CustomerAuthController::profile'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/customer/company-users' => [[['_route' => 'api_customer_company_users', '_controller' => 'App\\Controller\\CustomerAuthController::getCompanyUsers'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/customer/logout' => [[['_route' => 'api_customer_logout', '_controller' => 'App\\Controller\\CustomerAuthController::logout'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/customer/invitations' => [[['_route' => 'api_customer_invitations_list', '_controller' => 'App\\Controller\\CustomerInvitationController::listInvitations'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/customer/invitations/send' => [[['_route' => 'api_customer_send_invitation', '_controller' => 'App\\Controller\\CustomerInvitationController::sendInvitation'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/registration/start' => [[['_route' => 'api_customer_register_step1', '_controller' => 'App\\Controller\\CustomerRegistrationController::start'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/registration/register' => [[['_route' => 'api_customer_register', '_controller' => 'App\\Controller\\CustomerRegistrationController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/registration/gus-lookup' => [[['_route' => 'api_gus_lookup', '_controller' => 'App\\Controller\\CustomerRegistrationController::gusLookup'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/registration/check-email' => [[['_route' => 'api_check_email', '_controller' => 'App\\Controller\\CustomerRegistrationController::checkEmail'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/registration/customer-types' => [[['_route' => 'api_customer_types', '_controller' => 'App\\Controller\\CustomerRegistrationController::getCustomerTypes'], null, ['GET' => 0], null, false, false, null]],
        '/test-email' => [[['_route' => 'test_email', '_controller' => 'App\\Controller\\EmailTestController::testEmail'], null, ['GET' => 0], null, false, false, null]],
        '/send-email' => [[['_route' => 'send_email', '_controller' => 'App\\Controller\\EmailTestController::sendEmail'], null, ['POST' => 0], null, false, false, null]],
        '/health' => [[['_route' => 'app_health', '_controller' => 'App\\Controller\\HealthController::health'], null, ['GET' => 0], null, false, false, null]],
        '/' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\HomeController::index'], null, ['GET' => 0], null, false, false, null]],
        '/web' => [[['_route' => 'app_home_web', '_controller' => 'App\\Controller\\HomeController::web'], null, ['GET' => 0], null, false, false, null]],
        '/auth' => [[['_route' => 'app_auth', '_controller' => 'App\\Controller\\HomeController::auth'], null, ['GET' => 0], null, false, false, null]],
        '/login' => [[['_route' => 'app_login_web', '_controller' => 'App\\Controller\\HomeController::loginWeb'], null, ['GET' => 0], null, false, false, null]],
        '/login/customer' => [[['_route' => 'app_login_customer_web', '_controller' => 'App\\Controller\\HomeController::loginCustomerWeb'], null, ['GET' => 0], null, false, false, null]],
        '/login/admin' => [[['_route' => 'app_login_admin_web', '_controller' => 'App\\Controller\\HomeController::loginAdminWeb'], null, ['GET' => 0], null, false, false, null]],
        '/register' => [[['_route' => 'app_register_web', '_controller' => 'App\\Controller\\HomeController::registerWeb'], null, ['GET' => 0], null, false, false, null]],
        '/forgot' => [[['_route' => 'app_forgot_web', '_controller' => 'App\\Controller\\HomeController::forgotWeb'], null, ['GET' => 0], null, false, false, null]],
        '/dashboard' => [[['_route' => 'app_dashboard', '_controller' => 'App\\Controller\\HomeController::dashboard'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/system/login' => [[['_route' => 'api_system_login', '_controller' => 'App\\Controller\\SystemAuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/system/profile' => [[['_route' => 'api_system_profile', '_controller' => 'App\\Controller\\SystemAuthController::profile'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/system/team' => [[['_route' => 'api_system_team', '_controller' => 'App\\Controller\\SystemAuthController::getTeam'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/system/logout' => [[['_route' => 'api_system_logout', '_controller' => 'App\\Controller\\SystemAuthController::logout'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:98)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:134)'
                                .'|router(*:148)'
                                .'|exception(?'
                                    .'|(*:168)'
                                    .'|\\.css(*:181)'
                                .')'
                            .')'
                            .'|(*:191)'
                        .')'
                    .')'
                .')'
                .'|/api/v1/(?'
                    .'|customer/(?'
                        .'|company\\-users/([^/]++)(?'
                            .'|(*:251)'
                            .'|/(?'
                                .'|role(*:267)'
                                .'|status(*:281)'
                            .')'
                            .'|(*:290)'
                        .')'
                        .'|invitations/([^/]++)/(?'
                            .'|cancel(*:329)'
                            .'|accept(*:343)'
                            .'|info(*:355)'
                        .')'
                    .')'
                    .'|invitations/([^/]++)/(?'
                        .'|info(*:393)'
                        .'|accept(*:407)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        98 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        134 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        168 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        181 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        191 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        251 => [[['_route' => 'api_customer_get_user', '_controller' => 'App\\Controller\\CustomerAuthController::getCompanyUser'], ['id'], ['GET' => 0], null, false, true, null]],
        267 => [[['_route' => 'api_customer_update_user_role', '_controller' => 'App\\Controller\\CustomerAuthController::updateUserRole'], ['id'], ['PUT' => 0], null, false, false, null]],
        281 => [[['_route' => 'api_customer_update_user_status', '_controller' => 'App\\Controller\\CustomerAuthController::updateUserStatus'], ['id'], ['PUT' => 0], null, false, false, null]],
        290 => [[['_route' => 'api_customer_remove_user', '_controller' => 'App\\Controller\\CustomerAuthController::removeCompanyUser'], ['id'], ['DELETE' => 0], null, false, true, null]],
        329 => [[['_route' => 'api_customer_cancel_invitation', '_controller' => 'App\\Controller\\CustomerInvitationController::cancelInvitation'], ['id'], ['DELETE' => 0], null, false, false, null]],
        343 => [[['_route' => 'api_accept_invitation', '_controller' => 'App\\Controller\\CustomerInvitationController::acceptInvitation'], ['token'], ['POST' => 0], null, false, false, null]],
        355 => [[['_route' => 'api_invitation_info', '_controller' => 'App\\Controller\\CustomerInvitationController::getInvitationInfo'], ['token'], ['GET' => 0], null, false, false, null]],
        393 => [[['_route' => 'api_public_invitation_info', '_controller' => 'App\\Controller\\PublicInvitationController::getInvitationInfo'], ['token'], ['GET' => 0], null, false, false, null]],
        407 => [
            [['_route' => 'api_public_accept_invitation', '_controller' => 'App\\Controller\\PublicInvitationController::acceptInvitation'], ['token'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
