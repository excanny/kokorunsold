<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserExperienceModel;
use App\Models\UserEducationModel;
use App\Models\UserPortfolioModel;
use App\Models\UserDocumentModel;
use App\Models\UserWebLinkModel;
use App\Models\UserProSkillModel;
use App\Models\UserOtherSkillModel;
use App\Models\CompanyModel;
use App\Models\SchoolModel;
use App\Models\AssociationModel;
use App\Models\LGAModel;
use App\Models\ProfessionModel;
use App\Models\UserVerificationEmailModel;
use App\Models\CompanyVerificationEmailModel;
use App\Libraries\Zebra_Image;



class Front extends BaseController
{
	public function __construct()
    {
		$validation =  \Config\Services::validation();

       // Site cookie
        if(empty($_COOKIE["_krs"]))
	    {

    	  $GUID = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));;
    	  $cookie_name = "_krs";
          $cookie_value = $GUID;
		  setcookie($cookie_name, $cookie_value, time() + (10 * 365 * 24 * 60 * 60), "/");
		  
		}
		
    }
	

	public function index()
	{
		// $check_verification = new userVerificationEmailModel();
		// $check_details = $check_verification->where('fcookie', $_COOKIE["_krs"])->where('fverified', 0)->orderBy('frecno', 'desc')->first();
		
		// if(!empty($check_details))
		// {
		// 	session()->setFlashdata('show_resend_email',  $check_details['femail']);
		// 	return redirect()->to(base_url('/register/verify/'.$check_details['fdevice']));
		// }

		return view('front/index');
		
	}

	public function construction()
	{
		// $check_verification = new userVerificationEmailModel();
		// $check_details = $check_verification->where('fcookie', $_COOKIE["_krs"])->where('fverified', 0)->orderBy('frecno', 'desc')->first();
		
		// if(!empty($check_details))
		// {
		// 	session()->setFlashdata('show_resend_email',  $check_details['femail']);
		// 	return redirect()->to(base_url('/register/verify/'.$check_details['fdevice']));
		// }

		return view('front/construction');
		
	}

	public function privacy()
	{
		return view('front/privacy');
	}

	public function terms()
	{
		return view('front/terms');
	}


	public function register()
	{
		return view('front/register');
	}

	public function registeraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
			'user-email-or-phone-number' => [
				'rules' => 'required|trim|is_unique[tusers.fuser_name]',
				'errors' => [
						'required' => 'Please input email or phone. Email or Phone is required!',
						'is_unique' => 'Email or Phone Number already in use. Use a different one.',
				]
			],
			'password' => [
						'label'  => 'Password',
						'rules'  => 'required|trim|min_length[8]',
						'errors' => [
							'min_length' => 'Your Password is too short. Use a minimum of 8 characters'
							]
						],
        	'password2' => [
				'rules' => 'required|matches[password]',
				'errors' => [
						'required' => 'Please input email or phone. Email or Phone is required!',
						'matches' => 'The Confirm Password field does not match the Password field.',
				]
			],
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/register'));
		}


		$token = mt_rand(10000, 99999);

		$token_exists = new userVerificationEmailModel();
		$token_details = $token_exists->where('ftoken', $token)->first();

		if(is_null($token_details))
		{
			$token_free = $token;
		}
		else
		{
			$token_free = $token .'0';
		}		$token = mt_rand(10000, 99999);

		$token_exists = new userVerificationEmailModel();
		$token_details = $token_exists->where('ftoken', $token)->first();

		if(is_null($token_details))
		{
			$token_free = $token;
		}
		else
		{
			$token_free = $token .'0';
		}


		$text_color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));


		if (filter_var($this->request->getPost('user-email-or-phone-number'), FILTER_VALIDATE_EMAIL)) 
		{

			$model = new userModel();

			$data = [
					'fuser_name' => $this->request->getPost('user-email-or-phone-number'),
					'fpass_word' => password_hash($this->request->getPost('password2'),PASSWORD_DEFAULT),
					'fprofile_image' => 'User_DP.png',
					'ftext_colour'  => $text_color,
				];

			//var_dump($data);
			$model->insert($data);
			$inserted = $model->affectedRows();
			if($inserted > 0)
			{
				$check = time();

				$email = \Config\Services::email();
	
				$email->setFrom('noreply@kokoruns.com', 'Kokoruns');
				$email->setTo($this->request->getPost('user-email-or-phone-number'));
	
				$email->setSubject('Kokoruns Registration Email Verification');
				$email->setMessage("<p style='text-align:center'>Your Kokoruns Verification code is <strong>$token_free</strong></p>");
					

					$data = [
						'femail' => $this->request->getPost('user-email-or-phone-number'),
						'ftoken'  => $token_free,
						'fcookie' => $_COOKIE["_krs"],
						'fdevice' => 'email'
						];

					//var_dump($data);

					$verify_email = new UserVerificationEmailModel();
					$verify_email->insert($data);

					session()->set('verify_email', $this->request->getPost('user-email-or-phone-number'));
					session()->setFlashdata('success', 'Verification code sent to your email successfully.');

					return redirect()->to(base_url('/register/verify/email'));
			
				}

			}
		else if(substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0701' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0702' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0703' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0704' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0705' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0706' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0707' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0708' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0709' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0802' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0803' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0804' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0805'|| substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0806' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0807' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0808' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0809' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0810' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0811' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0812' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0813' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0814' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0815' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0816' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0817' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0818' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0819' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0901' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0902' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0903' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0904' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0905' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0906' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0907' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0908' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0909' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0910' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0911' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0912' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0913' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0914' || substr($this->request->getPost('user-email-or-phone-number'), 0, 4) == '0915')
		{
			$filtered_phone_number = filter_var($this->request->getPost('user-email-or-phone-number'), FILTER_SANITIZE_NUMBER_INT);
			// Remove "-" from number
			$phone_to_check = str_replace("-", "", $filtered_phone_number);
			// Check the lenght of number
			// This can be customized if you want phone number from a specific country
			if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) 
			{
				echo "Wrong phone number";
			} 
			else 
			{

				$model = new userModel();

				$data = [
					'fuser_name' => $this->request->getPost('user-email-or-phone-number'),
					'fpass_word'  => password_hash($this->request->getPost('password2'),PASSWORD_DEFAULT),
					'fprofile_image' => 'User_DP.png',
					'ftext_colour'  => $text_color,
					];

				//var_dump($data);
				$model->insert($data);
				$inserted = $model->affectedRows();
				if($inserted > 0)
				{ 
				   $phone = $this->request->getPost('user-email-or-phone-number');
    
			
					$curl = curl_init();
					
					curl_setopt_array($curl, array(
					CURLOPT_URL => "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=SwYVYdBMdgJon9gPk7P6eRbccAwrD00RiOOGm6vWDv6G63gmjuaPjzwWefyh&from=BulkSMS.ng&to=".$phone."&body=Your%20Kokoruns%20Verification%20code%20is%20".$token_free."&dnd=2",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => "POST",
					//CURLOPT_POSTFIELDS => json_encode($data),
					// CURLOPT_HTTPHEADER => array(
					// 	"x-api-key: SwYVYdBMdgJon9gPk7P6eRbccAwrD00RiOOGm6vWDv6G63gmjuaPjzwWefyh",
					// 	"Content-Type: application/json"
					// ),
					));
					
					$response = curl_exec($curl);
					
					curl_close($curl);
					//echo $resposession()->set('verify_email', $this->request->getPost('user-email-or-phone-number'));nse;


					$verify_email = new userVerificationEmailModel();

					$data = [
						'femail' => $this->request->getPost('user-email-or-phone-number'),
						'ftoken'  => $token_free,
						'fcookie' => $_COOKIE["_krs"],
						'fdevice' => 'phone'
						];

					//var_dump($data);
					$verify_email->insert($data);

					session()->set('verify_phone', $this->request->getPost('user-email-or-phone-number'));
					session()->setFlashdata('success', 'Verification code sent to your phone successfully.');

					return redirect()->to(base_url('/register/verify/phone'));

				}
			}

		}
		else
		{
			$data = [
				'error' => 'Enter a valid email or phone number.'
			];
	
			session()->setFlashdata('error', $data);

			return redirect()->to(base_url('/register'));
		}
		
	}



	public function verificationemailresend()
	{
		
		$token = mt_rand(10000, 99999);

		$token_exists = new userVerificationEmailModel();
		$token_details = $token_exists->where('ftoken', $token)->first();

		if(is_null($token_details))
		{
			$token_free = $token;
		}
		else
		{
			$token_free = $token .'0';
		}

		if(session()->verify_email)
		{
			$resendemail = session()->verify_email;
		}
		else
		{
			$resendemail = $this->request->getPost('email');
		}

		$email = \Config\Services::email();

		$email->setFrom('noreply@kokoruns.com', 'Kokoruns');
		$email->setTo($resendemail);

		$email->setSubject('Kokoruns Registration Email Verification');
		$email->setMessage("<p style='text-align:center'>Your Kokoruns Verification code is <strong>$token_free</strong></p>");

		
		if ($email->send()) 
		{
			
			$data = [
				'femail' => $resendemail,
				'ftoken'  => $token_free,
				'fcookie' => $_COOKIE["_krs"],
				'fdevice' => 'email'
				];

			//var_dump($data);
			$verify_email = new userVerificationEmailModel();

			$verify_email->insert($data);

			session()->set('verify_email', $this->request->getPost('email'));

			session()->setFlashdata('success', 'Verification code sent to your email successfully.');

			return redirect()->to(base_url('/register/verify/email'));
		} 
		else 
		{
			echo $email->printDebugger();
			return false;
		}

	}


	public function verificationphoneresend()
	{

		$token = mt_rand(10000, 99999);

		$token_exists = new userVerificationEmailModel();
		$token_details = $token_exists->where('ftoken', $token)->first();

		if(is_null($token_details))
		{
			$token_free = $token;
		}
		else
		{
			$token_free = $token .'0';
		}
		
		if(session()->verify_phone)
		{
			$resendphone = session()->verify_phone;
		}
		else
		{
			$resendphone = $this->request->getPost('phone');
		}
    
			
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=SwYVYdBMdgJon9gPk7P6eRbccAwrD00RiOOGm6vWDv6G63gmjuaPjzwWefyh&from=BulkSMS.ng&to=".$resendphone."&body=Your%20Kokoruns%20Verification%20code%20is%20".$token_free."&dnd=2",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => "POST",
		//CURLOPT_POSTFIELDS => json_encode($data),
		// CURLOPT_HTTPHEADER => array(
		// 	"x-api-key: SwYVYdBMdgJon9gPk7P6eRbccAwrD00RiOOGm6vWDv6G63gmjuaPjzwWefyh",
		// 	"Content-Type: application/json"
		// ),
		));
		
		$response = curl_exec($curl);
		
		curl_close($curl);
		//echo $resposession()->set('verify_email', $this->request->getPost('user-email-or-phone-number'));nse;


		$verify_email = new userVerificationEmailModel();

		$data = [
			'femail' => $this->request->getPost('phone'),
			'ftoken'  => $token_free,
			'fcookie' => $_COOKIE["_krs"],
			'fdevice' => 'phone'
			];

		//var_dump($data);
		$verify_email->insert($data);

		session()->set('verify_phone', $this->request->getPost('phone'));
		session()->setFlashdata('success', 'Verification code sent to your phone successfully.');

		return redirect()->to(base_url('/register/verify/phone'));

	}


	public function RegisterVerifyEmail()
	{

		return view('front/registerverifyemail');
	}


	public function RegisterVerifyEmailAction()
	{
		//var_dump($this->request->getPost());
	    if (! $this->validate([

			'verify-code-email' => 'required|trim'
			
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/register/verify/email'));
		}
	    
			$code  = $this->request->getPost('verify-code-email');
    	    $verify = new userVerificationEmailModel();
			$token_details = $verify->where('ftoken', $code)->first();
	
			//var_dump($user_details);
    	    if(is_null($token_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! You entered an invalid verification code. You can click resend to get another one');
				return redirect()->to(base_url('/register/verify/email'));
    	    }
    	    else
    	    {
    	 

				session()->setFlashdata('success', 'Email verified successfully. Now you can log in.');

				return redirect()->to(base_url('/login'));

    	    }
	}


	public function CRegisterVerifyEmail()
	{
	
		return view('front/cregisterverifyemail');
	}


	public function CRegisterVerifyEmailAction()
	{
		//var_dump($this->request->getPost());
	    if (! $this->validate([

			'verify-code-email' => 'required|trim'
			
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/register/confirm/email'));
		}
	    
			$code  = $this->request->getPost('verify-code-email');
    	    $verify = new CompanyVerificationEmailModel();
			$token_details = $verify->where('ftoken', $code)->first();
	
			//var_dump($token_details);
    	    if(is_null($token_details))
    	    {

				session()->setFlashdata('error', 'Sorry! You entered an invalid verification code. You can click resend to get another one');
				return redirect()->to(base_url('/register/confirm/email'));
    	    }
    	    else
    	    {
				$id = $token_details['finserted_id'];

				$data = [
					'fverified' => 1,
				];

				//var_dump($id);

				$am = new AssociationModel();
				$updated = $am->update($id, $data);

				if($updated > 0)
				{
					
					$association_details = $am->where('frecno', $id)->first();
					//var_dump($association_details);

					$sessiondata = [

						'id' => $association_details['frecno'],
						'association_id' => $association_details['fassociation_id'],
						'association_name' => $association_details['fassociation_name'],
						'association_email' => $association_details['fassociation_email'],
	
					];
	
					session()->set($sessiondata);
					//var_dump($sessiondata);

					return redirect()->to(base_url('/association/dashboard'));

				}

    	    }
	}


	public function registerverifyphone()
	{

		return view('front/registerverifyphone');
	}

	public function registerverifyphoneaction()
	{
		if (! $this->validate([

			'verify-code-email' => 'required|trim'
			
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/register/verify/phone'));
		}
	    
			$code  = $this->request->getPost('verify-code-email');
    	    $verify = new userVerificationEmailModel();
			$token_details = $verify->where('ftoken', $code)->first();
	
			//var_dump($user_details);
    	    if(is_null($token_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! You entered an invalid verification code. You can click resend to get another one');
				return redirect()->to(base_url('/register/verify/phone'));
    	    }
    	    else
    	    {
				session()->setFlashdata('success', 'Phone number verified successfully. Now you can log in.');
				return redirect()->to(base_url('/login'));

    	    }
	}

	public function login()
	{


		// $data = [];
		// require_once APPPATH.'Libraries/vendor/autoload.php';
		// $facebook = new \Facebook\Facebook(
		// 	[
		// 		'app_id'      => '391222708680549',
		// 		'app_secret'     => '9287a49104438476b8c8edbdfed318f7',
		// 		'default_graph_version'  => 'v2.10'
		// 	  ]
		// );

		// $facebook_helper = $facebook->getRedirectLoginHelper();

		// if($this->request->getPost('code'))
		// {
		// 	if(session()->get('access_token'))
		// 	{
		// 		$access_token = session()->get('access_token');
		// 	}
		// 	else
		// 	{
		// 		$access_token = $facebook_helper->getAccessToken();

		// 		session()->set('access_token', $access_token);

		// 		$facebook->setDefaultAccessToken(session()->get('access_token'));
		// 	}

		// 	$graph_response = $facebook->get("/me?fields=name,email,birthday", $access_token);

		// 	$facebook_user_info = $graph_response->getGraphUser();

		// 	if(!empty($facebook_user_info['id']))
		// 	{
			 
		// 	  $fbdata=[
		// 		  'profile_pic' => 'http://graph.facebook.com/'.$facebook_user_info['id'].'/picture',
		// 		  'user_name' => $facebook_user_info['name'],
		// 		  'user_email_address' => $facebook_user_info['email'],
		// 		  'userid' => $facebook_user_info['id'],
		// 	  ];

		// 	  session()->set($fbdata);

		// 	//   $json = file_get_contents('https://graph.facebook.com/me');
		// 	//   $detailObj = json_decode($json);
		// 	  //var_dump($facebook_user_info);

		// 	}

		// }
		// else
		// {
		// 	// Get login url
		// 	$facebook_permissions = ['email']; // Optional permissions

		// 	$data['facebook_login_url'] = $facebook_helper->getLoginUrl('https://kokoruns.com/', $facebook_permissions);
			
		// 	// Render Facebook login button
		// 	//$facebook_login_url = '<div align="center"><a href="'.$facebook_login_url.'"><img src="php-login-with-facebook.gif" /></a></div>';

		// }


		// $google_client = new \Google_Client();

		// 	$google_client->setClientId('389651429194-d2jm4kkcd6efm7knec1tjtmj3ol01gkn.apps.googleusercontent.com'); //Define your ClientID

		// 	$google_client->setClientSecret('NO0-8MePM7rCAxJ5u4GW7g8j'); //Define your Client Secret Key

		// 	$google_client->setRedirectUri('https://kokoruns.com/login'); //Define your Redirect Uri

		// 	$google_client->addScope('email');

		// 	$google_client->addScope('profile');

			

		
		// 	  if($this->request->getVar('code'))
		// 	  {
		// 		$token = $google_client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));

		// 		if(!isset($token["error"]))
		// 		{
		// 			$google_client->setAccessToken($token['access_token']);

		// 			session()->set('access_token', $token['access_token']);

		// 			$google_service = new Google_Service_Oauth2($google_client);

		// 			$data = $google_service->userinfo->get();

		// 			print_r($data);

		
					// if($this->google_login_model->Is_already_register($data['id']))
					// {
					// 	//update data
					// 	$user_data = array(
					// 	'first_name' => $data['given_name'],
					// 	'last_name'  => $data['family_name'],
					// 	'email_address' => $data['email'],
					// 	'profile_picture'=> $data['picture'],
					// 	'updated_at' => $current_datetime
					// 	);

					// 	$this->google_login_model->Update_user_data($user_data, $data['id']);
					// }
					// else
					// {
					// 	//insert data
					// 	$user_data = array(
					// 	'login_oauth_uid' => $data['id'],
					// 	'first_name'  => $data['given_name'],
					// 	'last_name'   => $data['family_name'],
					// 	'email_address'  => $data['email'],
					// 	'profile_picture' => $data['picture'],
					// 	'created_at'  => $current_datetime
					// 	);

					// 	$this->google_login_model->Insert_user_data($user_data);
					// }

					// $user_data = array(
					// 	'first_name' => $data['given_name'],
					// 	'last_name'  => $data['family_name'],
					// 	'email_address' => $data['email'],
					// 	'profile_picture'=> $data['picture'],
					// 	'updated_at' => $current_datetime
					// 	);

					// session()->set($user_data);
					// $newdata = [
					// 	'username'  => 'emmanuel',
					// 	'email'     => 'johndoe@some-site.com',
					// 	'logged_in' => TRUE
					// ];
					
					// session()->set($newdata);
			// 	}
			// }

			// $login_button = '';
			// if(!session()->access_token)
			// {
			// 	$data['loginButton'] = $google_client->createAuthUrl();
		
			// }

		//return view('front/login', $data);

		return view('front/login');
		
	}

	public function loginaction()
	{
		if (! $this->validate([

			'user-email-or-phone-number' => "required|trim",
			'password'  => 'required|trim',
    
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/login'));
		}

		$user_name  = $this->request->getPost('user-email-or-phone-number');
		$pass_word  = $this->request->getPost('password');

		//check credentials
		$user = new userModel();
		$user_details = $user->where('fuser_name', $user_name)->first();

		///var_dump($user_details);
		
		if(!empty($user_details))
		{
			//$user_details = (object)$user_details2;
			$validate = password_verify($pass_word, $user_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				//Check if profile setup has been done, if not redirect to profile setup
				if($user_details['factive'] == 0)
				{
					//set id session
					$sessiondata = [
						'id' => $user_details['frecno'],
						'username' => $user_details['fuser_name'],
					];

					session()->set($sessiondata);

					if(session()->has('id'))
					{
						return redirect()->to(base_url('/profilesetup'));
					}
					else
					{
						session()->setFlashdata('error', 'Error occurred! Try again');
						return redirect()->to(base_url('/login'));
					}
				}
				else //setup done,send to dashboard
				{
					$sessiondata = [
						'id' => $user_details['frecno'],
						'username' => $user_details['fuser_name'],
						'first_name' => $user_details['ffirst_name'],
						'last_name' => $user_details['flast_name'],
						'user_id' => $user_details['fuser_id'],
						'job_title' => $user_details['fprofession'],
						'company' => $user_details['fcurrent_employer'],
						'user_text_color' => $user_details['ftext_colour'],
					];

					session()->set($sessiondata);

					//var_dump(session()->user_id);

					return redirect()->to(base_url('/user/dashboard'));
				}
			}
			else
			{
				session()->setFlashdata('error', "Wrong Password");
				return redirect()->to(base_url('/login'));
			}

		}
		else
		{
			session()->setFlashdata('error', "Email or Phone does not exist in the system");
			return redirect()->to(base_url('/login'));
		}

		
	}

	public function modalloginaction()
	{
		if (! $this->validate([

			'email' => "required|trim",
			'password'  => 'required|trim',
    
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/login'));
		}

		$user_name  = $this->request->getPost('email');
		$pass_word  = $this->request->getPost('password');

		//check credentials
		$user = new userModel();
		$user_details = $user->where('fuser_name', $user_name)->first();

		///var_dump($user_details);
		
		if(!empty($user_details))
		{
			//$user_details = (object)$user_details2;
			$validate = password_verify($pass_word, $user_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				
				//Check if profile setup has been done, if not redirect to profile setup
				if($user_details['factive'] == 0)
				{
					//set id session
					$sessiondata = [
						'id' => $user_details['frecno'],
						'username' => $user_details['fuser_name'],
					];

					session()->set($sessiondata);

					if(session()->has('id'))
					{
						// return redirect()->to(base_url('/profilesetup'));

						// return response()->json(['url'=> $url, 'error' => 0]);
						return $this->response->setJSON(['url'=> base_url('/profilesetup'), 'error' => 0]);
      
					}
					else
					{
						// session()->setFlashdata('error', 'Error occurred! Try again');
						// return redirect()->to(base_url('/login'));
						return $this->response->setJSON(['url'=> base_url('/cregister'), 'error' => 0]);
					}
				}
				else //setup done,send to dashboard
				{
					$sessiondata = [
						'id' => $user_details['frecno'],
						'username' => $user_details['fuser_name'],
						'user_id' => $user_details['fuser_id'],
						'job_title' => $user_details['fprofession'],
					];

					session()->set($sessiondata);

					//return redirect()->to(base_url('/user/dashboard'));
					return $this->response->setJSON(['url'=> base_url('/cregister'), 'error' => 0]);
				}
			}
			else
			{

				return $this->response->setJSON(['msg' => 'wrong-password-msg']);
			}

		}
		else
		{

			//return $this->response->setJSON($response);
			return $this->response->setJSON(['url'=> base_url('/register'), 'error' => 1, 'msg' => 'no-email-found']);

		}
		
	}

	public function cregister()
	{
		if(session()->user_id)
		{
			$data['show_modal'] = false;
			return view('front/cregister', $data);
		}
		else
		{
			$data['show_modal'] = true;
			return view('front/cregister', $data);
		}
		
	}


	public function cregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
			'company_name' => 'required|trim|is_unique[tcompanies.fcompany_name]',
			'company_id' => 'required|trim',
			'company_address' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Please input address. Address is required!',
				]
			],

			'company_number' => "required|trim",
			'state' => "required|trim",
			'lga'  => 'required|trim',
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/cregister'));
		}


		$company = new CompanyModel();
		$company_details = $company->where('fcompany_id', strtolower($this->request->getPost('company_id')))->first();

		//var_dump($company_details);

		if(empty($company_details))
		{

			$company_id = strtolower($this->request->getPost('company_id'));

		}
		else
		{

			// //user ID already exists. Add 1 to it
			$id = $company_details['frecno'];
			$id2 = $id + 4;
			$company_id = $company_details['fcompany_id'] . '.' . $id2;
			//echo $company_details['fcompany_name'];

		}

	
		if($_FILES["company_logo"]["error"] == 4)
		{
			$ghka = 'Company_DP.jpg';
		}
		else
		{
		
			$picd = $this->request->getFile('company_logo');
			$ghka = $picd->getRandomName();
			
			$full_path = $picd->getTempName();

			// create a new instance of the class
		
			$image = new Zebra_Image();

			// if you handle image uploads from users and you have enabled exif-support with --enable-exif
			// (or, on a Windows machine you have enabled php_mbstring.dll and php_exif.dll in php.ini)
			// set this property to TRUE in order to fix rotation so you always see images in correct position
			$image->auto_handle_exif_orientation = false;

			// indicate a source image (a GIF, PNG or JPEG file)
			$image->source_path = $full_path;

			// indicate a target image
			// note that there's no extra property to set in order to specify the target
			// image's type -simply by writing '.jpg' as extension will instruct the script
			// to create a 'jpg' file
			$image->target_path = 'public/companygalleries/logos/'. $ghka;

			// since in this example we're going to have a jpeg file, let's set the output
			// image's quality
			$image->jpeg_quality = 100;

			// some additional properties that can be set
			// read about them in the documentation
			$image->preserve_aspect_ratio = true;
			$image->enlarge_smaller_images = true;
			$image->preserve_time = true;
			$image->handle_exif_orientation_tag = true;

			// resize the image to exactly 100x100 pixels by using the "crop from center" method
			// (read more in the overview section or in the documentation)
			//  and if there is an error, check what the error is about
			if (!$image->resize(200, 200, ZEBRA_IMAGE_CROP_CENTER)) {

				// if there was an error, let's see what the error is about
				switch ($image->error) {

					case 1:
						echo 'Source file could not be found!';
						break;
					case 2:
						echo 'Source file is not readable!';
						break;
					case 3:
						echo 'Could not write target file!';
						break;
					case 4:
						echo 'Unsupported source file format!';
						break;
					case 5:
						echo 'Unsupported target file format!';
						break;
					case 6:
						echo 'GD library version does not support target file format!';
						break;
					case 7:
						echo 'GD library is not installed!';
						break;
					case 8:
						echo '"chmod" command is disabled via configuration!';
						break;
					case 9:
						echo '"exif_read_data" function is not available';
						break;

				}
			// if no errors
			} //else echo 'Success!';
		}

		$model = new CompanyModel();

		$data = [
			'fcompany_name' => $this->request->getPost('company_name'),
			'fcompany_id' => $company_id,
			'fphone' => $this->request->getPost('company_number'),
			'fcac' => $this->request->getPost('cac'),
			'fcompany_type' => $this->request->getPost('company_type'),
			'fcompany_size' => $this->request->getPost('company_size'),
			'fcompany_industry' => $this->request->getPost('industry1'),
			'flogo' => $ghka,
			'fwebsite' => $this->request->getPost('website'),
			'fcompany_address' => $this->request->getPost('company_address'),
			'fmain_office_location_state' => $this->request->getPost('state'),
			'fmain_office_location_lga' => $this->request->getPost('lga'),
			'fauthor' => session()->user_id,
 
			];

		//var_dump($data);


		$model->insert($data);
		$inserted = $model->affectedRows();
		if($inserted > 0)
		{
			// session()->setFlashdata('success', 'Registration successful. Now you can login into your account.');
			// return redirect()->to(base_url('/clogin'))

			$csessiondata = [

				'id' => $model->insertID(),
				'company_id' => $company_id,
				// 'company_name' => $company_details['fcompany_name'],

			];

			session()->set($csessiondata);

			return redirect()->to(base_url('/company/dashboard/'. $company_id));
				
		} 
		else 
		{
			//echo $email->printDebugger();
			return false;
		}
		
	}


	public function sregister()
	{
		if(session()->user_id)
		{
			$data['show_modal'] = false;
			return view('front/sregister', $data);
		}
		else
		{
			$data['show_modal'] = true;
			return view('front/sregister', $data);
		}
	}


	public function sregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
	
			'school_name' => 'required|trim',
			'school_id' => 'required|trim',
			'state' => "required|trim",
			'lga'  => 'required|trim',
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/sregister'));
		}


		$school = new SchoolModel();
		$school_details = $school->where('fschool_id', strtolower($this->request->getPost('school_id')))->first();

		//var_dump($company_details);

		if(empty($school_details))
		{

			$school_id = strtolower($this->request->getPost('school_id'));

		}
		else
		{

			// //user ID already exists. Add 1 to it
			$id = $school_details['frecno'];
			$id2 = $id + 4;
			$school_id = $school_details['fschool_id'] . '.' . $id2;
			//echo $company_details['fcompany_name'];

		}


		if($_FILES["school_logo"]["error"] == 4)
		{
			$ghka = 'School_DP.png';
		}
		else
		{
		
			$picd = $this->request->getFile('school_logo');
			$ghka = $picd->getRandomName();
			
			$full_path = $picd->getTempName();

			// create a new instance of the class
		
			$image = new Zebra_Image();

			// if you handle image uploads from users and you have enabled exif-support with --enable-exif
			// (or, on a Windows machine you have enabled php_mbstring.dll and php_exif.dll in php.ini)
			// set this property to TRUE in order to fix rotation so you always see images in correct position
			$image->auto_handle_exif_orientation = false;

			// indicate a source image (a GIF, PNG or JPEG file)
			$image->source_path = $full_path;

			// indicate a target image
			// note that there's no extra property to set in order to specify the target
			// image's type -simply by writing '.jpg' as extension will instruct the script
			// to create a 'jpg' file
			$image->target_path = 'public/schoolgalleries/logos/'. $ghka;

			// since in this example we're going to have a jpeg file, let's set the output
			// image's quality
			$image->jpeg_quality = 100;

			// some additional properties that can be set
			// read about them in the documentation
			$image->preserve_aspect_ratio = true;
			$image->enlarge_smaller_images = true;
			$image->preserve_time = true;
			$image->handle_exif_orientation_tag = true;

			// resize the image to exactly 100x100 pixels by using the "crop from center" method
			// (read more in the overview section or in the documentation)
			//  and if there is an error, check what the error is about
			if (!$image->resize(200, 200, ZEBRA_IMAGE_CROP_CENTER)) {

				// if there was an error, let's see what the error is about
				switch ($image->error) {

					case 1:
						echo 'Source file could not be found!';
						break;
					case 2:
						echo 'Source file is not readable!';
						break;
					case 3:
						echo 'Could not write target file!';
						break;
					case 4:
						echo 'Unsupported source file format!';
						break;
					case 5:
						echo 'Unsupported target file format!';
						break;
					case 6:
						echo 'GD library version does not support target file format!';
						break;
					case 7:
						echo 'GD library is not installed!';
						break;
					case 8:
						echo '"chmod" command is disabled via configuration!';
						break;
					case 9:
						echo '"exif_read_data" function is not available';
						break;

				}
			// if no errors
			} //else echo 'Success!';
		}

		
		$data = [

			'fschool_name' => $this->request->getPost('school_name'),
			'fschool_id' => $school_id,
			'fschool_type' => $this->request->getPost('school_type'),
			'fschool_size'  => $this->request->getPost('school_size'),
			'fschool_industry'  => $this->request->getPost('school_industry'),
			'fwebsite'  => $this->request->getPost('website'),
			'fschool_size'  => $this->request->getPost('school_size'),
			'fschool_address' => $this->request->getPost('address'),
			'fmain_office_location_state' => $this->request->getPost('state'),
			'fmain_office_location_lga' => $this->request->getPost('lga'),
			'fauthor' => session()->user_id,
			'flogo' => $ghka,
			];

		//var_dump($data);

		$model = new SchoolModel();
		$model->insert($data);
		$inserted = $model->affectedRows();
		if($inserted > 0)
		{
			$csessiondata = [


				'id' => $model->insertID(),
				'school_id' => $school_id,

			];

			session()->set($csessiondata);
			return redirect()->to(base_url('/school/dashboard'));
		}
	}


	public function aregister()
	{
		if(session()->user_id)
		{
			$data['show_modal'] = false;
			return view('front/aregister', $data);
		}
		else
		{
			$data['show_modal'] = true;
			return view('front/aregister', $data);
		}
	}

	public function aregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
		
			'association_name' => 'required|trim',
			'association_number' => "required|trim",
			'state' => "required|trim",
			'lga'  => 'required|trim',
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/aregister'));
		}


		
		//echo $token_free;

		//var_dump($this->request->getPost('association_email'));



			$association = new AssociationModel();
			$association_details = $association->where('fassociation_id', strtolower($this->request->getPost('association_id')))->first();

			//($this->request->getPost('association_name'));
		

			if(empty($association_details))
			{

				$association_id = strtolower($this->request->getPost('association_id'));

			}
			else
			{
				// //user ID already exists. Add 1 to it
				$id = $association_details['frecno'];
				$id2 = $id + 4;
				$association_id = $association_details['fassociation_id'] . '.' . $id2;
				//echo $company_details['fcompany_name'];

			}


		if($_FILES["association_logo"]["error"] == 4)
		{
			$ghka = 'Association_DP.jpg';
		}
		else
		{
			$picd = $this->request->getFile('association_logo');
			$ghka = $picd->getRandomName();
			
			$full_path = $picd->getTempName();

			// create a new instance of the class
		
			$image = new Zebra_Image();

			// if you handle image uploads from users and you have enabled exif-support with --enable-exif
			// (or, on a Windows machine you have enabled php_mbstring.dll and php_exif.dll in php.ini)
			// set this property to TRUE in order to fix rotation so you always see images in correct position
			$image->auto_handle_exif_orientation = false;

			// indicate a source image (a GIF, PNG or JPEG file)
			$image->source_path = $full_path;

			// indicate a target image
			// note that there's no extra property to set in order to specify the target
			// image's type -simply by writing '.jpg' as extension will instruct the script
			// to create a 'jpg' file
			$image->target_path = 'public/associationgalleries/logos/'. $ghka;

			// since in this example we're going to have a jpeg file, let's set the output
			// image's quality
			$image->jpeg_quality = 100;

			// some additional properties that can be set
			// read about them in the documentation
			$image->preserve_aspect_ratio = true;
			$image->enlarge_smaller_images = true;
			$image->preserve_time = true;
			$image->handle_exif_orientation_tag = true;

			// resize the image to exactly 100x100 pixels by using the "crop from center" method
			// (read more in the overview section or in the documentation)
			//  and if there is an error, check what the error is about
			if (!$image->resize(200, 200, ZEBRA_IMAGE_CROP_CENTER)) {

				// if there was an error, let's see what the error is about
				switch ($image->error) {

					case 1:
						echo 'Source file could not be found!';
						break;
					case 2:
						echo 'Source file is not readable!';
						break;
					case 3:
						echo 'Could not write target file!';
						break;
					case 4:
						echo 'Unsupported source file format!';
						break;
					case 5:
						echo 'Unsupported target file format!';
						break;
					case 6:
						echo 'GD library version does not support target file format!';
						break;
					case 7:
						echo 'GD library is not installed!';
						break;
					case 8:
						echo '"chmod" command is disabled via configuration!';
						break;
					case 9:
						echo '"exif_read_data" function is not available';
						break;

				}
			// if no errors
			} //else echo 'Success!';


			}

			$data = [
				
				'fassociation_name' => $this->request->getPost('association_name'),
				'fassociation_id' => $association_id,
				'fassociation_type' => $this->request->getPost('association_type'),
				'fassociation_size'  => $this->request->getPost('association_size'),
				'fassociation_industry'  => $this->request->getPost('association_industry'),
				'fwebsite'  => $this->request->getPost('website'),
				'fassociation_address' => $this->request->getPost('association_address'),
				'fmain_office_location_state' => $this->request->getPost('state'),
				'fmain_office_location_lga' => $this->request->getPost('lga'),
				'fauthor' => session()->user_id,
				'flogo' => $ghka,
				
				];

			//var_dump($data);

			$model = new AssociationModel();
			$model->insert($data);
			$inserted = $model->affectedRows();
			$inserted_ID = $model->insertID();

			if($inserted > 0)
			{
				$asessiondata = [


					'id' => $model->insertID(),
					'association_id' => $association_id,
	
				];
	
				session()->set($asessiondata);
				return redirect()->to(base_url('/association/dashboard'));
			}
	}


	public function profilesetup()
	{
		if(!session()->id)
		{
			return redirect()->to(base_url('/login'));
		}

		$profession = new ProfessionModel();
		$data['professions'] = $profession->orderBy('fname', 'asc')->findAll();

		//var_dump($professions);

		return view('front/profilesetup', $data);
	}

	public function profilesetupaction()
	{
		//var_dump($this->request->getPost());
		if (! $this->validate([

			'first_name' => 'required|trim',
			'last_name' => 'required|trim',
			'user_phonenum' => 'required|trim',
			'age_range' => 'required|trim',
			'profession_or_craft' => 'required|trim',
			'employment_type' => 'required|trim',
			'educational_qualification' => 'required|trim',
			'state' => 'required|trim',
			'lga' => 'required|trim',
			
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/profilesetup'));
		}
		
		if(!session()->id)
		{
			session()->setFlashdata('error', 'Sorry! Error occurred. Try again');
			return redirect()->to(base_url('/login'));
		}
		else
		{
			$id = session()->id;
		}

		$user = new userModel();
		$user_details = $user->where('fuser_id', strtolower($this->request->getPost('first_name'). '.' . $this->request->getPost('last_name')))->first();
		if(empty($user_details))
		{
			$user_id = strtolower($this->request->getPost('first_name'). '.' . $this->request->getPost('last_name'));

		}
		else
		{
			//user ID already exists. Add 1 to it
			$id2 = $id + 3;
			$user_id = $user_details['fuser_id']. '.' . $id2;
			
		}

		$address = $this->request->getPost('house_no') . ',' . ' ' .$this->request->getPost('user_street/estate');
		
		$data = [

			'ffirst_name'  => $this->request->getPost('first_name'),
			'flast_name'  => $this->request->getPost('last_name') ,
			'fuser_id'  => $user_id,
			'fphone'  => $this->request->getPost('user_phonenum'),
			'fage_range'  => $this->request->getPost('age_range'),
			'fprofession'  => $this->request->getPost('profession_or_craft'),
			'fother_professions1'  => $this->request->getPost('other_professions1'),
			'fother_professions2'  => $this->request->getPost('other_professions2'),
			'fother_professions3'  => $this->request->getPost('other_professions3'),
			'fother_professions4'  => $this->request->getPost('other_professions4'),
			'femployment_type'  => $this->request->getPost('employment_type'),
			'feducational_qualification'  => $this->request->getPost('educational_qualification'),
			'fstate'  => $this->request->getPost('state'),
			'flga'  => $this->request->getPost('lga'),
			'factive'  => 1,

			];

		//var_dump($data);

		$usermodel = new userModel();
		$updated = $usermodel->update($id, $data);
		if($updated > 0)
		{
			
			$sessiondata = [
				'user_id' => $user_id,
			];

			session()->set($sessiondata);
			
			return redirect()->to(base_url('user/dashboard'));
		}
		else
		{
			session()->setFlashdata('error', 'Profile not updated. Try again');
			return redirect()->to(base_url('/login'));
		}

	}


	public function profile($userid)
	{
		// echo $name;
		$user = new userModel();
		$user_details = $user->where('fuser_id', $userid)->first();

		if(empty($user_details))
		{
			//session()->setFlashdata('error', "Page not found");
			//return view('front/404');
		}
		else
		{
			$data['user_details'] = $user_details;

			$user_experience = new UserExperienceModel();

			$data['user_experiences'] = $user_experience->where('fuser_id', $userid)->orderBy('frecno', 'desc')->findAll();

			$user_education = new UserEducationModel();
			$data['user_educations'] = $user_education->where('fuser_id', $userid)->orderBy('frecno', 'desc')->findAll();

			$pro_skill = new UserProSkillModel();
			$data['pro_skills'] = $pro_skill->where('fuser_id', $userid)->orderBy('frecno', 'desc')->findAll();

			$other_skill = new UserOtherSkillModel();
			$data['other_skills'] = $other_skill->where('fuser_id', $userid)->orderBy('frecno', 'desc')->findAll();

			//var_dump($data['user_experiences']);
			return view('front/profile', $data);	
		}

	}




	public function forgot()
	{
		return view('front/forgot');
	}

	public function forgetpasswordaction()
	{
		//var_dump($this->request->getPost());
	    if (! $this->validate([

			'user-email-or-phone-number' => 'required|trim'
			
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/forgot'));
		}
	    
			$email  = $this->request->getPost('user-email-or-phone-number');
    	    $user = new userModel();
			$user_details = $user->where('fuser_name', $email)->first();
	
			//var_dump($user_details);
    	    if(is_null($user_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! Username not registered in the system.');
				return redirect()->to(base_url('/forgot'));
    	    }
    	    else
    	    {
    	        $token2 = openssl_random_pseudo_bytes(16);
                $token = bin2hex($token2);
                $email = $user_details['fuser_name'];
				 
				 $data = [
					'ftoken' => $token,
					'femail'  => $email,
					];
                
				  $db  = \Config\Database::connect();
				  $db->table('tpass_word_resets')->insert($data);
				  $inserted = $db->affectedRows();
				
    	        if($inserted > 0)
    	        {
    	            // $email = \Config\Services::email();
					// $email->setFrom('your@example.com', 'Your Name');
					// $email->setTo('excanny@yahoo.com');
					// $email->setCC('another@another-example.com');
					// $email->setBCC('them@their-example.com');

					// $email->setSubject('Email Test');
					// $email->setMessage('Testing the email class.');

					// $email->send();

					// $email->printDebugger(['headers']);

					session()->setFlashdata('success', 'Reset link sent successfully. Check your email folders.');
					return redirect()->to(base_url('/forgot'));
        		    
				}
				else
				{
					session()->setFlashdata('error', 'Error occurred. Try again');
					return redirect()->to(base_url('/forgot'));
				}
    	    }
       
	}

	public function verifytoken()
	{
	//    $email = $this->uri->segment(3);
	//    $token = $this->uri->segment(4);
	//    $row = $this->user_model->VerifyToken($email, $token);
	//    //var_dump($row);
	//    if(!empty($row))
	//    {
	// 	  $this->session->set_userdata('create_password', 'ok');
	//       $this->session->set_userdata('recover_email', $email);
	//       redirect('w/CreatePassword');
	//    }
	//    else
	//    {
	// 	  $this->session->set_flashdata('error', 'Wrong or invalid token. Resend verification or reclick reset link in your email'); 
    // 	   redirect('w/ForgotPassword');
	//    }
	}
	
	public function createpassword()
	{
	    // if (!$this->session->userdata('create_password'))
		// {
		//     $this->session->set_flashdata('error', 'Wrong or invalid token.');  
		// 	redirect('w/ForgotPassword'); // the user is not logged in, redirect them!
		// }

		
	    // $this->load->view('main/create_password');
	}

	public function createpasswordaction()
	{
	    // $this->form_validation->set_rules('pssd', 'Password', 'trim|required');
        // $this->form_validation->set_rules('pssd2', 'Retype Password', 'trim|required|matches[pssd]');
        // if ($this->form_validation->run() == FALSE)
        // {
		// 	$this->session->set_flashdata('error', validation_errors());
		// 	redirect('w/CreatePassword');
			
        // }
        // else
        // {
        //     $email = $this->session->userdata('recover_email');
        //     $new_password = password_hash($this->input->post('pssd2'),PASSWORD_DEFAULT);
        //     $updated = $this->user_model->UpdatePassword($email, $new_password);
        //     if($updated > 0)
		// 	{
		// 		$this->session->set_flashdata('success', 'Password changed successfully. You can login now');
		// 		redirect('w/login');
		// 	}
		// 	else
		// 	{
		// 		$this->session->set_flashdata('error', 'Error occured. Retry.');
		// 			redirect('w/createpassword');
		// 	}
        // }
	}

	public function getAllLGAs()
	{
		$state = $this->request->getPost('state');
		$lga = new LGAModel();
		$lgas = $lga->where('state_id ', $state)->findAll();

		//var_dump($lgas);
		
		$output = "";
		if(!empty($lgas))
		{
				$output .= '<option value="">Select one</option>';
				foreach($lgas as $lga)
				{
					$output .= '<option value="'.$lga['name'].'">'.$lga['name'].'</option>';  
				}

			return $output;

		}
		else
		{
				$output .= '<option value="">None found</option>';
				return $output;
							
		}

	}

	public function logout()
	{
		session()->destroy();
		return redirect()->to(base_url('/'));
	}


}
