<?php namespace App\Controllers;

use App\Models\ApplicantModel;
use App\Models\CompanyModel;
use App\Models\SchoolModel;
use App\Models\AssociationModel;
use App\Models\LGAModel;
use App\Models\ProfessionModel;
use App\Models\ApplicantVerificationEmailModel;
use App\Models\CompanyVerificationEmailModel;



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
		// $check_verification = new ApplicantVerificationEmailModel();
		// $check_details = $check_verification->where('fcookie', $_COOKIE["_krs"])->where('fverified', 0)->orderBy('frecno', 'desc')->first();
		
		// if(!empty($check_details))
		// {
		// 	session()->setFlashdata('show_resend_email',  $check_details['femail']);
		// 	return redirect()->to(base_url('/register/verify/'.$check_details['fdevice']));
		// }

		return view('front/index');
		
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

		///var_dump($this->request->getPost());
		if (! $this->validate([
			'user-email-or-phone-number' => [
				'rules' => 'required|trim|is_unique[tapplicants.fuser_name]',
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

		$token_exists = new ApplicantVerificationEmailModel();
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

			$model = new ApplicantModel();

			$data = [
				'fuser_name' => $this->request->getPost('user-email-or-phone-number'),
				'fpass_word'  => password_hash($this->request->getPost('password2'),PASSWORD_DEFAULT),
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
	
				
				if ($email->send()) 
				{
					$verify_email = new ApplicantVerificationEmailModel();

					$data = [
						'femail' => $this->request->getPost('user-email-or-phone-number'),
						'ftoken'  => $token_free,
						'fcookie' => $_COOKIE["_krs"],
						'fdevice' => 'email'
						];

					//var_dump($data);
					$verify_email->insert($data);

					session()->set('verify_email', $this->request->getPost('user-email-or-phone-number'));
					session()->setFlashdata('success', 'Verification code sent to your email successfully.');

					return redirect()->to(base_url('/register/verify/email'));
				} 
				else 
				{
					//echo $email->printDebugger();
					return false;
				}

				// session()->setFlashdata('success', 'Registration successful. Now you can login into your account.');
				// return redirect()->to(base_url('login'));
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

				$model = new ApplicantModel();

				$data = [
					'fuser_name' => $this->request->getPost('user-email-or-phone-number'),
					'fpass_word'  => password_hash($this->request->getPost('password2'),PASSWORD_DEFAULT),
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


					$verify_email = new ApplicantVerificationEmailModel();

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

		$token_exists = new ApplicantVerificationEmailModel();
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
			$verify_email = new ApplicantVerificationEmailModel();

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

		$token_exists = new ApplicantVerificationEmailModel();
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


		$verify_email = new ApplicantVerificationEmailModel();

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
    	    $verify = new ApplicantVerificationEmailModel();
			$token_details = $verify->where('ftoken', $code)->first();
	
			//var_dump($applicant_details);
    	    if(is_null($token_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! You entered an invalid verification code. You can click resend to get another one');
				return redirect()->to(base_url('/register/verify/email'));
    	    }
    	    else
    	    {
    	       //check credentials
				$applicant = new ApplicantModel();
				$applicant_details = $applicant->where('fuser_name', session()->verify_email)->first();

				$sessiondata = [
					'id' => $applicant_details['frecno'],
					'username' => $applicant_details['fuser_name'],
					'applicant_id' => $applicant_details['fapplicant_id'],
					'job_title' => $applicant_details['fprofession'],
				];

				session()->set($sessiondata);

				return redirect()->to(base_url('/applicant/experiences'));

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
    	    $verify = new ApplicantVerificationEmailModel();
			$token_details = $verify->where('ftoken', $code)->first();
	
			//var_dump($applicant_details);
    	    if(is_null($token_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! You entered an invalid verification code. You can click resend to get another one');
				return redirect()->to(base_url('/register/verify/phone'));
    	    }
    	    else
    	    {
    	       //check credentials
				$applicant = new ApplicantModel();
				$applicant_details = $applicant->where('fuser_name', session()->verify_phone)->first();

				$sessiondata = [
					'id' => $applicant_details['frecno'],
					'username' => $applicant_details['fuser_name'],
					'applicant_id' => $applicant_details['fapplicant_id'],
					'job_title' => $applicant_details['fprofession'],
				];

				session()->set($sessiondata);

				return redirect()->to(base_url('/applicant/experiences'));

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
		$applicant = new ApplicantModel();
		$applicant_details = $applicant->where('fuser_name', $user_name)->first();

		///var_dump($applicant_details);
		
		if(!empty($applicant_details))
		{
			//$applicant_details = (object)$applicant_details2;
			$validate = password_verify($pass_word, $applicant_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				//Check if profile setup has been done, if not redirect to profile setup
				if($applicant_details['factive'] == 0)
				{
					//set id session
					$sessiondata = [
						'id' => $applicant_details['frecno'],
						'username' => $applicant_details['fuser_name'],
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
						'id' => $applicant_details['frecno'],
						'username' => $applicant_details['fuser_name'],
						'applicant_id' => $applicant_details['fapplicant_id'],
						'job_title' => $applicant_details['fprofession'],
					];

					session()->set($sessiondata);

					return redirect()->to(base_url('/applicant/experiences'));
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

	public function cregister()
	{
		return view('front/cregister');
	}


	public function cregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
			'company_name' => 'required|trim',
			'company_email' => 'is_unique[tcompanies.fcompany_email]',
			'company_email' => [
				'rules' => 'required|trim|is_unique[tcompanies.fcompany_email]',
				'errors' => [
						'required' => 'Please input email. Email is required!',
						'is_unique' => 'Email already in use. Use a different one.',
				]
			],
			'company_pword' => [
				'label'  => 'Password',
				'rules'  => 'required|trim|min_length[8]',
				'errors' => [
					'min_length' => 'Your Password is too short. Use a minimum of 8 characters'
					]
				],
			'confrim_company_pword' => [
				'rules' => 'required|matches[company_pword]',
				'errors' => [
						'required' => 'Confirm Password is required!',
						'matches' => 'The Confirm Password field does not match the Password field.',
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


		$token2 = openssl_random_pseudo_bytes(16);
		$token = bin2hex($token2);

		$token_exists = new CompanyVerificationEmailModel();
		$token_details = $token_exists->where('ftoken', $token)->first();

		if(is_null($token_details))
		{
			$token_free = $token;
		}
		else
		{
			$token_free = $token .'x';
		}

		$company = new CompanyModel();
		$company_details = $company->where('fcompany_name', strtolower($this->request->getPost('company_name')))->first();

		//var_dump($company_details);

		if(empty($company_details))
		{

			$company_id = strtolower($this->request->getPost('company_name'));

		}
		else
		{

			// //Applicant ID already exists. Add 1 to it
			$id = $company_details['frecno'];
			$id2 = $id + 4;
			$company_id = $company_details['fcompany_name'] . '.' . $id2;
			//echo $company_details['fcompany_name'];

		}

		

		$model = new CompanyModel();

		$data = [
			'fcompany_name' => $this->request->getPost('company_name'),
			'fcompany_id' => $company_id,
			'fcompany_email' => $this->request->getPost('company_email'),
			'fpass_word'  => password_hash($this->request->getPost('confrim_company_pword'), PASSWORD_DEFAULT),
			'fphone' => $this->request->getPost('company_number'),
			'fcac' => $this->request->getPost('cac'),
			'fstate' => $this->request->getPost('state'),
			'flga' => $this->request->getPost('lga'),
			];

		//var_dump($data);
		$model->insert($data);
		$inserted = $model->affectedRows();
		if($inserted > 0)
		{
			// session()->setFlashdata('success', 'Registration successful. Now you can login into your account.');
			// return redirect()->to(base_url('/clogin'));

			$email = \Config\Services::email();
	
			$email->setFrom('noreply@kokoruns.com', 'Kokoruns');
			$email->setTo($this->request->getPost('company_email'));

			$email->setSubject('Kokoruns Company Registration Email Verification');
			$email->setMessage('<p style="text-align:center"><a style="background-color: #4CAF50;border: none;color: white;padding: 10px 28px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" href="https://kokoruns.com/verifyemail/'.$this->request->getPost('company_email').'/'.$token_free.'">Verify Email</a></p>');
			
			
			if ($email->send()) 
			{
				$cverify_email = new CompanyVerificationEmailModel();

				$data = [
					'femail' => $this->request->getPost('company_email'),
					'ftoken'  => $token_free,
					'fcookie' => $_COOKIE["_krs"],
					];

				//var_dump($data);
				$cverify_email->insert($data);

				session()->set('cverify_email', $this->request->getPost('company_email'));
				session()->setFlashdata('success', 'Verification link sent to your email successfully.');

				return redirect()->to(base_url('/'));
				
			} 
			else 
			{
				//echo $email->printDebugger();
				return false;
			}
		}
	}


	public function clogin()
	{
		return view('front/clogin');
	}

	public function cloginaction()
	{
		if (! $this->validate([

			'user-email-or-phone-number' => "required|trim",
			'password'  => 'required|trim',
    
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/clogin'));
		}

		 $user_name  = $this->request->getPost('user-email-or-phone-number');
		 $pass_word  = $this->request->getPost('password');

		//check credentials
		$company = new CompanyModel();
		$company_details = $company->where('fcompany_email', $user_name)->first();

		//var_dump($company_details);
		
		if(!empty($company_details))
		{
			//$applicant_details = (object)$applicant_details2;
			$validate = password_verify($pass_word, $company_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				$sessiondata = [

					'id' => $company_details['frecno'],
					'company_id' => $company_details['fcompany_id'],
					'company_name' => $company_details['fcompany_name'],
					'company_email' => $company_details['fcompany_email'],

				];

				session()->set($sessiondata);
				
				//var_dump($sessiondata);

				return redirect()->to(base_url('/company/dashboard'));
					
			}
			else
			{
				
				session()->setFlashdata('error', "Wrong Password");
				return redirect()->to(base_url('/clogin'));
			}

		}
		else
		{
			session()->setFlashdata('error', "Email does not exist in the system");
			return redirect()->to(base_url('/clogin'));
		}

		
	}

	

	public function sregister()
	{
		return view('front/sregister');
	}


	public function sregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
	
			'school_name' => 'required|trim',
			'school_email' => [
				'rules' => 'required|trim|is_unique[tschools.fschool_email]',
				'errors' => [
						'required' => 'Please input email. Email is required!',
						'is_unique' => 'Email already in use. Use a different one.',
				]
			],
			'school_pword' => [
				'label'  => 'Password',
				'rules'  => 'required|trim|min_length[8]',
				'errors' => [
					'min_length' => 'Your Password is too short. Use a minimum of 8 characters'
					]
				],
			'confrim_school_pword' => [
				'rules' => 'required|matches[school_pword]',
				'errors' => [
						'required' => 'Confirm Password is required!',
						'matches' => 'The Confirm Password field does not match the Password field.',
				]
			],
			'school_number' => "required|trim",
			'state' => "required|trim",
			'lga'  => 'required|trim',
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/sregister'));
		}


		$school = new SchoolModel();
		$school_details = $school->where('fschool_name', strtolower($this->request->getPost('school_name')))->first();

		//var_dump($company_details);

		if(empty($school_details))
		{

			$school_id = strtolower($this->request->getPost('school_name'));

		}
		else
		{

			// //Applicant ID already exists. Add 1 to it
			$id = $school_details['frecno'];
			$id2 = $id + 4;
			$school_id = $school_details['fschool_name'] . '.' . $id2;
			//echo $company_details['fcompany_name'];

		}

		$data = [

			'fschool_name' => $this->request->getPost('school_name'),
			'fschool_id' => $school_id,
			'fschool_email' => $this->request->getPost('school_email'),
			'fpass_word'  => password_hash($this->request->getPost('confrim_school_pword'), PASSWORD_DEFAULT),
			'fphone' => $this->request->getPost('school_number'),
			'fcac' => $this->request->getPost('cac'),
			'fstate' => $this->request->getPost('state'),
			'flga' => $this->request->getPost('lga'),
			];

		//var_dump($data);

		$model = new SchoolModel();
		$model->insert($data);
		$inserted = $model->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Registration successful. Now you can login into your account.');
			return redirect()->to(base_url('/slogin'));
		}
	}


	public function aregister()
	{
		return view('front/aregister');
	}

	public function aregisteraction()
	{

		//var_dump($this->request->getPost());
		if (! $this->validate([
		
			'association_name' => 'required|trim',
			'association_email' => [
				'rules' => 'required|trim|is_unique[tassociations.fassociation_email]',
				'errors' => [
						'required' => 'Please input email. Email is required!',
						'is_unique' => 'Email already in use. Use a different one.',
				]
			],
			'association_pword' => [
				'label'  => 'Password',
				'rules'  => 'required|trim|min_length[8]',
				'errors' => [
					'min_length' => 'Your Password is too short. Use a minimum of 8 characters'
					]
				],
			'confrim_association_pword' => [
				'rules' => 'required|matches[association_pword]',
				'errors' => [
						'required' => 'Confirm Password is required!',
						'matches' => 'The Confirm Password field does not match the Password field.',
				]
			],
			'association_number' => "required|trim",
			'state' => "required|trim",
			'lga'  => 'required|trim',
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/aregister'));
		}


		$association = new AssociationModel();
		$association_details = $association->where('fassociation_name', strtolower($this->request->getPost('association_name')))->first();

		//var_dump($company_details);

		if(empty($association_details))
		{

			$association_id = strtolower($this->request->getPost('association_name'));

		}
		else
		{

			// //Applicant ID already exists. Add 1 to it
			$id = $association_details['frecno'];
			$id2 = $id + 4;
			$association_id = $association_details['fassociation_name'] . '.' . $id2;
			//echo $company_details['fcompany_name'];

		}

		$data = [
			'fassociation_name' => $this->request->getPost('association_name'),
			'fassociation_id' => $association_id,
			'fassociation_email' => $this->request->getPost('association_email'),
			'fpass_word'  => password_hash($this->request->getPost('confrim_association_pword'), PASSWORD_DEFAULT),
			'fphone' => $this->request->getPost('association_number'),
			'fcac' => $this->request->getPost('cac'),
			'fstate' => $this->request->getPost('state'),
			'flga' => $this->request->getPost('lga'),
			];

		//var_dump($data);

		$model = new AssociationModel();
		$model->insert($data);
		$inserted = $model->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Registration successful. Now you can login into your account.');
			return redirect()->to(base_url('/alogin'));
		}
	}


	public function alogin()
	{
		return view('front/alogin');
	}

	public function aloginaction()
	{
		if (! $this->validate([

			'user-email-or-phone-number' => "required|trim",
			'password'  => 'required|trim',
    
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/employerlogin'));
		}

		 $user_name  = $this->request->getPost('user-email-or-phone-number');
		 $pass_word  = $this->request->getPost('password');

		//check credentials
		$association = new AssociationModel();
		$association_details = $association->where('fassociation_email', $user_name)->first();

		//var_dump($school_details);
		
		if(!empty($association_details))
		{
			//$applicant_details = (object)$applicant_details2;
			$validate = password_verify($pass_word, $association_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				$sessiondata = [

					'id' => $association_details['frecno'],
					'association_id' => $association_details['fassociation_id'],
					'association_name' => $association_details['fassociation_name'],
					'association_email' => $association_details['fassociation_email'],

				];

				session()->set($sessiondata);

				return redirect()->to(base_url('/association/dashboard'));
					
			}
			else
			{
				
				session()->setFlashdata('error', "Wrong Password");
				return redirect()->to(base_url('/alogin'));
			}

			}
		else
		{
			session()->setFlashdata('error', "Email does not exist in the system");
			return redirect()->to(base_url('/alogin'));
		}

	}


	public function slogin()
	{
		return view('front/slogin');
	}

	public function sloginaction()
	{
		if (! $this->validate([

			'user-email-or-phone-number' => "required|trim",
			'password'  => 'required|trim',
    
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/employerlogin'));
		}

		 $user_name  = $this->request->getPost('user-email-or-phone-number');
		 $pass_word  = $this->request->getPost('password');

		//check credentials
		$school = new SchoolModel();
		$school_details = $school->where('fschool_email', $user_name)->first();

		//var_dump($school_details);
		
		if(!empty($school_details))
		{
			//$applicant_details = (object)$applicant_details2;
			$validate = password_verify($pass_word, $school_details['fpass_word']);
			//var_dump($validate);
			if($validate)
			{
				$sessiondata = [

					'id' => $school_details['frecno'],
					'school_id' => $school_details['fschool_id'],
					'school_name' => $school_details['fschool_name'],
					'school_email' => $school_details['fschool_email'],

				];

				session()->set($sessiondata);

				return redirect()->to(base_url('/school/dashboard'));
					
			}
			else
			{
				
				session()->setFlashdata('error', "Wrong Password");
				return redirect()->to(base_url('/slogin'));
			}

			}
		else
		{
			session()->setFlashdata('error', "Email does not exist in the system");
			return redirect()->to(base_url('/slogin'));
		}

	}


	public function CompanyVerifyEmail()
	{
	//    $check = $this->uri->segment(3);
	//    $token = $this->uri->segment(4);
	   //cho $uri->getSegment(2);
	   echo $request->uri->getSegment(2);
	   //$this->request->uri->getSegment(2); 
	//    $row = $this->User_model->FetchSellerDetails($check, $token);
	// 	//var_dump($row);
	//    if(!empty($row))
	//    {
	// 		// $sess_array = array(
	// 		// 	'shop_name' => $row->fshop_name,
	// 		// 	'shop_id' => $row->fshop_id,
	// 		// 	'merchant_email' =>   $row->femail,
	// 		// 	'merchant_city' =>   $row->fcity_name,
	// 		// );
	// 		// $this->session->set_userdata($sess_array);

	// 		$updated = $this->User_model->ActivateSeller($row->femail);
	// 		if($updated > 0)
	// 		{
	// 			$this->session->set_flashdata('success', 'Your email is verified now. You can login.'); 
	// 			redirect('index.php/w/login');
	// 		}
	// 		else
	// 		{
	// 			$this->session->set_flashdata('error', 'Wrong or invalid reset token');
	// 			redirect('index.php/w/forgotpasword');
	// 		}
			
	//    }
	//    else
	//    {
	// 		$this->session->set_flashdata('error', 'Wrong or invalid reset token');
	// 		redirect('index.php/w/forgotpasword');
	//    }
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

		$applicant = new ApplicantModel();
		$applicant_details = $applicant->where('fapplicant_id', strtolower($this->request->getPost('first_name'). '.' . $this->request->getPost('last_name')))->first();
		if(empty($applicant_details))
		{
			$applicant_id = strtolower($this->request->getPost('first_name'). '.' . $this->request->getPost('last_name'));

		}
		else
		{
			//Applicant ID already exists. Add 1 to it
			$id2 = $id + 3;
			$applicant_id = $applicant_details['fapplicant_id']. '.' . $id2;
			
		}

		$address = $this->request->getPost('house_no') . ',' . ' ' .$this->request->getPost('user_street/estate');
		
		$data = [

			'ffirst_name'  => $this->request->getPost('first_name'),
			'flast_name'  => $this->request->getPost('last_name') ,
			'fapplicant_id'  => $applicant_id,
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

		$applicantmodel = new ApplicantModel();
		$updated = $applicantmodel->update($id, $data);
		if($updated > 0)
		{
			
			$sessiondata = [
				'applicant_id' => $applicant_id,
			];

			session()->set($sessiondata);
			
			return redirect()->to(base_url('applicant/experiences'));
		}
		else
		{
			session()->setFlashdata('error', 'Profile not updated. Try again');
			return redirect()->to(base_url('/login'));
		}

	}


	public function profile($username)
	{
		// echo $name;
		$applicant = new ApplicantModel();
		$applicant_details = $applicant->where('fuser_name', $username)->first();

		if(empty($applicant_details))
		{
			//session()->setFlashdata('error', "Page not found");
			//return view('front/404');
		}
		else
		{
			//echo "profile page";
			return view('front/profile');	
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
    	    $applicant = new ApplicantModel();
			$applicant_details = $applicant->where('fuser_name', $email)->first();
	
			//var_dump($applicant_details);
    	    if(is_null($applicant_details))
    	    {
				
				session()->setFlashdata('error', 'Sorry! Username not registered in the system.');
				return redirect()->to(base_url('/forgot'));
    	    }
    	    else
    	    {
    	        $token2 = openssl_random_pseudo_bytes(16);
                $token = bin2hex($token2);
                $email = $applicant_details['fuser_name'];
				 
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
	//    $row = $this->applicant_model->VerifyToken($email, $token);
	//    //var_dump($row);
	//    if(!empty($row))
	//    {
	// 	  $this->session->set_applicantdata('create_password', 'ok');
	//       $this->session->set_applicantdata('recover_email', $email);
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
	    // if (!$this->session->applicantdata('create_password'))
		// {
		//     $this->session->set_flashdata('error', 'Wrong or invalid token.');  
		// 	redirect('w/ForgotPassword'); // the applicant is not logged in, redirect them!
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
        //     $email = $this->session->applicantdata('recover_email');
        //     $new_password = password_hash($this->input->post('pssd2'),PASSWORD_DEFAULT);
        //     $updated = $this->applicant_model->UpdatePassword($email, $new_password);
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
