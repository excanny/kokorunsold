<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Front');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Front::construction');
$routes->get('/homepage', 'Front::index');
$routes->get('/privacy', 'Front::privacy');
$routes->get('/terms', 'Front::terms');
$routes->get('/login', 'Front::login');
$routes->get('/loginaction', 'Front::loginaction');
$routes->get('/logout', 'Front::logout');
$routes->get('/register', 'Front::register');
$routes->post('/registeraction', 'Front::registeraction');
$routes->get('/cregister', 'Front::cregister');
$routes->get('/clogin', 'Front::clogin');
$routes->get('/sregister', 'Front::sregister');
$routes->get('/slogin', 'Front::slogin');
$routes->get('/aregister', 'Front::aregister');
$routes->get('/alogin', 'Front::alogin');
$routes->get('/forgot', 'Front::forgot');
$routes->get('/profilesetup', 'Front::profilesetup');
$routes->get('/register/verify/email', 'Front::registerverifyemail');
$routes->get('/register/verify/phone', 'Front::registerverifyphone');
$routes->get('/register/confirm/email', 'Front::cregisterverifyemail');


$routes->get('/user/dashboard', 'User::dashboard');
$routes->get('/user/education', 'User::education');
$routes->get('/user/experiences_container', 'User::experiences_container');
$routes->get('/user/dashboard', 'User::dashboard');
$routes->get('/user/teams', 'User::teams');
$routes->get('/user/jobdash', 'User::jobdash');
$routes->get('/user/events', 'User::events');
$routes->post('/user/createeventaction', 'User::createeventaction');
$routes->get('/user/allaboutyou', 'User::allaboutyou');
$routes->get('/user/recommendations', 'User::recommendations');
$routes->post('/user/recommendation/request', 'User::recommendationrequest');
$routes->post('user/recommendation/offer', 'User::recommendationoffer');
$routes->get('user/settings', 'User::settings');
$routes->get('user/search', 'User::search');
$routes->get('user/messages', 'User::messagesinbox');
$routes->get('user/messages/sent', 'User::messagessent');
$routes->get('/user/jobs', 'User::jobs');
$routes->get('/user/myjobs', 'User::myjobs');
$routes->get('/user/createjob', 'User::createjob');


$routes->get('/company/jobdash', 'Company::jobdash');
$routes->get('/company/messages/inbox', 'Company::inbox');
$routes->get('/company/messages/sent', 'Company::sent');

$routes->get('/school/jobdash', 'School::jobdash');
$routes->get('/school/messages/inbox', 'School::inbox');
$routes->get('/school/messages/sent', 'School::sent');


$routes->get('/association/jobdash', 'Association::jobdash');
$routes->get('/association/messages/inbox', 'Association::inbox');
$routes->get('/association/messages/sent', 'Association::sent');


$routes->get('/company/getjobmatches/(:any)', 'Company::getjobmatches/$1');

$routes->get('/user/recommendation/request/details/(:any)', 'User::RecommendationRequestDetails/$1');
$routes->get('/user/recommendation/request/accept/(:any)', 'User::RecommendationRequestAccept/$1');
$routes->get('/user/recommendation/offer/details/(:any)', 'User::RecommendationOfferDetails/$1');
$routes->get('/company/message/details/(:any)/(:any)', 'Company::messagedetails/$1/$2');
$routes->get('/school/message/details/(:any)/(:any)', 'School::messagedetails/$1/$2');
$routes->get('/association/message/details/(:any)/(:any)', 'Association::messagedetails/$1/$2');

$routes->get('/company/dashboard/(:any)', 'Company::dashboard/$1');
$routes->get('/association/dashboard/(:any)', 'Association::dashboard/$1');
$routes->get('/school/dashboard/(:any)', 'School::dashboard/$1');

$routes->get('/user/event/(:any)', 'User::event/$1');
$routes->get('/message/(:any)', 'User::message/$1');
$routes->get('/team/(:any)/messages', 'User::teammessages/$1');

$routes->get('user/jobdashmessages/(:any)', 'User::jobdashmessages/$1');

$routes->get('verifyemail/(:any)/(:any)', 'Front::CompanyVerifyEmail/$1/$2');
$routes->get('/(:any)', 'Front::profile/$1');

// $routes->get('company/getjobmatches/(:any)', 'Company::getjobmatches/$1');





/**
 * --------------------------------------------------------------------
 * Additional$routes->get('/user/events', 'user::events'); Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
