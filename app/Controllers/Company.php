<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\ApplicantExperienceModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantPortfolioModel;
use App\Models\ApplicantDocumentModel;
use App\Models\ApplicantWebLinkModel;
use App\Models\ApplicantProSkillModel;
use App\Models\ApplicantOtherSkillModel;
use App\Models\TeamModel;
use App\Models\JobModel;
use App\Models\JobInviteModel;
use App\Models\JobInviteMessageModel;
use App\Models\JobFinalChoiceModel;
use App\Models\EventModel;
use App\Models\TeamMessageModel;
use App\Models\TeamMemberModel;
use App\Models\RecommendationRequestModel;
use App\Models\RecommendationOfferModel;
use App\Models\MessageModel;
use App\Models\CompanyGalleryModel;
use App\Models\CompanyBranchModel;
use App\Models\CompanyAdminModel;
use App\Models\JobMatchesModel;
use App\Libraries\Zebra_Image;

class Company extends BaseController
{
	function __construct() 
	{

		if(!session()->id)
        {
            return redirect()->to(base_url('/clogin'));
		} 
	
    }


    // public function dashboard($id = null)
	public function dashboard($id = null)
    {
		

		if(!empty($id))
		{
			session()->set('company_id', $id);
			$company_id = session()->company_id;
		}
		else
		{
			$company_id = session()->company_id;
		}

		if(!session()->company_id)
        {
			session()->setFlashdata('error', "You do not have permission to view page.");
            return redirect()->to(base_url('/user/dashboard'));
		} 

		
        $company = new CompanyModel();
        $data['company_details'] = $company->where('fcompany_id', $company_id)->first();


		$company_gallery = new CompanyGalleryModel();
		$data['company_gallery'] = $company_gallery->where('fcompany_id', $company_id)->orderBy('frecno', 'desc')->findAll();


		$company_branch = new CompanyBranchModel();
		$data['company_branches'] = $company_branch->where('fcompany_id', $company_id)->orderBy('fbranch_name', 'asc')->findAll();

		$company_admin = new CompanyAdminModel();
		$data['company_admins'] = $company_admin->where('fcompany_id', $company_id)->orderBy('fsubadmin_name', 'asc')->findAll();

		$message = new MessageModel();
		$data['broadcasts'] = $message->where('fsender_id', $company_id)->where('fis_broadcast', 1)->findAll();
	   //var_dump($data['company_admins']);
	   
		return view('company/dashboard', $data);
	
	}


	public function updateprofileaction()
	{
		

		$locations = array_filter($this->request->getPost('locations'));
		$extracted_locations = implode(" -", $locations);


		$socials = array_filter($this->request->getPost('socials'));
		$extracted_socials = implode(" -", $socials);


		$data = [

            'fabout' => $this->request->getPost('about'),
            'flocations' => $extracted_locations,
            'fsocials' => $extracted_socials,
    
            ];
            
		
		//var_dump($data);

		$id = session()->id;

		$company = new CompanyModel();
		$updated = $company->update($id, $data);
		
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "School Profile updated successfully",
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "School Profile not updated",
			];
		}


		return $this->response->setJSON($response);
		
	}




	public function loadcompanyprofile()
	{
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 

		$company_id = session()->company_id;

        $company = new CompanyModel();
		$company_details = $company->where('fcompany_id', $company_id)->first();

		$output = "";
		
		if(empty($company_details))
		{
			$output .= '
				
						
					';
		}
		else
		{

			$url = site_url(); 

			$locations = explode("-", $company_details['flocations']);
			$socials = explode(",", $company_details['fsocials']);

			$output .= '
						<div class="about-div">
						<div class="about-label"><img src="" class="">About</div> 
						<div class="about">
							'.$company_details['fabout'].'
						</div>
						<hr>    
						</div>
					
						
						
						<div class="locations-div">
						<div class="locations-label"><img src="" class="">Locations</div> 
						
					';
		
			foreach($locations as $location)
			{


				$output .= '<div class="locations">'.$location.'</div>';

			}

			$output .= '<hr>    
            </div>
            <div class="social-div">
			<div class="social-label">Socials</div>
			';


			foreach($socials as $social)
			{


				$output .= '
                <a href="#"><div class="social-div-a">
                <div class="social">
                <img src="'.$url.'public/employerassets/Images/Company%20%20Profile/Twitter-Logo.png" class="social-icon">Twitter</div></div></a>';

			}

			$output .= '<hr>
                
			</div>
			';


		}

		$data = array(
			'company_profile' => $output,
		);	

		return $this->response->setJSON($data);
	}


	public function updateaboutaction()
	{
		
		$data = [

			'fabout' => $this->request->getPost('about'),
			'fphone' => $this->request->getPost('phone'),
			'fcac' => $this->request->getPost('cac'),
			'fcompany_director' => $this->request->getPost('company_director'),
			'fwebsite' => $this->request->getPost('website'),
			'fcompany_address' => $this->request->getPost('company_address'),
			'fmain_office_location_state' => $this->request->getPost('about_state'),
			'fmain_office_location_lga' => $this->request->getPost('about_lga'),
		
            ];
            
		
		//var_dump($data);

		$id = $this->request->getPost('id');

		$company = new CompanyModel();
		$updated = $company->update($id, $data);
		
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Company Profile updated successfully",
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Company Profile not updated",
			];
		}


		return $this->response->setJSON($response);
	}

	///createbranchaction
	

	public function createbranchaction()
	{
		 //var_dump($this->request->getPost());
		 if (! $this->validate([
			'branch_name' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Branch name is required!',
							]
						],
			'branch_manager' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Branch Manager is required!',
				]
			],
			'branch_address' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Branch Address is required!',
				]
			],
			'branch_phone' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Branch Phone is required!',
				]
			],
			'branch_email' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Branch Email is required!',
				]
			],

		
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Branch not added"
			];
		}
		
		$company_id = session()->company_id;

        $company = new CompanyModel();
		$company_details = $company->where('fcompany_id', $company_id)->first();


        $data = [
			'fcompany_id' => $company_details['fcompany_id'],
            'fbranch_id' => 'BR' . time(),
			'fbranch_name' => $this->request->getPost('branch_name'),
			'fbranch_manager' => $this->request->getPost('branch_manager'),
			'fbranch_address' => $this->request->getPost('branch_address'),
			'fbranch_phone' => $this->request->getPost('branch_phone'),
			'fbranch_state' => $this->request->getPost('branch_state'),
            'fbranch_lga' => $this->request->getPost('branch_lga'),
            ];
            
        //var_dump($start_time);

        $branch = new CompanyBranchModel();
		$branch->insert($data);
		$inserted = $branch->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Branch created successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Branch not created"
			];	
			
		}

		return $this->response->setJSON($response);
	}


	public function updatebranchaction()
	{
		//editbranchaction

		$id = $this->request->getPost('id');

		$data = [
	
			'fbranch_name' => $this->request->getPost('branch_name'),
			'fbranch_manager' => $this->request->getPost('branch_manager'),
			'fbranch_address' => $this->request->getPost('branch_address'),
			'fbranch_phone' => $this->request->getPost('branch_phone'),
			'fbranch_email' => $this->request->getPost('branch_email'),
            
			];



			$branchm = new CompanyBranchModel();
			$updated = $branchm->update($id, $data);
		
			if($updated > 0)
			{
				$response = [
					'success' => true,
					'data' => 'saved',
					'msg' => "company Branch deleted successfully",
				];
			}
			else
			{
				$response = [
					'success' => false,
					'data' => 'failed',
					'msg' => "company branch not deleted",
				];
			}

			return $this->response->setJSON($response);

	}


	public function deletebranchaction()
    {
		$id = $this->request->getPost('recno');
        $as = new CompanyBranchModel();
        $as->where('frecno', $id)->delete();
        $deleted = $as->affectedRows();
		if($deleted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "company branch deleted successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "company branch not deleted"
			];
		}

		return $this->response->setJSON($response);
	}




	public function updatesocialsaction()
	{
		//editbranchaction

		$id = $this->request->getPost('id');

		$data = [
	
			'flinkedin' => $this->request->getPost('linkedin'),
			'ffacebook' => $this->request->getPost('facebook'),
			'finstagram' => $this->request->getPost('instagram'),
            
			];



			$am = new CompanyModel();
			$updated = $am->update($id, $data);
		
			if($updated > 0)
			{
				$response = [
					'success' => true,
					'data' => 'saved',
					'msg' => "Socials updated successfully",
				];
			}
			else
			{
				$response = [
					'success' => false,
					'data' => 'failed',
					'msg' => "company branch not deleted",
				];
			}

			return $this->response->setJSON($response);

	}

	

	public function updateinfoaction()
	{
		//editbranchaction

		$id = $this->request->getPost('recno');
		//$id = 22;

		$data = [
	
			'ffounded' => $this->request->getPost('founded'),
			'ffield' => $this->request->getPost('field'),
            
			];



			$assoc = new CompanyModel();
			$updated = $assoc->update($id, $data);
		
			if($updated > 0)
			{
				$response = [
					'success' => true,
					'data' => 'saved',
					'msg' => "company Profile updated successfully",
				];
			}
			else
			{
				$response = [
					'success' => false,
					'data' => 'failed',
					'msg' => "company Profile not updated",
				];
			}

			return $this->response->setJSON($response);

	}
	
	


	
	public function createeventaction()
	{
		 //var_dump($this->request->getPost());
		 if (! $this->validate([
			'event_start' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Event Start is required!',
							]
						],
			'event_end' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event End is required!',
				]
			],
			'event_title' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event Title is required!',
				]
			],
			'event_address' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event Title is required!',
				]
			],
			'event_description' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event Description is required!',
				]
			],

		
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Event not added"
			];
		}


		
		if($this->request->getPost('event_price') == 'free')
		{
			$price1 = 0;
			$price2 = null;
		}
		else if(!empty($this->request->getPost('event_price_from2')))
		{
			$price1 = $this->request->getPost('event_price_from2');
			$price2 = null;
		}
		else if(!empty($this->request->getPost('event_price_from3')) && !empty($this->request->getPost('event_price_to')))
		{
			$price1 = $this->request->getPost('event_price_from3');
			$price2 = $this->request->getPost('event_price_to');

		}



			if(!empty($this->request->getFile('event_image')))
			{
				$file1 = $this->request->getFile('event_image');
				$filename1 = $file1->getRandomName();
			}
			else
			{
				$filename1 = 'event.jpg';
			}

			if(!empty($this->request->getFile('event_logo')))
			{
				$logo = $this->request->getFile('event_logo');
				$logoname = $logo->getRandomName();
			}
			else
			{
				$logoname = 'logo.jpg';
			}


			$start_DT = date('Y-m-d H:i:s', strtotime($this->request->getPost('event_start')));
			$end_DT = date('Y-m-d H:i:s', strtotime($this->request->getPost('event_end')));

        	$data = [
			
			'fevent_id' => 'EV' . time(),
			'ffrom' => $start_DT,
			'fto' => $end_DT,
			'ftitle' => $this->request->getPost('event_title'),
			'fevent_link' => $this->request->getPost('event_link'),
			'fauthor' => $this->request->getPost('author'),
			'fdescription' => $this->request->getPost('event_description'),
			'fevent_type' => $this->request->getPost('event_type'),
			'fevent_industry' => $this->request->getPost('event_industry'),
			'fevent_price1' => $price1,
			'fevent_price2' => $price2,
			'fevent_address' => $this->request->getPost('event_address'),
			'fevent_state' => $this->request->getPost('event_state'), 
			'fevent_lga' => $this->request->getPost('event_lga'),
			'fevent_image1' => $filename1,
			'fevent_logo' => $logoname,

            ];
            
        // //var_dump($start_time);

        $event = new EventModel();
		$event->insert($data);
		$inserted = $event->affectedRows();
		if($inserted > 0)
		{

			if(!empty($this->request->getFile('event_image')))
			{
				// $file1 = $this->request->getFile('event_image');
				// $filename1 = $file1->getRandomName();
				$PATH = getcwd();
				$file1->move($PATH .'/public/eventimages', $filename1);
			}

			if(!empty($this->request->getFile('event_logo')))
			{
				// $logo = $this->request->getFile('event_logo');
				// $logoname = $logo->getRandomName();
				$PATH = getcwd();
				$logo->move($PATH .'/public/eventimages/logos', $logoname);
			}
		
		
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Event created successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Event not created"
			];	
			
		}

		return $this->response->setJSON($response);
	}


	
	public function editeventaction()
    {
        
		 //var_dump($this->request->getPost());
		//  if (! $this->validate([
		// 	// 'ffrom' => [
		// 	// 			'rules'  => 'required|trim',
		// 	// 			'errors' => [
		// 	// 				'required' => 'Event Start is required!',
		// 	// 				]
		// 	// 			],
		// 	// 'fto' => [
		// 	// 	'rules' => 'required|trim',
		// 	// 	'errors' => [
		// 	// 			'required' => 'Event End is required!',
		// 	// 	]
		// 	// ],
		// 	'ftitle' => [
		// 		'rules' => 'required|trim',
		// 		'errors' => [
		// 				'required' => 'Event Title is required!',
		// 		]
		// 	],
		// 	'fdescription' => [
		// 		'rules' => 'required|trim',
		// 		'errors' => [
		// 				'required' => 'Event Description is required!',
		// 		]
		// 	],

		
		// ]))
		// {
		// 	$response = [
		// 		'success' => false,
		// 		'data' => 'failed',
		// 		'msg' => "Event not added"
		// 	];
		// }
		
		// // $invitees = $this->request->getPost('invitees');
		// // $extracted_invitees = implode(",", $invitees);

        $id = $this->request->getPost('id');

		$start_DT = date('Y-m-d H:i:s', strtotime($this->request->getPost('event_start')));
		$end_DT = date('Y-m-d H:i:s', strtotime($this->request->getPost('event_end')));

		$data = [
	
		'ffrom' => $start_DT,
		'fto' => $end_DT,
		'ftitle' => $this->request->getPost('event_title'),
		'fevent_link' => $this->request->getPost('event_link'),
		'fdescription' => $this->request->getPost('event_description'),
		'fevent_price1' => $this->request->getPost('event_price1'),
		'fevent_price2' => $this->request->getPost('event_price2'),
		'fevent_address' => $this->request->getPost('event_address'),
		'fevent_state' => $this->request->getPost('event_state'), 
		'fevent_lga' => $this->request->getPost('event_lga'),
		// 'fevent_image1' => $filename1,
		// 'fevent_image2' => $filename2,
		// 'fevent_image3' => $filename3,
		// 'fevent_image4' => $filename4,
		// 'fevent_image5' => $filename5,
		// 'fevent_image6' => $filename6,
		];
            
		
		// //var_dump($data);
	

        $event = new EventModel();
        $updated = $event->update($id, $data);
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Event updated successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Event not updated"
			];
		}

		return $this->response->setJSON($response);

	}


	public function deleteeventaction()
    {
		$id = $this->request->getPost('event_id');
        $event = new EventModel();
        $event->where('frecno', $id)->delete();
        $deleted = $event->affectedRows();
		if($deleted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Event deleted successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Event not deleted"
			];
		}

		return $this->response->setJSON($response);
	}

	

	public function loadcompanyevents()
	{

		$company_id = session()->company_id;

		if(!empty($this->request->getPost('start_date')))
		{
			$start_date = date('Y-m-d', strtotime($this->request->getPost('start_date')));
		}

		if(!empty($this->request->getPost('start_date')))
		{
			$end_date = date('Y-m-d', strtotime($this->request->getPost('end_date')));
		}
		
		$event_type = $this->request->getPost('event_type');
		$event_industry = $this->request->getPost('event_industry');
		

		if($this->request->getPost('event_price') == 'Free')
		{
			$event_price = $this->request->getPost('event_price');
		}


		$query ="SELECT * FROM `tevents` WHERE `fauthor` = '".$company_id."'";


		if(!empty($start_date))
		{
			$query .=" AND ffrom ='".$start_date."'";
		}

		if(!empty($end_date))
		{
			$query .=" AND fto ='".$end_date."'";
		}

		if(!empty($event_type))
		{
			$query .=" AND fevent_type ='".$event_type."'";
		}

		if(!empty($this->request->getPost('event_price') && $this->request->getPost('event_price') == 'Free'))
		{
			$query .=" AND fevent_price1 ='0.00'";
		}

		if(!empty($this->request->getPost('event_price') && $this->request->getPost('event_price') == 'Paid'))
		{
			$query .=" AND fevent_price1 !='0.00'";
		}

		$query_addendum =" ORDER BY frecno DESC;";


        // $company = new EventModel();
		// $company_events = $company->where('fauthor', $company_id)->orderBy('frecno', 'desc')->findAll();

		$db = \Config\Database::connect();

		$query1 =  $db->query($query.$query_addendum);

		$company_events = $query1->getResult('array');

		// $output = "";
		
		if(empty($company_events))
		{
			$output .='
				
                  
						<div class="posted-event-container">
							<p style="text-align:center">Seems you have no events yet. Add a new event to get started.</p>
						</div>
			  
						
				';
		}
		else
		{
			$url = site_url();

			foreach($company_events as $event)
			{
				if($event['fevent_price1'] == 0.00)
				{
					$price_tag1 = "Free";

					$price_tag2 = "";
				}
				else if(!empty($event['fevent_price1']) && empty($event['fevent_price2']))
				{
					$price_tag1 = '₦'. number_format($event['fevent_price1']);

					$price_tag2 = "";
				}
				else if(!empty($event['fevent_price1']) && ($event['fevent_price2'] == 0.00))
				{
					$price_tag1 = '₦'. number_format($event['fevent_price1']);

					$price_tag2 = "";
				}
				else if(!empty($event['fevent_price2']))
				{
					$price_tag1 = '₦'. number_format($event['fevent_price1']);

					$price_tag2 = '- ₦'. number_format($event['fevent_price2']);
				}


				$output .= '
					<div class="card mb-5">
					
							<img class="card-img-top" src="'.$url.'public/eventimages/'.$event['fevent_image1'].'" alt="Card image" width="100%" height="300rem">
					
						
					<div class="card-body pb-0">
						<div class="row">
							<div class="col-lg-3 text-right">
								<img src="'.$url.'public/eventimages/logos/'.$event['fevent_logo'].'" alt="" width="50%">
							</div>
							<div class="col-lg-9">
								<h4 class="card-title">'.ucwords(strtolower($event['ftitle'])).'</h4>
								<p><i class="far fa-calendar-alt"></i> &nbsp;'.date('M j, Y', strtotime($event['ffrom'])).' - '.date('M j, Y', strtotime($event['fto'])).'</p>
								<p><i class="fas fa-map-marker-alt"></i> &nbsp;'.$event['fevent_address'].' | '.$event['fevent_lga'].', '.$event['fevent_state'].'</p>
								<p><i class="fas fa-money-bill"></i> &nbsp;'.$price_tag1.' '.$price_tag2.'</p>
								<p>'.$event['fdescription'].'</p>

								<div class="row">
									<div class="col">
										<p class="text-primary">'.$event['fevent_link'].'</p>
									</div>
									<div class="col">
									<p class="text-right">
											<i class="far fa-edit cursor fa-lg text-warning edit-event-button" data-toggle="tooltip" title="edit" data-edit_id="'.$event['frecno'].'" data-event_title="'.$event['ftitle'].'" data-event_link="'.$event['fevent_link'].'"  data-event_start="'.date("H:i d-m-Y", strtotime($event['ffrom'])).'" data-event_end="'.date("H:i d-m-Y", strtotime($event['fto'])).'" data-event_description="'.$event['fdescription'].'" data-event_address="'.$event['fevent_address'].'" data-event_price1="'.$event['fevent_price1'].'" data-event_price2="'.$event['fevent_price2'].'"></i> &nbsp;&nbsp;
											<i class="far fa-trash-alt cursor fa-lg text-danger delete-event-button" data-toggle="tooltip" title="delete" data-delete_id="'.$event['frecno'].'"></i></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			
				';
			}

		}


		

		
		$data = array(
			'company_events' => $output,
		);	

		return $this->response->setJSON($data);

	}


	public function ebroadcast()
	{
		$applicant_id = session()->company_id;


		 $applicant = new UserModel();
	
		//

		$applicant_ids = ['godson.ihemere', 'gbemileke.daniel', 'bunto.ronny', 'demilade.oyeyele'];


		$receivers = array();
		foreach ($applicant_ids as $id) 
		{
			$receivers[] = $applicant->where('fuser_id', $id)->select('ffirst_name', 'flast_name')->first();
		}
	
		$message_id = 'mg' . time();

		for($i=0; $i<count($applicant_ids); $i++)
		{
			$data[]=array(
				'fsender_id' => $this->request->getPost('sender'),
				'fsender_name' => $this->request->getPost('sender_name')[$i],
				'fsubject'=> $this->request->getPost('subject')[$i],
				'fcontent'=> $this->request->getPost('message')[$i],
				'freceiver_id'=>$applicant_ids[$i],
				'freceiver_name'=> $receivers[$i],
				'fmessage_id' => $message_id,
				'fis_broadcast' => 1,
			);
		}


		   
		//   var_dump($data);
            
		
			$message = new MessageModel();

			$message->insertBatch($data);


	
		$inserted = $message->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Broadcast sent successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Broadcast not sent"
			];
		}	


		return $this->response->setJSON($response);

	}



	// public function loadbroadcasts()
	// {
	
	// 	$company_id = session()->company_id;

    
	// 	$message = new MessageModel();
	// 	$broadcasts = $message->where('fsender_id', $company_id)->where('fis_broadcast', 1)->findAll();

	// 	$output = "";
		
	// 	if(empty($broadcasts))
	// 	{
	// 		$output .= '
				
						
	// 				';
	// 	}
	// 	else
	// 	{

	// 		$output .='<table class="table table-bordered table-sm datatable">
	// 		<thead>
	// 		<tr>
	// 			<th>Subject</th>
	// 			<th>Content</th>
	// 			<th>Date Created</th>
	// 		</tr>
	// 		</thead>
	// 		<tbody >';

	// 		foreach($broadcasts as $broadcast)
	// 		{

	// 			$output .= '

							
	// 					<tr>
	// 						<td>'.$broadcast['fsubject'].'</td>
	// 						<td>'.$broadcast['fcontent'].'</td>
	// 						<td>'.$broadcast['created_at'].'</td>
	// 					</tr>'
						

	// 					;
	// 		 }

	// 		 $output .= 
	// 		 		'</tbody>
	// 		 		</table>';

	// 	}

	// 	$data = array(
	// 		'broadcasts' => $output,
	// 	);	

	// 	return $this->response->setJSON($data);
	// }




	public function uploadgalleryaction()
	{
		if (! $this->validate([
			'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[file,4096]',
            ],
	
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Image not uploaded"
			];
        }
        
        $filh = $this->request->getFile('file');
		$ndfgf = $filh->getRandomName();
		$PATH = getcwd();
		//$filh->move($PATH .'/public/companygalleries', $ndfgf);

		$full_path = $filh->getTempName();

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
		$image->target_path = 'public/companygalleries/600/'. $ndfgf;

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
		if (!$image->resize(400, 245, ZEBRA_IMAGE_CROP_CENTER)) {

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


		 $data = [

			'fimage' => $ndfgf,
			'fimage_id' => 'IMG'. time(),
			'fimage_caption' => $this->request->getPost('image_caption'),
			'fcompany_id' => $this->request->getPost('company_id'),

		];
		
		$gallery = new CompanyGalleryModel();
        
		$gallery->insert($data);
	    $inserted = $gallery->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Image uploaded successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Image not uploaded"
			];
		}	

        //var_dump($data);

		
	    return $this->response->setJSON($response);

	}


	public function deletegalleryaction()
	{	

		$image_id = $this->request->getPost('image_id');
		$image_name = $this->request->getPost('image_name');
		
		$gallery = new CompanyGalleryModel();
		$gallery->where('fimage_id', $image_id)->delete();
		$deleted = $gallery->affectedRows();
		if($deleted > 0)
		{
			$PATH = getcwd();
			//unlink($PATH .'/public/companygalleries/'.$image_name);
			$response = [
				'success' => true,
				'data' => 'done',
				'msg' => "Image deleted"
			];
		}

		return $this->response->setJSON($response);

	}


	public function jobdash()
	{

		if(!session()->id)
        {
            return redirect()->to(base_url('/slogin'));
		} 
		
		$company_id = session()->company_id;
		
        $company = new CompanyModel();
		$data['company_details'] = $company->where('fcompany_id', $company_id)->first();

		$job = new JobModel();
		$data['jobs'] = $job->where('femployer_id', $company_id)->orderBy('frecno', 'DESC')->findAll();



		//var_dump($data['jobs']);
		
		return view('company/jobdash', $data);

	}

	public function createjobaction()
	{
		
		 if (! $this->validate([
			'job_title' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Job Title is required!',
							]
						],
			'employment_type' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Employment Type is required!',
				]
			],
			'workdays' => [
				'rules' => 'required',
				'errors' => [
						'required' => 'Work days is required!',
				]
			],
			'work_time_start' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Work Time Start is required!',
				]
			],
			'work_time_end' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Work Time End is required!',
				]
			],
			'job_description' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Job Description is required!',
				]
			],
			
		
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			redirect('company/jobdash');
		}
		
		
		$workdays = array_filter($this->request->getPost('workdays'));
		$extracted_workdays = implode(",", $workdays);

		$job_title = $this->request->getPost('job_title');
		$employment_type = $this->request->getPost('employment_type');
		$location_state = $this->request->getPost('state');
		$location_lga = $this->request->getPost('lga');
		$educational_qualification = $this->request->getPost('educational_qualification');

		$job_id = 'JB' . time();

        $data = [

            'fjob_id' => $job_id,
			'fjob_title' => $job_title,
			'femployment_type' => $employment_type,
			'flocation_state' => $location_state,
			'flocation_lga' => $location_lga,
			'fworkdays' => $extracted_workdays,
			'fwork_time_start' => $this->request->getPost('work_time_start'),
			'fwork_time_end' => $this->request->getPost('work_time_end'),
			'feducational_qualification' => $educational_qualification,
			'fjob_description' => $this->request->getPost('job_description'),
			'femployer_id' => $this->request->getPost('company_id'),
			'femployer_name' => $this->request->getPost('company_name'),
	
            ];
            
        //var_dump($data);

        $job = new JobModel();
		$job->insert($data);
		$inserted = $job->affectedRows();
		$inserted_id = $job->insertID();
		if($inserted > 0)
		{

			$db = \Config\Database::connect();

		
			$sql ="SELECT * FROM `tusers` WHERE factive = 1";

         
			if(!empty($job_title))
			{
			
				// $sql .=" AND  fprofession ='".$job_title."' OR fpreferred_job1 ='".$job_title."' ";
				$sql .=" AND  fprofession ='".$job_title."' OR fpreferred_job1 ='".$job_title."' OR fpreferred_job2 ='".$job_title."' OR fpreferred_job3 ='".$job_title."'OR fpreferred_job4 ='".$job_title."' OR fother_professions1 ='".$job_title."' OR 	fother_professions2 ='".$job_title."' OR fother_professions3 ='".$job_title."' OR fother_professions4 ='".$job_title."' ";
			}

			if(!empty($employment_type))
			{
			
				$sql .=" AND femployment_type ='".$employment_type."'";
			}

			if(!empty($location_state))
			{
			
				$sql .=" AND fstate ='".$location_state."'";
			}
			
			if(!empty($location_lga))
			{
			
				$sql .=" AND flga ='".$location_lga."'";
			}

			if(!empty($educational_qualification))
			{
			
				$sql .=" AND feducational_qualification ='".$educational_qualification."'";
			}
         

         	$query_addendum =" ORDER BY ffirst_name ASC;";
        
			$query = $db->query($sql.$query_addendum);
			$results = $query->getResult('array');

			//var_dump($data['results']);

			$company_id = session()->company_id;
		
			$company = new CompanyModel();
			$company_details = $company->where('fcompany_id', $company_id)->first();

		
			for($i=0; $i < count($results); $i++)
			{
				$data2[] =
				[
					'fjob_id' => $job_id,
					'fjob_title'=> $job_title,
					'femployer_id'=> $company_details['fcompany_id'],
					'femployer_name'=> $company_details['fcompany_name'],
					'fuser_id'=> $results[$i]['fuser_id'],
					'fuser_names'=> $results[$i]['ffirst_name'] . ' '.$results[$i]['flast_name'],
				];
			}

			$jmm = new JobMatchesModel();

			$jmm->insertBatch($data2);
			echo $inserted = $jmm->affectedRows();

			//var_dump((array)$data2);


			if($inserted > 0)
			{
				session()->setFlashdata('success', 'Job created successfully');
				return redirect()->to(base_url('company/jobdash'));
			}
			else
			{
				session()->setFlashdata('error', 'Error! Job not created. Try again.');
				return redirect()->to(base_url('company/jobdash'));
			}

		}
		else
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			redirect('company/jobdash');
		}

	}


	public function getjobmatches()
	{

		//$job_id = $this->request->getPost('job_id');

		$job_id = $_POST["job_id"];

		$jmm2 = new JobMatchesModel();
		$job_matches = $jmm2->where('fjob_id', $job_id)->orderBy('frecno', 'DESC')->findAll();
		// //var_dump($job_matches);

			$output = "";
		
		if(empty($job_matches))
		{
			$output .= '
				
						<p>No matches </p>
						
					';
		}
		else
		{
			$url = site_url();

			foreach($job_matches as $job_match)
			{

				$user = new userModel();
        		$user_details = $user->where('fuser_id', $job_match['fuser_id'])->select('fprofile_image')->first();

				$output .= '

				<div class="row mb-3 text-center">
				<div class="col-md-2">
					<img src="'.$url.'/public/profilepics/600/'.$user_details['fprofile_image'].'" alt="" width="100%" class="rounded-circle">
				</div>
				<div class="col">
					<p>'.$job_match['fuser_names'].'</p>
				</div>
			
				<div class="col">
					<a href="'.$url.'/'.$job_match['fuser_id'].'" class="btn btn-primary btn-sm rounded-0" target="_blank">View</a>
				</div>
			</div>
							
					'
				;
			 }

		}

		$data = array(
			'matches' => $output,
		);	

		return $this->response->setJSON($data);
		
	}

	public function jobs()
	{
		
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 

		return view('company/jobs');


	}


	public function job($job_id)
	{
		
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 

		$job_invite_message = new JobInviteMessageModel();
		$data['job_invite_messages'] = $job_invite_message->where('fjob_id', $job_id)->groupBy('fsender_id')->findAll();

		//$data['applicants_invited'] = $data['job_invite']['fapplicants_id'];

		return view('company/jobinvites', $data);

		//var_dump($data);

	}


	public function jobinviteaction()
	{
		//var_dump($this->request->getPost());
		$applicants = array_filter($this->request->getPost('applicant_id'));
		$extracted_applicants = implode(" -", $applicants);
		

		$data = [
		
			'fjob_id' => $this->request->getPost('job_id'),
			'fjob_title' => $this->request->getPost('job_title'),
			'fapplicants_id' => $extracted_applicants,
			'femployer_id' => $this->request->getPost('company_id'),
			'femployer_name' => $this->request->getPost('company_name'),
		
		];
            
		
		//var_dump($data);
		
	
        $job_invite = new JobInviteModel();
	    $job_invite->insert($data);
		$inserted = $job_invite->affectedRows();
		
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Job Invites sent successfully to selected candidates');
			return redirect()->to(base_url('company/jobdash'));
		}
		else
		{
			session()->setFlashdata('error', 'Error! Job Invites not sent. Try again.');
			return redirect()->to(base_url('company/jobdash'));
		}
	}



	public function inbox()
	{
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 
		
		$company_id = session()->company_id;
		
		$company = new CompanyModel();
		
        $data['company_details'] = $company->where('fcompany_id', $company_id)->first();

        $message = new MessageModel();
		$data['inbox'] = $message->where('freceiver_id', $company_id)->findAll();
		$data['sent'] = $message->where('fsender_id', $company_id)->findAll();

		$data['inbox_count'] = count($data['inbox']);
		$data['sent_count'] = count($data['sent']);

		//$job_invite_message = new JobInviteMessageModel();
		// $data['job_invite_messages'] = $job_invite_message->where('fuser_id',$applicant_id)->where('fjob_id', $job_id)->findAll();

		//var_dump($data);

		 return view('company/inbox', $data);

	}


	public function createmessageaction()
	{


		$applicant_id = session()->company_id;


		$applicant = new UserModel();
	
		$receiver = $applicant->where('fuser_id', $this->request->getPost('receiver_id'))->first();
		

		 $data = [

			'freceiver_id' => $this->request->getPost('receiver_id'),
			'freceiver_name' => $receiver['ffirst_name']. ' ' . $receiver['flast_name'],
			'fsender_id' => $this->request->getPost('sender_id'),
			'fsender_name' => $this->request->getPost('sender_name'),
			'fmessage_id' => 'MG' . time(),
			'fparent_message_id' => $this->request->getPost('parent_message_id'),
			'fcontent' => $this->request->getPost('content'),
			'fsubject' => $this->request->getPost('subject'),
            
		   ];
		   
		  //var_dump($data);
            
		
		$message = new MessageModel();
		$message->insert($data);
		$inserted = $message->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Message sent successfully');
			return redirect()->to(base_url('company/messages/inbox'));
		}
		else
		{
			session()->setFlashdata('error', 'Message not sent');
			return redirect()->to(base_url('company/messages/inbox'));
		}
	}


	public function sent()
	{
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 

		$company_id = session()->company_id;
		
		$company = new CompanyModel();
		
        $data['company_details'] = $company->where('fcompany_id', $company_id)->first();

        $message = new MessageModel();
		$data['inbox'] = $message->where('freceiver_id', $company_id)->findAll();
		$data['sent'] = $message->where('fsender_id', $company_id)->findAll();

		$data['inbox_count'] = count($data['inbox']);
		$data['sent_count'] = count($data['sent']);

		//$job_invite_message = new JobInviteMessageModel();
		// $data['job_invite_messages'] = $job_invite_message->where('fuser_id',$applicant_id)->where('fjob_id', $job_id)->findAll();

		//var_dump($data['sent']);

		 return view('company/sent', $data);

	}


	public function emaildetails($message_id)
	{
		$message = new MessageModel();
		
		$data['message_details'] = $message->where('fmessage_id', $message_id)->first();
		
		//var_dump($data);

		return view('company/email_details', $data);
	}


	public function messagedetails($job_id, $applicant_id)
	{

		if(!session()->id)
        {
            return redirect()->to(base_url('/slogin'));
		} 


		$company_id = session()->company_id;
		
		$company = new CompanyModel();
		
        $data['company_details'] = $company->where('fcompany_id', $company_id)->first();

		$job_invite_message = new JobInviteMessageModel();
		$data['messages'] = $job_invite_message->where('fjob_id', $job_id)->where('fuser_id', $company_id)->orderBy('frecno', 'asc')->findAll();
		
		//var_dump($data['messages']);

		$job_model = new JobModel();
		$data['job'] = $job_model->where('fjob_id', $job_id)->first();

		$applicant_id = $this->request->uri->getSegment(5);

		$applicant = new UserModel();
	
		$data['applicant_details'] = $applicant->where('fuser_id', $applicant_id)->first();

		$job_final_choice_model = new JobFinalChoiceModel();

		$data['applicant_status'] = $job_final_choice_model->where('fuser_id', $applicant_id)->where('fjob_id', $job_id)->first();


		//var_dump($data['applicant_status']);

		
		return view('company/job_invite_message_details', $data);

	}


	public function sendjobdashmessage()
	{

        $data = [

			'fjob_id' => $this->request->getPost('job_id'),
            'fjob_title' => $this->request->getPost('job_title'),
			'fmessage' => $this->request->getPost('message'),
			'fsubject' => $this->request->getPost('subject'),
			'fsender_id' => $this->request->getPost('company_id'),
			'freceiver_id' => $this->request->getPost('applicant_id'),
			'fuser_id' => $this->request->getPost('applicant_id'),
			'fapplicant_name' => $this->request->getPost('applicant_name'),
			'femployer_id' => $this->request->getPost('company_id'),
			'femployer_name' => $this->request->getPost('company_name'),
			
            
            ];
            
		//var_dump($data);

		$job_invite_message_model = new JobInviteMessageModel();
		$job_invite_message_model->insert($data);
		$inserted = $job_invite_message_model->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Message sent successfully');
			return redirect()->to(base_url('company/messagedetails/'.$this->request->getPost('job_id').'/'.$this->request->getPost('applicant_id')));
		}
		else
		{
			session()->setFlashdata('error', 'Message not added');
			return redirect()->to(base_url('company/messagedetails/'.$this->request->getPost('job_id').'/'.$this->request->getPost('applicant_id')));
		}
		
	}


	public function jobfinalchoice()
	{
		$data = [

			'fjob_id' => $this->request->getPost('job_id'),
            'fjob_title' => $this->request->getPost('job_title'),
			'fstatus' => $this->request->getPost('final_choice'),
			'fuser_id' => $this->request->getPost('applicant_id'),
			'fapplicant_name' => $this->request->getPost('applicant_name'),
			'femployer_id' => $this->request->getPost('company_id'),
			'femployer_name' => $this->request->getPost('company_name'),
			
            
            ];
            
		//var_dump($data);

		$job_final_choice_model = new JobFinalChoiceModel();
		$job_final_choice_model->insert($data);
		$inserted = $job_final_choice_model->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Choice sent successfully');
			return redirect()->to(base_url('company/messagedetails/'.$this->request->getPost('job_id').'/'.$this->request->getPost('applicant_id')));
		}
		else
		{
			session()->setFlashdata('error', 'Choice not added');
			return redirect()->to(base_url('company/messagedetails/'.$this->request->getPost('job_id').'/'.$this->request->getPost('applicant_id')));
		}

	}


	public function search()
	{
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		} 

		return view('company/search');
	}


	public function settings()
	{
		if(!session()->id)
        {
            return redirect()->to(base_url('/alogin'));
		}  
		
		return view('company/settings');
	}



	public function makeadmin()
	{
		
		$applicant = new UserModel();
		$applicant_details = $applicant->where('fuser_id', $this->request->getPost('user_id'))->first();

		// var_dump($applicant_details);

		if(empty($applicant_details))
		{
			session()->setFlashdata('error', 'User is not registered on Kokoruns. Kindly ask them to do so.');
            return redirect()->to(base_url('/company/dashboard'));
		}
		else
		{
			
			$aa = new CompanyAdminModel();
			$already_admin = $aa->where('fsubadmin_id', $this->request->getPost('user_id'))->where('fcompany_id', $this->request->getPost('company_id'))->first();
			//var_dump($already_admin);

			if(!empty($already_admin))
			{
				session()->setFlashdata('error', 'User is already a subadmin of this page.');
            	return redirect()->to(base_url('/company/dashboard'));
			}
			else
			{

				$data = [
					
					'fcompany_id' => $this->request->getPost('company_id'),
					'fsubadmin_id' => $this->request->getPost('user_id'),
					'fsubadmin_name' => $applicant_details['ffirst_name']. ' '. $applicant_details['flast_name'],
			
					];
					
				
				//var_dump($data);
			
		
				$asa = new CompanyAdminModel();
				$asa->insert($data);
				$inserted = $asa->affectedRows();
				if($inserted > 0)
				{
					session()->setFlashdata('success', 'Admin added successfully');
            		return redirect()->to(base_url('/company/dashboard'));
				}
				else
				{
					session()->setFlashdata('error', 'Error! Admin not added. Try again.');
            		return redirect()->to(base_url('/company/dashboard'));
				}

			}


		}

	}


	public function removeadmin($id)
	{
        $aam = new CompanyAdminModel();
        $aam->where('frecno', $id)->delete();
        $deleted = $aam->affectedRows();
		if($deleted > 0)
		{
			session()->setFlashdata('success', 'Admin removed successfully');
            return redirect()->to(base_url('/company/dashboard'));
		}
		else
		{
			session()->setFlashdata('error', 'Admin not removed. Try again.');
            return redirect()->to(base_url('/company/dashboard'));
		}

	}

    public function logout()
	{
		session()->destroy();
		return redirect()->to(base_url('/'));
	}

}
	