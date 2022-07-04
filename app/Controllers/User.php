<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployerModel;
use App\Models\UserExperienceModel;
use App\Models\UserEducationModel;
use App\Models\UserPortfolioModel;
use App\Models\UserDocumentModel;
use App\Models\UserWebLinkModel;
use App\Models\UserProSkillModel;
use App\Models\UserOtherSkillModel;
use App\Models\TeamModel;
use App\Models\JobModel;
use App\Models\JobInviteModel;
use App\Models\JobInviteMessageModel;
use App\Models\EventModel;
use App\Models\TeamMessageModel;
use App\Models\TeamMemberModel;
use App\Models\RecommendationRequestModel;
use App\Models\RecommendationOfferModel;
use App\Models\MessageModel;
use App\Models\ProfessionModel;
use App\Models\LanguageModel;
use App\Models\AssociationModel;
use App\Models\CompanyModel;
use App\Models\SchoolModel; 
use App\Models\PersonalityTraitModel;
use App\Models\AssociationAdminModel;
use App\Models\CompanyAdminModel;
use App\Models\SchoolAdminModel;
use App\Models\UserJobModel;
use App\Libraries\Zebra_Image;
use \DateTime;

class user extends BaseController
{


	protected $image;

    public function __construct()
    {
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}
		
        //$this->image = new Zebra_Image();
    }


    public function dashboard()
    {
	
		if(!session()->username)
        {
			session()->setFlashdata('error', 'Session expired. Login again');
            return redirect()->to(base_url('/login'));
		}
		
		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
        $data['users'] = $user->findAll();
		
        $user = new userModel();
        $data['user_details'] = $user->where('fuser_name', $username)->first();

        $a_admin = new AssociationModel();
		$data['association_admin_pages'] = $a_admin->where('fauthor', $user_id)->orderBy('frecno', 'desc')->findAll();
		$c_admin = new CompanyModel();
		$data['company_admin_pages'] = $c_admin->where('fauthor', $user_id)->orderBy('frecno', 'desc')->findAll();

		$s_admin = new SchoolModel();
		$data['school_admin_pages'] = $s_admin->where('fauthor', $user_id)->orderBy('frecno', 'desc')->findAll();
		
		$a_subadmin = new AssociationAdminModel();
		$data['association_subadmin_pages'] = $a_subadmin->where('fsubadmin_id', $user_id)->orderBy('frecno', 'desc')->findAll();

		$c_subadmin = new CompanyAdminModel();
		$data['company_subadmin_pages'] = $a_subadmin->where('fsubadmin_id', $user_id)->orderBy('frecno', 'desc')->findAll();

		$s_subadmin = new SchoolAdminModel();
		$data['school_subadmin_pages'] = $a_subadmin->where('fsubadmin_id', $user_id)->orderBy('frecno', 'desc')->findAll();

		$profession = new ProfessionModel();
		$data['professions'] = $profession->findAll();

		$language = new LanguageModel();
		$data['languages'] = $language->orderBy('fname', 'asc')->findAll();

		// var_dump($sessiondata);
		// var_dump($sessiondata2);
		// var_dump($sessiondata3);

		 //var_dump(session()->get());
		
		return view('user/dashboard', $data);
		
	}


	public function experiences_container()
	{
		$username = session()->username;
		$user_id = session()->user_id;

		$user_experience = new userExperienceModel();
		$user_experiences = $user_experience->where('fuser_id', $user_id)->orderBy('frecno', 'desc')->findAll();

		$user = new UserModel();
		$user_details = $user->where('fuser_id', $user_id)->first();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($user_experiences))
		{
			$output .= '
				
			<div class="exp-cont">
	
				<p>Seems you have no job experiences yet. Add a new record to get started.</p>

			</div> 

		<br>
						
					';
		}
		else
		{
			
		
			foreach($user_experiences as $experience)
			{
				$roles_array = explode(",", $experience['frole']);

				// foreach($roles_array as $role)
				// {
				// 	$role_list = $role;
				// }

					
				// $date1  = strtotime($experience['fstart']);
				// $day1   = date('d',$date1);
				// $month1 = date('m',$date1);
				// $year1  = date('Y',$date1);


				// $date1 = new DateTime($experience['fstart']);
				// $year1 = $date1->format('Y');
				// $month1 = $date1->format('m');

				$start_date = $experience['fstart'];

				// split "25-09-2012" into an array of three elements
				$thedate = explode("-", $start_date);

				// retrieve the values
				$year1 = $thedate[0]; // 25
				$month1 = $thedate[1]; // 09
				$day1 = $thedate[2]; // 2012


				$end_date = $experience['fend'];

				$thedate2 = explode("-", $end_date);


				// retrieve the values
				$year2 = $thedate2[0]; // 25
				$month2 = $thedate2[1]; // 09
				$day2 = $thedate2[2]; // 2012




					$output .= '
					
					<section class="user-experience card mb-5">
        
					<div class="experience-post-container">

					<div class="exp-cont">
						
					<div class="">     
						<div class="row">
							<div class="col">
								<span class="exp-date ">'.date('F Y', strtotime($experience['fstart'])).'</span> - 
								<span class="exp-date">'.date('F Y', strtotime($experience['fend'])).'</span>
							</div>
							<div class="col text-right">
								<div class="dropdown dropleft">
									<i class="fa fa-ellipsis-v cursor" dropdown-toggle" data-toggle="dropdown"></i>
									<div class="dropdown-menu rounded-0 bg-light p-0">
										<a class="dropdown-item p-0 edit-experience-btn cursor" data-toggle="modal" data-exp_id="'.$experience['frecno'].'" data-exp_start_month="'.$month1.'" data-exp_start_year="'.$year1.'" data-exp_end_month="'.$month2.'" data-exp_end_year="'.$year2.'" data-company="'.$experience['fcompany_name'].'" data-position="'.$experience['fposition'].'" data-roles="'.$experience['frole'].'"><i class="fa fa-edit text-warning"></i> &nbsp;Edit</a>
										<a class="dropdown-item p-0 exp-delete cursor" data-exp_id="'.$experience['frecno'].'"><i class="fa fa-trash text-danger"></i> Delete</a>
									
									</div>
								</div>
							</div>
						</div>
						
								
						<p class="mt-3 mb-2"><span class="exp-position">'.ucwords(strtolower($experience['fposition'])).'</span> </p>
						      
						
							<span class="company">'.ucwords(strtolower($experience['fcompany_name'])).'</span><br><br>
							

							<ul class="roles-and-respon">';


									foreach($roles_array as $role)
									{

										$output .= '<li>'.$role.'</li>';

									}
										
							$output .= '
									</ul><br>  
								       
							
								</div>
							</div>
						</section>';

			}

			

		}

		$data = array(
			'experiences' =>  $output,
		);	

		return $this->response->setJSON($data);
	}



    public function addexperienceaction()
    {
      
		if (! $this->validate([
			'start_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Start Month is required!',
				]
			],
			'start_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Start Year is required!',
							]
						],
			'end_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'End Month is required!',
				]
			],
			'end_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'End Year is required!',
							]
						],
        	'exposition' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Position is required!',
						
				]
            ],
            'ex-company-name' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Company is required!',
						
				]
			],
		]))
		{
			
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Job experience not added"
			];	
		}


		$start_day = 01;
		$end_day = 01;
		$start_month = $this->request->getPost('start_month');
		$start_year = $this->request->getPost('start_year');
		$end_month = $this->request->getPost('end_month');
		$end_year = $this->request->getPost('end_year');

		$start_duration = $start_year . '-' . $start_month . '-' . $start_day;
		$end_duration = $end_year . '-' . $end_month . '-' . $end_day;
	 	$start_DT = date('Y-m-d', strtotime("$start_duration"));
		$end_DT = date('Y-m-d', strtotime("$end_duration"));

		$roles = array_filter($this->request->getPost('ex-roles'));
		$extracted_roles = implode(",", $roles);
		
        $data = [
            'fstart' => $start_DT,
            'fend' => $end_DT,
            'fposition' => $this->request->getPost('exposition'),
            'fcompany_name' => $this->request->getPost('ex-company-name'),
            'frole' => $extracted_roles,
            'fuser_id' => $this->request->getPost('user_id'),
            'fuser_name' => $this->request->getPost('user_name'),
            ];
            
		
		//var_dump($data);
	

        $experience = new userExperienceModel();


		try
		{
			$experience->insert($data);
			$inserted = $experience->affectedRows();
			if($inserted > 0)
			{
				$response = [
					'success' => true,
					'data' => 'saved',
					'msg' => "Job experience added successfully"
				];
			}
			else
			{
				$response = [
					'success' => false,
					'data' => 'failed',
					'msg' => "Job experience not added"
				];	
			}
		}
		catch (\Exception $e)
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => $e->getMessage()
			];
		}

		return $this->response->setJSON($response);
			
    }

    public function updateexperienceaction()
    {
        //var_dump($this->request->getPost());
        
		if (! $this->validate([
			'start_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Start Month is required!',
				]
			],
			'start_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Start Year is required!',
							]
						],
			'end_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'End Month is required!',
				]
			],
			'end_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'End Year is required!',
							]
						],
        	'exposition' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Position is required!',
						
				]
            ],
            'ex-company-name' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Company is required!',
						
				]
			],
		]))
		{
			
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Job experience not added"
			];	
		}


        $id = $this->request->getPost('exp_id');

        $start_day = 01;
		$end_day = 01;
		$start_month = $this->request->getPost('start_month');
		$start_year = $this->request->getPost('start_year');
		$end_month = $this->request->getPost('end_month');
		$end_year = $this->request->getPost('end_year');

		$start_duration = $start_year . '-' . $start_month . '-' . $start_day;
		$end_duration = $end_year . '-' . $end_month . '-' . $end_day;
	 	$start_DT = date('Y-m-d', strtotime("$start_duration"));
		$end_DT = date('Y-m-d', strtotime("$end_duration"));

		$roles = array_filter($this->request->getPost('ex-roles'));
		$extracted_roles = implode(",", $roles);
		
        $data = [
            'fstart' => $start_DT,
            'fend' => $end_DT,
            'fposition' => $this->request->getPost('exposition'),
            'fcompany_name' => $this->request->getPost('ex-company-name'),
            'frole' => $extracted_roles,
            ];
            
		
		//var_dump($data);
	

        $experience = new userExperienceModel();
        $updated = $experience->update($id, $data);
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Job experience updated successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Job experience not updated"
			];
		}

		return $this->response->setJSON($response);

    }

    public function deletejobexperienceaction()
    {
		$id = $this->request->getPost('exp_id');
        $experience = new userExperienceModel();
        $experience->where('frecno', $id)->delete();
        $deleted = $experience->affectedRows();
		if($deleted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Job experience added successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Job experience added successfully"
			];
		}

		return $this->response->setJSON($response);
	}
	


	
	public function educations_container()
	{
		$username = session()->username;
		$user_id = session()->user_id;

		$user_education = new UserEducationModel();
		$user_educations = $user_education->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();

		$user = new userModel();
		$user_details = $user->where('fuser_id', $user_id)->first();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($user_educations))
		{
			$output .= '
				
			<div class="exp-cont">
	
			<p>Seems you have no education history yet. Add a new record to get started.</p>

		</div> 

			<br>
					';
		}
		else
		{
			
		
			foreach($user_educations as $education)
			{
				
				$skills_array = explode(",", $education['fskills_learned']); 


				$dateEdu1  = strtotime($education['fstart']);
				$dayEdu1   = date('d',$dateEdu1);
				$monthEdu1 = date('m',$dateEdu1);
				$yearEdu1  = date('Y',$dateEdu1);
					

				
				$dateEdu2  = strtotime($education['fend']);
				$dayEdu2   = date('d',$dateEdu2);
				$monthEdu2 = date('m',$dateEdu2);
				$yearEdu2  = date('Y',$dateEdu2);


				$output .='
				<div  style="padding-left: 59px; padding-top: 12px;">
            
				<section class="user-education card mb-5">   
	
					
					
					<div class="education-post-container">
						
						<div class="edu-cont">
							
							<div class="edu-cont-2f">


								<div class="row">
									<div class="col">
										<span class="exp-date ">'.date('F Y', strtotime($education['fstart'])).'</span> - 
										<span class="exp-date">'.date('F Y', strtotime($education['fend'])).'</span>
									</div>
									<div class="col text-right">
										<div class="dropdown dropleft">
											<i class="fa fa-ellipsis-v cursor" dropdown-toggle" data-toggle="dropdown"></i>
											<div class="dropdown-menu rounded-0 bg-light p-0">
												<a class="dropdown-item p-0 edu-edit-btn cursor" data-toggle="modal" data-edu_id="'.$education['frecno'].'" data-edu_id="'.$education['frecno'].'" data-edu_start_month="'.$monthEdu1.'" data-edu_start_year="'.$yearEdu1.'" data-edu_end_month="'.$monthEdu2.'" data-edu_end_year="'.$yearEdu2.'" data-school="'.$education['fschool'].'" data-class_of_degree="'.$education['fclass_of_degree'].'" data-course="'.$education['fcourse'].'" data-skills="'.$education['fskills_learned'].'"><i class="fa fa-edit text-warning"></i> &nbsp;Edit</a>
												<a class="dropdown-item p-0 exp-delete cursor" data-edu_id="'.$education['frecno'].'"><i class="fa fa-trash text-danger"></i> Delete</a>
											
											</div>
										</div>
									</div>
								</div>

								
					<span class="degree">'.$education['fclass_of_degree'].' '.$education['fcourse'].'</span>&nbsp;
					
					<br>           
					
					<span class="school">'.$education['fschool'].'</span><br><br>


							<ul class="skills-topics">';
							

							foreach($skills_array as $skill)
							{
			
								$output .= '<li>'.$skill.'</li>';
							} 

							
						$output .= '</ul>
							
					<br>        
						
				
							
					</div>        
						
				</div>     
						
				</div>
					
					
				</section>
					
				</div>'
				;
			}

			

		}

		$data = array(
			'educations' =>	$output,
		);	

		return $this->response->setJSON($data);
	}



    public function addeducationaction()
    {
        //var_dump($this->request->getPost());
        if (! $this->validate([
			'start_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Start Month is required!',
				]
			],
			'start_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Start Year is required!',
							]
						],
			'end_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'End Month is required!',
				]
			],
			'end_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'End Year is required!',
							]
						],
        	'school' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'School attended is required!',
						
				]
            ],
            'course' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Course studied is required!',
						
				]
            ],
            'class_of_degree' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Class Of Degree is required!',
						
				]
            ],
            
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Education Validation failed"
			];	
		}
		

		$start_day = 01;
		$end_day = 01;
		$start_month = $this->request->getPost('start_month');
		$start_year = $this->request->getPost('start_year');
		$end_month = $this->request->getPost('end_month');
		$end_year = $this->request->getPost('end_year');

		$start_duration = $start_year . '-' . $start_month . '-' . $start_day;
		$end_duration = $end_year . '-' . $end_month . '-' . $end_day;
	 	$start_DT = date('Y-m-d', strtotime("$start_duration"));
		$end_DT = date('Y-m-d', strtotime("$end_duration"));

		$skills = array_filter($this->request->getPost('skills'));
		$extracted_skills = implode(",", $skills);
        
        $data = [
            'fstart' => $start_DT,
            'fend' => $end_DT,
            'fschool' => $this->request->getPost('school'),
            'fcourse' => $this->request->getPost('course'),
            'fclass_of_degree' => $this->request->getPost('class_of_degree'),
            'fskills_learned' => $extracted_skills,
            'fuser_id' => $this->request->getPost('user_id'),
            'fuser_name' => $this->request->getPost('user_name'),
            ];
            
        //var_dump($data);

        $education = new userEducationModel();
	    $education->insert($data);
	   	$inserted = $education->affectedRows();
		   if($inserted > 0)
		   {
			   $response = [
				   'success' => true,
				   'data' => 'saved',
				   'msg' => "Education added successfully"
			   ];
		   }
		   else
		   {
			   $response = [
				   'success' => false,
				   'data' => 'failed',
				   'msg' => "Education not added"
			   ];	
		   }

		return $this->response->setJSON($response);
   
    }

    public function updateeducationaction()
    {
		if (! $this->validate([
			'start_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Start Month is required!',
				]
			],
			'start_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Start Year is required!',
							]
						],
			'end_month' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'End Month is required!',
				]
			],
			'end_year' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'End Year is required!',
							]
						],
        	'school' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'School attended is required!',
						
				]
            ],
            'course' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Course studied is required!',
						
				]
            ],
            'class_of_degree' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Class Of Degree is required!',
						
				]
            ],
            
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Education Validation failed"
			];	
		}
		

		$start_day = 01;
		$end_day = 01;
		$start_month = $this->request->getPost('start_month');
		$start_year = $this->request->getPost('start_year');
		$end_month = $this->request->getPost('end_month');
		$end_year = $this->request->getPost('end_year');

		$start_duration = $start_year . '-' . $start_month . '-' . $start_day;
		$end_duration = $end_year . '-' . $end_month . '-' . $end_day;
	 	$start_DT = date('Y-m-d', strtotime("$start_duration"));
		$end_DT = date('Y-m-d', strtotime("$end_duration"));

		$skills = array_filter($this->request->getPost('skills'));
		$extracted_skills = implode(",", $skills);

        $id = $this->request->getPost('edu_id');
        
		$data = [
            'fstart' => $start_DT,
            'fend' => $end_DT,
            'fschool' => $this->request->getPost('school'),
            'fcourse' => $this->request->getPost('course'),
            'fclass_of_degree' => $this->request->getPost('class_of_degree'),
            'fskills_learned' => $extracted_skills,
            ];
            
        //var_dump($data);
            
       
        $education = new userEducationModel();
        $updated = $education->update($id, $data);
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Education updated successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Education not updated"
			];
		}

		return $this->response->setJSON($response);
	}
	

    public function deleteeducationaction()
    {
	    $id = $this->request->getPost('edu_id');
	   
        $education = new UserEducationModel();
        $education->where('frecno', $id)->delete();
        $deleted = $education->affectedRows();
		if($deleted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Education deleted successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Education not deleted"
			];
		}

		return $this->response->setJSON($response);
	}
	

    public function uploadportfolioaction()
    {
        if (! $this->validate([
			'fileid' => [
                'uploaded[fileid]',
                'mime_in[fileid,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[fileid,4096]',
            ],
	
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('user/dashboard'));
			
        }
        
        $filh = $this->request->getFile('fileid');
		$ndfgf = $filh->getRandomName();
		$PATH = getcwd();
		$filh->move($PATH .'/public/portfoliopics', $ndfgf);


		$user_id = $this->request->getPost('user_id');
		$username = $this->request->getPost('user_name');

		 $data = [

			'fimage' => $ndfgf,
			'fuser_id' => $this->request->getPost('user_id'),
			'fuser_name' => $this->request->getPost('user_name'),
		];
		
		$portfolio = new userPortfolioModel();
        
		$portfolio->insert($data);
	    $inserted = $portfolio->affectedRows();
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


		return $this->response->setJSON($response);
		

	}
	
	public function deleteportfolioaction()
	{	

		$image_id = $this->request->getPost('image_id');
		$image_name = $this->request->getPost('image_name');
		
		$portfolio = new UserPortfolioModel();
		$portfolio->where('frecno', $image_id)->delete();
		
		$deleted = $portfolio->affectedRows();
		if($deleted > 0)
		{
			$PATH = getcwd();
			unlink($PATH .'/public/portfoliopics/'.$image_name);
			$response = [
				'success' => true,
				'data' => 'done',
				'msg' => "Image deleted"
			];
		}

		return $this->response->setJSON($response);

	}




	public function load_images()
	{
		$username = session()->username;
		$user_id = session()->user_id;


		$user_portfolio = new userPortfolioModel();
		$user_portfolios = $user_portfolio->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($user_portfolios))
		{
			$output .= '
				
			<div class="exp-cont">
	
			<p>Seems you have no portfolio images yet. Add a new photo to get started.</p>

		</div> 

			<br>
					';
		}
		else
		{
			$url = site_url();
		
			foreach($user_portfolios as $portfolio)
			{
			
				$output .='
						<div class="col-lg-6">
							<img class="image img-thumbnail" src="'.$url.'/public/portfoliopics/'.$portfolio['fimage'].'" width="100%">
							<span data-image_id="'.$portfolio['frecno'].'" data-image_name="'.$portfolio['fimage'].'" class="fa fa-times text-danger cursor delete-image" style="position:relative;top:-90px;right:35px;"></span>
						</div>
				';
			}

		}

			$data = array(
				'portfolio_images' => $output,
			);	

			return $this->response->setJSON($data);
	}


	public function uploaddocumentaction()
    {
        if (! $this->validate([
			'fileid' => [
                'uploaded[docid]',
                'mime_in[docid,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[docid,4096]',
            ],
	
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Document not uploaded"
			];
			
        }
        
        $filh = $this->request->getFile('docid');
		$ndfgf = $filh->getRandomName();
		$PATH = getcwd();
		$filh->move($PATH .'/public/documentpics', $ndfgf);


		$user_id = $this->request->getPost('user_id');
		$username = $this->request->getPost('user_name');

		$data = [

			'ffile_name' => $ndfgf,
			'fuser_id' => $this->request->getPost('user_id'),
			'fuser_name' => $this->request->getPost('user_name'),
		];
		
		$document = new userDocumentModel();
        
		$document->insert($data);
	    $inserted = $document->affectedRows();
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

		//var_dump($this->request->getPost());

		return $this->response->setJSON($response);
		

	}



	public function load_docs()
	{
		$username = session()->username;
		$user_id = session()->user_id;

		$user_document = new userDocumentModel();
		$user_documents = $user_document->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();
		


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($user_documents))
		{
			$output .= '
				
			<div class="exp-cont">
	
			<p>Seems you have no documents yet. Add a new document to get started.</p>

		</div> 

			<br>
					';
		}
		else
		{
			$url = site_url();
		
			foreach($user_documents as $document)
			{
			
				$output .='
						
						<div class="col-lg-6">
							<img class="image img-thumbnail" src="'.$url.'/public/documentpics/'.$document['ffile_name'].'" width="100%">
							<span data-doc_id="'.$document['frecno'].'" data-doc_name="'.$document['ffile_name'].'" class="fa fa-times text-danger cursor delete-image2" style="position:relative;top:-90px;right:35px;"></span>	
						</div>
				';
			}

		}

			$data = array(
				'docs' => $output,
			);	

			return $this->response->setJSON($data);
	}




	public function deleteducumentaction()
	{	

		$doc_id = $this->request->getPost('doc_id');
		$doc_name = $this->request->getPost('doc_name');
		
		$document = new userDocumentModel();
		$document->where('frecno', $doc_id)->delete();
		$deleted = $document->affectedRows();
		if($deleted > 0)
		{
			$PATH = getcwd();
			unlink($PATH .'/public/documentpics/'.$doc_name);
			$response = [
				'success' => true,
				'data' => 'done',
				'msg' => "Document deleted"
			];
		}

		return $this->response->setJSON($response);

	}


    public function addweblinkaction()
    {
        //var_dump($this->request->getPost());
        if (! $this->validate([
			
			'weblink' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Weblink is required!',
				]
			],
		
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/user/dashboard'));
        }

        $data = [
			'ftitle' => $this->request->getPost('link_title'),
			'fweb_link' => $this->request->getPost('weblink'),
			'fuser_id' => $this->request->getPost('user_id'),
            'fuser_name' => $this->request->getPost('user_name'),
            
            ];
            
        //var_dump($data);

        $weblink = new userWebLinkModel();
		$weblink->insert($data);
	    $inserted = $weblink->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Web link added successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Web Link not added"
			];
		}	

		//var_dump($this->request->getPost());

		return $this->response->setJSON($response);
        
	}
	
	public function deleteweblinkaction()
	{
		$link_id = $this->request->getPost('link_id');
		$weblink = new userWebLinkModel();
        $weblink->where('frecno', $link_id)->delete();
    	$deleted = $weblink->affectedRows();
		if($deleted > 0)
		{
            $response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Web link deleted successfully"
			];
        }
        else
        {
            $response = [
				'success' => false,
				'data' => 'saved',
				'msg' => "Error. Try again"
			];
		}
		
		return $this->response->setJSON($response);
	}



	public function load_weblinks()
	{
		$username = session()->username;
		$user_id = session()->user_id;


		$user_weblink = new userWebLinkModel();
		$user_weblinks = $user_weblink->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($user_weblinks))
		{
			$output .= '
				
			<div class="exp-cont">
	
			<p>Seems you have no weblinks yet. Add a new photo to get started.</p>

		</div> 

			<br>
					';
		}
		else
		{
			$url = site_url();
		
			foreach($user_weblinks as $weblink)
			{
			
				$output .='
						<a class="delete-weblink" data-weblink="'.$weblink['frecno'].'">
							<div class="skill-padding">
								<div class="skill">'.$weblink['ftitle'].'&nbsp;
									<button class="delete-skill-button ">x</button>
								</div>
							</div>
						</a>
				';
			}

		}

			$data = array(
				'weblinks' => $output,
			);	

			return $this->response->setJSON($data);
	}




	public function addproskillaction()
    {
        //var_dump($this->request->getPost());
        if (! $this->validate([
			'pro_skill' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Skill is required!',
				]
			],
		
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/user/dashboard'));
        }

        $data = [
            
			'fskill' => $this->request->getPost('pro_skill'),
			'fuser_id' => $this->request->getPost('user_id'),
            'fuser_name' => $this->request->getPost('user_name'),
            
            ];
            
        //var_dump($data);

        $pro_skill = new userProSkillModel();
		$pro_skill->insert($data);
	    $inserted = $pro_skill->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Pro Skill added successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Pro Skill not added"
			];	
		}

		return $this->response->setJSON($response);
        
	}


	public function deleteproskillaction()
	{
		$id = $this->request->getPost('skill_id');
		$pro_skill = new userProSkillModel();
        $pro_skill->where('frecno', $id)->delete();
        $deleted = $pro_skill->affectedRows();
		if($deleted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Pro Skill deleted successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Pro Skill not deleted"
			];	
		}

		return $this->response->setJSON($response);
	}



	public function pro_skills_container()
	{
		$username = session()->username;

		$user_id = session()->user_id;

		$pro_skill = new UserProSkillModel();
		$pro_skills = $pro_skill->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();


		$user = new userModel();
		$user_details = $user->where('fuser_id', $user_id)->first();

		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($pro_skills))
		{
			$output .= '
				
					<div class="text-center">
						<p class="text-center">Seems you have no professional skills yet. Add a new record to get started.</p>
					</div>
						
					';
		}
		else
		{
			
		
			foreach($pro_skills as $skill)
			{
				

				$output .= '
				
					<div class="skill-padding">
						<div class="skill">
								'.$skill['fskill'].'&nbsp;<button class="delete-skill-button pro_skill_delete_btn" data-pro_skill_id="'.$skill['frecno'].'">x</button>
						</div>
					</div>

				';
			}

			

		}

		$data = array(
			'pro_skills_list' =>	$output,
		);	

		return $this->response->setJSON($data);


	}



	public function addotherskillaction()
    {
        // var_dump($this->request->getPost());
        if (! $this->validate([
			'other_skill' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Skill is required!',
				]
			],
		
		]))
		{
			session()->setFlashdata('error', $this->validator->getErrors());
			return redirect()->to(base_url('/user/dashboard'));
        }

        $data = [
            
			'fskill' => $this->request->getPost('other_skill'),
			'fuser_id' => $this->request->getPost('user_id'),
            'fuser_name' => $this->request->getPost('user_name'),
            
            ];
            
        //var_dump($data);

        $other_skill = new userOtherSkillModel();
		$other_skill->insert($data);
	    $inserted = $other_skill->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Other Skill added successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Other Skill not added"
			];	
		}

		return $this->response->setJSON($response);
        
	}
	

	public function deleteotherskillaction()
	{
		$id = $this->request->getPost('skill_id');
		$other_skill = new userOtherSkillModel();
        $other_skill->where('frecno', $id)->delete();
        $deleted = $other_skill->affectedRows();
		if($deleted > 0)
		{
            $response = [
				'success' => true,
				'data' => 'deleted',
				'msg' => "Other Skill deleted successfully"
			];
        }
        else
        {
            $response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Other Skill not deleted"
			];	
		}
		
		return $this->response->setJSON($response);
	}



	public function other_skills_container()
	{
		$username = session()->username;

		$user_id = session()->user_id;

		$other_skill = new UserOtherSkillModel();
		$other_skills = $other_skill->where('fuser_name', $username)->orderBy('frecno', 'desc')->findAll();


		$user = new userModel();
		$user_details = $user->where('fuser_id', $user_id)->first();

		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($other_skills))
		{
			$output .= '
				
					<div class="">
						<p style="text-align:center">Seems you have no other skills yet. Add a new record to get started.</p>
					</div>
						
					';
		}
		else
		{
			
		
			foreach($other_skills as $skill)
			{
				

				$output .= '
 
				
				<div class="skill-padding "><div class="skill" >'.$skill['fskill'].'&nbsp;<button data-skill_id="'.$skill['frecno'].'" class="delete-skill-button delete-other-skill-button">x</button></div></div>
			

				';
			}

			

		}

		$data = array(
			'other_skills' =>	$output,
		);	

		return $this->response->setJSON($data);


	}


	public function changelocation()
	{
		$id = $this->request->getPost('id');

		$data = [

			'fstate' => $this->request->getPost('state'),
			'flga' => $this->request->getPost('lga'),	

		];


		//var_dump($id);


		$usermodel = new userModel();
		$updated = $usermodel->update($id, $data);
		if($updated > 0)
		{
			session()->setFlashdata('success', 'Location updated successfully');
			return redirect()->to(base_url('user/dashboard'));
		}
		else
		{
			session()->setFlashdata('error', 'Location not updated. Try again');
			return redirect()->to(base_url('user/dashboard'));
		}
		

	}


	public function updatebio()
	{
		$id = $this->request->getPost('id');

		if(!empty($this->request->getPost('availability_start_date2')))
		{
			$start_date = $this->request->getPost('availability_start_date2');
		}
		else
		{
			if($this->request->getPost('availability_start_date') == 'now')
			{
				$start_date = date('Y-m-d', strtotime("January 1 2000"));
			}
			else if($this->request->getPost('availability_start_date') == 'not_yet')
			{
				$start_date = null;
			}
			
		}

		$data = [

			'fgender' => $this->request->getPost('gender'),
			'fmarital_status' => $this->request->getPost('marital_status'),	
			'fdisabled' => $this->request->getPost('disabled'),
			'feducational_qualification' => $this->request->getPost('certificate'),	
			'fother_professions1' => $this->request->getPost('other_professions1'),
			'fother_professions2' => $this->request->getPost('other_professions2'),	
			'fother_professions3' => $this->request->getPost('other_professions3'),
			'fother_professions4' => $this->request->getPost('other_professions4'),	
			'flanguages1' => $this->request->getPost('languages1'),
			'flanguages2' => $this->request->getPost('languages2'),	
			'flanguages3' => $this->request->getPost('languages3'),
			'flanguages4' => $this->request->getPost('languages4'),	
			'flanguages5' => $this->request->getPost('languages5'),
			'fcurrent_employer' => $this->request->getPost('current_employer'),	
			'fstate' => $this->request->getPost('state'),
			'flga' => $this->request->getPost('lga'),	
			'femployment_type' => $this->request->getPost('employment_type'),
			'fpreferred_job1' => $this->request->getPost('preferred_job1'),	
			'fpreferred_job2' => $this->request->getPost('preferred_job2'),
			'fpreferred_job3' => $this->request->getPost('preferred_job3'),	
			'fpreferred_job4' => $this->request->getPost('preferred_job4'),
			'fpreferred_job_location_state' => $this->request->getPost('preferred_job_location_state'),	
			'fpreferred_job_location_lga' => $this->request->getPost('preferred_job_location_lga'),
			'favailability_start_date' => $start_date,
		];


		//var_dump($id);


		$usermodel = new userModel();
		$updated = $usermodel->update($id, $data);
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'updated',
				'msg' => "Profile updated successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Not updated"
			];
		}

		return $this->response->setJSON($response);
		
	}




	public function profilesetupaction()
	{
		//var_dump($this->request->getPost());
		if (! $this->validate([

			'phone' => 'required|trim',
			'email' => 'required|trim',
			
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Profile not updated"
			];
		}
		
		$id = $this->request->getPost('id');
		
		$data = [

			'fphone'  => $this->request->getPost('phone'),
			'fminimum_salary'  => $this->request->getPost('minimum_salary') ,
			'femail'  => $this->request->getPost('email'),

			];

		//var_dump($data);

		$usermodel = new userModel();
		$updated = $usermodel->update($id, $data);
		if($updated > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Profile updated successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Other Skill not added"
			];	
		}

		return $this->response->setJSON($response);

	}


	public function changeprofilepic()
	{
		// if (! $this->validate([
		// 	'profile_pic_id' => [
        //         'uploaded[profile_pic_id]',
        //         'mime_in[profile_pic_id,image/jpg,image/jpeg,image/gif,image/png]',
        //         'max_size[profile_pic_id,4096]',
        //     ],
	
		// ]))
		// {
		// 	$response = [
		// 		'success' => false,
		// 		'data' => 'failed',
		// 		'msg' => "Profile Picture not updated"
		// 	];
			
		// }

		$id = $this->request->getPost('id');
		$old_image_name = $this->request->getPost('old_image_name');
		// $image_data = $_POST['image_data'];
		//$ghka = $picd->getRandomName();
		
		
		// $image_array_1 = explode(";", $image_data);

		// $image_array_2 = explode(",", $image_array_1[1]);
	   
		// $image_name = base64_decode($image_array_2[1]);

		$data = $_POST["image"];

		$image_array_1 = explode(";", $data);
	   
		$image_array_2 = explode(",", $image_array_1[1]);
	   
		$data = base64_decode($image_array_2[1]);
	   
		$imageName = time() . '.png';
		
		//$imageName = time().'.jpg';
		$PATH = getcwd();
        file_put_contents($PATH .'/public/profilepics/'.$imageName, $data);
	   
	
		//  $data = [

		// 	'fprofile_image' => $ghka,
		// ];

		// $user = new userModel();
        // $updated = $user->update($id, $data);
		// if($updated > 0)
		// {
		// 	// $PATH = getcwd();
		// 	// $picd->move($PATH .'/public/profilepics', $ghka);

		// 	$PATH = getcwd();
		// 	unlink($PATH .'/public/profilepics/600/'.$old_image_name);

		// 	$response = [
		// 		'success' => true,
		// 		'data' => 'saved',
		// 		'msg' => "Profile Picture updated successfully"
		// 	];
		// }
		// else
		// {
		// 	$response = [
		// 		'success' => false,
		// 		'data' => 'failed',
		// 		'msg' => "JProfile Picture not updated"
		// 	];
		// }

		//return $this->response->setJSON($data);
	}


	public function createteamaction()
	{
		 // var_dump($this->request->getPost());
		 if (! $this->validate([
			'team_name' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Team Name is required!',
				]
			],
			'team_description' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Team Description is required!',
							]
						],
			'team_privacy' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Team Privacy Setting is required!',
				]
			],
		
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Team not added"
			];
		}
		

		$team_id = 'TM' . time();

        $data = [
            
			'fteam_name' => $this->request->getPost('team_name'),
			'fteam_id' => $team_id,
			'fdescription' => $this->request->getPost('team_description'),
			'fprivacy' => $this->request->getPost('team_privacy'),
			'fadmin' => $this->request->getPost('user_id'),
            
            ];
            
        //var_dump($data);

        $team = new TeamModel();
		$team->insert($data);
		$inserted = $team->affectedRows();
		
		if($inserted > 0)
		{
			//Insert owner in team member list
			$data = [
            
				'fteam_id' => $team_id,
				'fuser_id' => $this->request->getPost('user_id'),
				'fuser_name' => $this->request->getPost('user_name'),
				'fis_admin' => 1,
				'fis_active' => 1,
				
				];
				
			//var_dump($data);
	
			$team_member = new TeamMemberModel();
			
			try
			{
					$team_member->insert($data);
					//$inserted = $team_member->affectedRows();
					// if($inserted > 0)
					// {
					// 	$response = [
					// 		'success' => true,
					// 		'data' => 'saved',
					// 		'msg' => "Memeber added successfully"
					// 	];
						
					// }
					// else
					// {
					// 	$response = [
					// 		'success' => false,
					// 		'data' => 'failed',
					// 		'msg' => "Member not created"
					// 	];	
						
					// }
			}
			catch (\Exception $e)
			{
				// $response = [
				// 	'success' => false,
				// 	'data' => 'failed',
				// 	'msg' => $e->getMessage()
				// ];
			}
	
		
			session()->setFlashdata('success', 'Team created successfully');
			return redirect()->to(base_url('user/teams/'));
		}
		else
		{
			session()->setFlashdata('error', 'Team not created');
			return redirect()->to(base_url('user/teams/'));
		}

		
	}



	public function load_user_bio()
	{
		$username = session()->username;

		$user_id = session()->user_id;


		$user_bio = new UserModel();
		$user_bio_details = $user_bio->where('fuser_id', $user_id)->first();

		// $output = "";
		// $total_item = 0;
		// $url = site_url();

		if(empty($user_bio_details))
		{
			$output .= '
				
					<div class="">
					
					</div>
						
					';
		}
		else
		{
				if(empty($user_bio_details['fgender']))
				{ 
					$gender_value = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$gender_value = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['fgender']. "</span>";
				} 

				if(empty($user_bio_details['fmarital_status']))
				{ 
					$marital_status = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$marital_status = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['fmarital_status']. "</span>";
				}
				
				if(empty($user_bio_details['feducational_qualification']))
				{ 
					$education = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$education = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['feducational_qualification']. "</span>";
				}

				if(empty($user_bio_details['fother_professions1']))
				{ 
					$other_professions1 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$other_professions1 = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fother_professions1']. "</span>";
				}

				if(empty($user_details['fother_professions2']))
				{ 
					$other_professions2 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$other_professions2 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['fother_professions2']. "</span>";
				}

				if(empty($user_bio_details['fother_professions3']))
				{ 
					$other_professions3 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$other_professions3 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['fother_professions3']. "</span>";
				}
				
				if(empty($user_bio_details['fother_professions4']))
				{ 
					$other_professions4 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$other_professions4 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['fother_professions4']. "</span>";
				}

				if(empty($user_bio_details['flanguages1']))
				{ 
					$languages1 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$languages1 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['flanguages1']. "</span>";
				}

				if(empty($user_bio_details['flanguages2']))
				{ 
					$languages2 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$languages2 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['flanguages2']. "</span>";
				}

				if(empty($user_bio_details['flanguages3']))
				{ 
					$languages3 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$languages3 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['flanguages3']. "</span>";
				}

				if(empty($user_bio_details['flanguages4']))
				{ 
					$languages4 = "<span style='text-decoration: none; color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$languages4 = "<span style='text-decoration: none; color: #0991ff;'>" .$user_bio_details['flanguages4']. "</span>";
				}

				if(empty($user_bio_details['flanguages5']))
				{ 
					$languages5 = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$languages5 = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['flanguages5']. "</span>";
				}

				if(empty($user_bio_details['fcurrent_employer']))
				{ 
					$current_employer = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$current_employer = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fcurrent_employer']. "</span>";
				} 

				if(empty($user_bio_details['fstate']))
				{ 
					$state = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$state = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fstate']. "</span>";
				}

				if(empty($user_bio_details['flga']))
				{
					$lga = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$lga = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['flga']. "</span>";
				}

				if(empty($user_bio_details['femployment_type']))
				{ 
					$employment_type = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$employment_type = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['femployment_type']. "</span>";
				}

				if(empty($user_bio_details['fpreferred_job1']))
				{ 
					$preferred_job = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$preferred_job = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fpreferred_job1']. "</span>";
				}

				if(empty($user_details['fpreferred_job_location_state']))
				{ 
					$preferred_job_location_state = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$preferred_job_location_state = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fpreferred_job_location_state']. "</span>";
				}

				if(empty($user_bio_details['fpreferred_job_location_lga']))
				{ 
					$preferred_job_location_lga = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$preferred_job_location_lga = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fpreferred_job_location_lga']. "</span>";
				}

				if(empty($user_bio_details['fdisabled']))
				{ 
					$disabled = "<span style='text-decoration: none;  color: #dc3545;'>Not Set</span>"; 
				}
				else
				{ 
					$disabled = "<span style='text-decoration: none;  color: #0991ff;'>" .$user_bio_details['fdisabled']. "</span>";
				}

				
				if($user_bio_details['favailability_start_date'] == '2000-01-01')
				{
					$availability_start_date = "<span style='text-decoration: none;  color: #0991ff;'>Immediately</span>";
				}
				else if($user_details['favailability_start_date'] == null)
				{
					$availability_start_date = "<span style='text-decoration: none;  color: #dc3545;'>Not Yet</span>";
				}
				else
				{
					$availability_start_date = "<span style='text-decoration: none;  color: #0991ff;'>" .date('F j, Y', strtotime($user_bio_details['favailability_start_date'])). "</span>";
				}
					
					
				$output .= '
 
				
					<label class="bio-info-label mb-0" style="color:#262222">Recommendations    
					</label>
					<p class="text-primary mt-0 mb-0" style="text-decoration: none; color: #0991ff;">47</p>
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Gender    
					</label><br>
					'.$gender_value.'
					<br style="line-height:2.0">
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Married    
					</label><br>
					'.$marital_status.'
					<br style="line-height:2.0">    
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Academic Level    
					</label><br>
						'.$education.'
					<br style="line-height:2.0">
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Other Professions
					</label><br>
					<p class="mb-0"> 
					'.$other_professions1.'
					 </p> 
					 <p class="mb-0"> 
					 '.$other_professions2.'
					  </p>
					  <p class="mb-0"> 
					'.$other_professions3.'
					 </p>
					 <p class="mb-0"> 
					'.$other_professions4.'
					 </p>
					<!-- <br style="line-height:2.0">  -->
					
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Languages
					</label><br>
					<p class="mb-0"> 
					  '.$languages1.'
					</p>
					
					<p class="mb-0"> 
					  '.$languages2.'
					</p>

					<p class="mb-0"> 
					  '.$languages3.'
					</p>

					<p class="mb-0"> 
					  '.$languages4.'
					</p>

					<p class="mb-0"> 
					  '.$languages5.'
					</p>
					    
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Company / Brand    
					</label><br>
					'.$current_employer.'
					<br style="line-height:2.0"> 
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Location    
					</label>
					<p class="mb-0"> State: '.$state.'<br style="line-height:2.0"> </p> 
					<p class="mb-0"> LGA: '.$lga.' </p> 
				
						
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Job Preference
					</label>
					<div class="border p-3 rounded">
					<p class="mb-0">Type - '.$employment_type.'</p>
					<p class="mb-0">Profession - '.$preferred_job.'</p>
					<p class="mb-0">Preferred Location </p>
					<p class="mb-0 pl-3">State - '.$preferred_job_location_state.',  </p>
					<p class="mb-0 pl-3">LGA - '.$preferred_job_location_lga.' </p>      
					</div>
		
					<label class="bio-info-label mb-0 mt-2" style="color:#262222">Disabled?    
					</label><br>
					
					'.$disabled.'

					<br><label class="bio-info-label mb-0 mt-2" style="color:#262222">Start Date    
					</label><br>
					'.$availability_start_date.'
					<br style="line-height:2.0">
			

				';

		}

		$data = array(
			'bio_data' =>	$output,
		);	

		return $this->response->setJSON($data);
	}


	public function jointeamaction()
	{
		//var_dump($this->request->getPost());
		
        $data = [
            
			'fteam_id' => $this->request->getPost('team_id_store'),
			'fuser_id' => $this->request->getPost('user_id'),
			'fuser_name' => $this->request->getPost('user_name'),
			'fis_active' => 1,
            
            ];
            
		//var_dump($data);

		$team_member = new TeamMemberModel();
		
		try
		{
				$team_member->insert($data);
				$inserted = $team_member->affectedRows();
				if($inserted > 0)
				{
					$response = [
						'success' => true,
						'data' => 'saved',
						'msg' => "Memeber added successfully"
					];
					
				}
				else
				{
					$response = [
						'success' => false,
						'data' => 'failed',
						'msg' => "Member not created"
					];	
					
				}
		}
		catch (\Exception $e)
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => $e->getMessage()
			];
		}

        
		return $this->response->setJSON($response);

	}


	public function jointeamaction2()
	{
		//var_dump($this->request->getPost());
		
        $data = [
            
			'fteam_id' => $this->request->getPost('team_id'),
			'fuser_id' => $this->request->getPost('user_id'),
			'fuser_name' => $this->request->getPost('user_name'),
			'fis_active' => 0,
            
            ];
            
		//var_dump($data);

		$team_member = new TeamMemberModel();
		
		try
		{
				$team_member->insert($data);
				$inserted = $team_member->affectedRows();
				if($inserted > 0)
				{
					$response = [
						'success' => true,
						'data' => 'saved',
						'msg' => "Memeber added successfully"
					];
					
				}
				else
				{
					$response = [
						'success' => false,
						'data' => 'failed',
						'msg' => "Member not created"
					];	
					
				}
		}
		catch (\Exception $e)
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => $e->getMessage()
			];
		}

        
		return $this->response->setJSON($response);

	}


	public function getteammembers()
	{
		$team_id = $this->request->getPost('team_id');

		$member = new TeamMemberModel();
		$team_members = $member->where('fteam_id', $team_id)->select('fuser_id, fuser_name')->findAll();
		$output = "";
		
		if(empty($team_members))
         {
			
             $output = '<div>No team members yet</div>';
         }
         else
         {
			$url = base_url();
            
				foreach ($team_members as $member) 
				{
					if($member['fis_admin'] == 1)
					{
						$is_admin = "Admin";
					}
					else
					{
						$is_admin = "";
					}
   							
					$user = new userModel();
        			$user_details = $user->where('fuser_id', $member['fuser_id'])->select('fprofile_image')->first();
					$output .='
						<div class="row mb-1">
							<div class="col-md-3">
							<img class="rounded-circle" src='.$url.'/public/profilepics/600/'.$user_details['fprofile_image'].' width="100%">
							</div>
							<div class="col-md-7 px-0">
							<p style="font-size:1rem;">'.$member['fuser_name'].'</p>
							<span>'.$is_admin.'</span>
							</div>
							<div class="col-md-1">
							
							<div class="dropdown dropleft">
								<i class="fa fa-ellipsis-v cursor" class="dropdown-toggle" data-toggle="dropdown"></i>
								<div class="dropdown-menu">
									<a class="dropdown-item text-center" href="#"> <i class="fa fa-trash text-danger"></i> Delete</a>
								</div>
								</div>
							</div>
						</div>
					 '; 
				}
			
		 }
		 
		 
         $data = array(
            'team_member_names'	=>	$output,
			'team_members_count' =>	count($team_members),
         );	

		return $this->response->setJSON($data);
	}

	public function searchusers()
	{
		$keyword = $this->request->getPost('keyword');
		$user_model = new userModel();
		$users = $user_model->where('fuser_id !=', session()->user_id)->where('factive', 1)->like('ffirst_name', $keyword)->findAll();
		$output = "";
		
		if(empty($users))
         {
             $output = '';
         }
         else
         {
			$output .= '<ul class="w3-card-2" style="padding-left:6px;">';

            foreach ($users as $user) 
            {
               $output .= '<li class="name-box cursor" data-user_job_title="'.$user['fprofession'].'" data-user_id="'.$user['fuser_id'].'" data-user_name="'.$user['ffirst_name']. ' ' .$user['flast_name'].'" style="list-style-type: none;">'.$user['ffirst_name']. ' ' .$user['flast_name']. '-'. $user['fcurrent_employer'].'</li>'; 
			}

			$output .= '</ul>';
			
		 }
		 
		 
         $data = array(
            'users'		=>	$output,
         );	

		return $this->response->setJSON($data);

	}


	public function searchteams()
	{

		$keyword = $this->request->getPost('keyword');
		$team_model = new TeamModel();
		$teams = $team_model->like('fteam_name', $keyword)->where('fprivacy', 1)->findAll();
		$output = "";
		
		if(is_null($teams))
         {
             $output = '<li>None found</li>';
         }
         else
         {
            // $output .= "<ul>";
            foreach ($teams as $team) 
            {
               $output .= '<li class="team-name-box cursor" data-team_id="'.$team['fteam_id'].'" data-team_name="'.$team['fteam_name'].'">'.$team['fteam_name'].'</li>'; 
			}
			
			// $output .= "</ul>";
		 }
		 

         $data = array(
            'teams'		=>	$output,
         );	

		return $this->response->setJSON($data);

		//var_dump($teams);
	}


	public function searchprofessions()
	{
		$textbox_no = $this->request->getPost('textbox_no');

		if(empty($this->request->getPost('keyword')))
		{	
		 	$keyword = 'hfkhfhehfjhfklhfklheklfhklfhklehf';
		}
		else
		{	
			$keyword = $this->request->getPost('keyword');
	   	}

		 $pm = new ProfessionModel();
		 $professions = $pm->like('fname', $keyword)->select('fparent_id')->where('fparent_id !=', 0)->orderBy('fname', 'RANDOM')->findAll();
		 

		$related_professions = array();

		for($i = 0; $i < count($professions); $i++) 
		{

			$related_professions[] = $pm->where('fparent_id =', $professions[$i])->findAll();
		}

		//$rp = array_unique($related_professions);
		$rp = array_map("unserialize", array_unique(array_map("serialize", $related_professions)));
		
		if(empty($rp))
         {
			$output .= '<ul class="bg-white">';
			  $output .= '<ul class="w3-ul w3-card-2 alert alert-danger">';
			 	$output .= '<li class="w3-bar">None found</li>';
			 $output .= "</ul>";
			 $output .= "</ul>";
         }
         else
         {
            $output .= '<ul class="bg-white">';
		
			foreach($rp as $lists) 
            {
				$profession_name = $pm->where('frecno', $lists[0]['fparent_id'])->select('fname')->first();
				$output .= '<br>';
				$output .= '<ul class="w3-ul w3-card-2">';
				$output .= '<button class="w3-btn w3-black">'.$profession_name['fname'].'</button>';
					
					foreach( $lists as $list)
					{
						
						$output .= '<li class="w3-bar cursor profession-name-box_'.$textbox_no.'" data-parent_name="'.$profession_name['fname'].'" data-profession_id="'.$list['frecno'].'" data-profession_name="'.$list['fname'].'">'.$list['fname'].'</li>';
						
					} 

				
				$output .= "</ul>";
				
			}

			
			
			$output .= "</ul>";
		 }
		 

         $data = array(
            'professions' =>	$output,
         );	

		return $this->response->setJSON($data);

		//var_dump($teams);
	}


	public function getjobinvitemessages()
	{
		$job_id = $this->request->getPost('job_id');
		$user_id = session()->user_id;
		$job_message = new JobInviteMessageModel();
		$job_messages = $job_message->where('fjob_id', $job_id)->where('fuser_id', $user_id)->findAll();

		//var_dump($job_messages);
	}


	public function createeventaction()
	{
		 // var_dump($this->request->getPost());
		 if (! $this->validate([
			'ffrom' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Event Start is required!',
							]
						],
			'fto' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event End is required!',
				]
			],
			'ftitle' => [
				'rules' => 'required|trim',
				'errors' => [
						'required' => 'Event Title is required!',
				]
			],
			'fdescription' => [
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
		
		$invitees = $this->request->getPost('invitees');
		$extracted_invitees = implode(",", $invitees);

        $data = [
            
			'ffrom' => $this->request->getPost('from'),
			'fto' => $this->request->getPost('to'),
			'ftitle' => $this->request->getPost('title'),
			'finvitees' => $extracted_invitees,
			'fauthor' => $this->request->getPost('user_id'),
			'fevent_id' => 'EV' . time(),
			'fdescription' => $this->request->getPost('description'),
            
            ];
            
        //var_dump($data);

        $event = new EventModel();
		$event->insert($data);
		 $inserted = $event->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Event created successfully');
			return redirect()->to(base_url('user/events/'));
		}
		else
		{

			session()->setFlashdata('error', 'Event not created');
			return redirect()->to(base_url('user/events/'));
		}

	}

	public function events()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		} 
		
		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->where('factive', 1)->where('fuser_id !=', session()->user_id)->orderBy('ffirst_name', 'asc')->findAll();

		$user = new userModel();
        $data['user_details'] = $user->where('fuser_name', $username)->first();
		
		$event_model = new EventModel();
		$data['my_events'] = $event_model->where('fauthor', $user_id)->orderBy('frecno', 'desc')->findAll();

		$user = $user->where('fuser_name', $username)->first();
        $data['my_event_invites'] = $event_model->like('finvitees', $user['fuser_id'])->orderBy('frecno', 'desc')->findAll();
		
		return view('user/events', $data);
		//var_dump($data['my_events']);
	}

	public function event($id)
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
        }
		
		$event_model = new EventModel();
		$data['event_details'] = $event_model->where('fevent_id', $id)->orderBy('frecno', 'desc')->first();

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_id', $data['event_details']['fauthor'])->first();

		$data['first_name'] = $data['user_details']['ffirst_name'];

		$data['last_name'] = $data['user_details']['flast_name'];
		
		//var_dump($data['user_details']['ffirst_name']);
		
		return view('user/eventdetails', $data);
	}

	public function teams()
	{

		if(!session()->user_id)
        {
            return redirect()->to(base_url('/login'));
		}
		
		$user_id = session()->user_id;

		$username = session()->username;
		

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_id', $user_id)->first();

		$team = new TeamModel();
		//$data['my_teams'] = $team->where('fadmin', $user_id)->orderBy('frecno', 'desc')->findAll();
		$data['my_teams'] = $team->where('fuser_id', $user_id)->orderBy('tteams.frecno', 'desc')->join('tteam_members','tteam_members.fteam_id=tteams.fteam_id')->findAll();
		
		//var_dump($data['my_teams']);
		return view('user/teams', $data);
	}


	public function teammessages($team_id)
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$data['team_id'] = $team_id;

		$team = new TeamModel();
		$data['team_name'] = $team->where('fteam_id', $team_id)->select('fteam_name')->first();

		$user_id = session()->user_id;

		$username = session()->username;

		$user = new userModel();

		$data['users'] = $user->findAll();
		

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		

		//var_dump($data['team_name']);

		return view('user/teammessages', $data);
	}



	public function sendteammessageaction()
	{
		 // var_dump($this->request->getPost());
		 if (! $this->validate([
			'message' => [
						'rules'  => 'required|trim',
						'errors' => [
							'required' => 'Chat Message is required!',
							]
						],

		
		]))
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Message not sent"
			];
		}
		

        $data = [
            
			'fmessage' => $this->request->getPost('message'),
			'fsender_id' => $this->request->getPost('user_id'),
			'fsender_name' => $this->request->getPost('user_name'),
			'fsender_text_colour' => $this->request->getPost('user_text_colour'),
			'fteam_id' => $this->request->getPost('team_id'),
			'fmessage' => $this->request->getPost('message'),
            
            ];
            
        //var_dump($data);

        $team_message = new TeamMessageModel();
		$team_message->insert($data);
		 $inserted = $team_message->affectedRows();
		if($inserted > 0)
		{
			
			$response = [
				'success' => true,
				'data' => 'sent',
				'team_id' => $this->request->getPost('team_id'),
				'msg' => "Message sent successfully"
			];
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Message not sent"
			];	
		}

		return $this->response->setJSON($response);
	}


	public function getteammessages()
	{
		$team_id = $this->request->getPost('team_id');

		//$team_id = "hhhhhhhhh";
		
		$team_message = new TeamMessageModel();
		$messages = $team_message->where('fteam_id', $team_id)->orderBy('frecno', 'desc')->findAll();

		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($messages))
		{

			$output .= '
				<div class="card rounded-0">
					<div class="card-header font-weight-bold text-white p-2 rounded-0 " style="background: #5F9EA0;">Team Messages</div>
					<div class="card-body"  style="height: 25rem;">
						<p>No team messages yet. Get started by saying hi</p>
					</div>
				</div>		
					';

			$output .='<div class="row mt-2">
			<div class="col-sm-12">
				<form id="send_message_to_team">
					<div class="form-group">
					
						<input type="hidden" name="user_id" value="'.session()->user_id.'">
						<input type="hidden" name="user_name" value="'.session()->first_name . ' ' . session()->flast_name.'">
						<input type="hidden" name="user_text_colour" value="'.session()->user_text_color.'">  
						<input type="hidden" name="team_id" id="team_id" value="'.$team_id.'">
						
						<div class="input-group mb-3">
							<textarea name="message" class="form-control" cols="30" rows="1" required></textarea>
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit">SEND</button>
								<!-- <button type="submit" class="w3-button w3-black w3-right w3-round">SEND</button> -->
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>';
		}
		else
		{
			$output .='
			<div class="card rounded-0">
				<div class="card-header font-weight-bold text-white py-2 rounded-0" style="background: #5F9EA0;">Team Messages</div>';
                        
			$output .='<div class="card-body"  style="height: 25rem; overflow: auto; display:flex; flex-direction:column-reverse">';
		
			foreach($messages as $message)
			{

				$user = new UserModel();
				$user_details = $user->where('fuser_id', $message['fsender_id'])->first();

				$output .= '
					
						<div class="row mb-3">
							<div class="col-md-10">
								<div class="card text-white rounded-lg" >
									<div class="card-header text-dark py-1" style="background:'.$message['fsender_text_colour'].'">
									<img class="rounded-circle" src='.$url.'/public/profilepics/600/'.$user_details['fprofile_image'].' width="7%">
										&nbsp;'.$message['fsender_name'].'</div>
									<div class="card-body text-dark">'.$message['fmessage'].'</div>
								</div>
							</div>
							<div class="col">13:45</div>
						</div>
					 	
					 
				';
			}

			$output .='</div></div>';
			

			$output .='<div class="row mt-2">
			<div class="col-sm-12">
				<form id="send_message_to_team">
					<div class="form-group">
					
						<input type="hidden" name="user_id" value="'.session()->user_id.'">
						<input type="hidden" name="user_name" value="'.session()->first_name . ' ' . session()->flast_name.'">
						<input type="hidden" name="user_text_colour" value="'.session()->user_text_color.'">  
						<input type="hidden" name="team_id" id="team_id" value="'.$team_id.'">
						
						<div class="input-group mb-3">
							<textarea name="message" class="form-control" cols="30" rows="1" required></textarea>
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit">SEND</button>
								<!-- <button type="submit" class="w3-button w3-black w3-right w3-round">SEND</button> -->
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>';

		
		}

		$data = array(
			'team_messages' =>	$output,
		);	

		return $this->response->setJSON($data);
		

	}

	
	public function jobdash()
	{

		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
        }

		$user_id = session()->user_id;

		$username = session()->username;

		$user = new userModel();

		$data['users'] = $user->findAll();
		

		$user = new userModel();
		//$data['user_details'] = $user->where('fuser_name', $username)->first();

		$job_model = new JobInviteMessageModel();
		$data['jobs'] = $job_model->where('fuser_id', $user_id)->orderBy('frecno', 'desc')->findAll();
		
		//var_dump($user_id);

		return view('user/jobdash', $data);
	}

	public function jobdashmessages($job_id)
	{

		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
        }
		//echo $id;
		$user_id = session()->user_id;

		$username = session()->username;

		$user = new userModel();

		$data['users'] = $user->findAll();
		

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		$job_invite_message_model = new JobInviteMessageModel();
		$data['job_messages'] = $job_invite_message_model->where('fuser_id', $user_id)->where('fjob_id', $job_id)->findAll();

		$data['job_messages_count'] = $job_invite_message_model->where('fuser_id', $user_id)->where('fjob_id', $job_id)->countAllResults();

		//var_dump($data['job_messages']);
		return view('user/jobdashmessages', $data);
	}


	public function sendjobdashmessage()
	{
		
        $data = [

			'fjob_id' => $this->request->getPost('job_id'),
            'fjob_title' => $this->request->getPost('job_title'),
			'fmessage' => $this->request->getPost('message'),
			'fsender_id' => $this->request->getPost('user_id'),
			'freceiver_id' => $this->request->getPost('employer_id'),
			'fuser_id' => $this->request->getPost('user_id'),
			'femployer_id' => $this->request->getPost('employer_id'),
			'fsubject' => $this->request->getPost('subject'),
            
            ];
            
		//var_dump($data);
		$job_invite_message_model = new JobInviteMessageModel();
		$job_invite_message_model->insert($data);
		$inserted = $job_invite_message_model->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Message sent successfully');
			return redirect()->to(base_url('user/jobdashmessages/'. $this->request->getPost('job_id')));
		}
		else
		{
			session()->setFlashdata('error', 'Message not added');
			return redirect()->to(base_url('user/jobdashmessages/'. $this->request->getPost('job_id')));
		}
		
	}



	public function allaboutyou()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$user = new userModel();
        $data['user_details'] = $user->where('fuser_name', $username)->first();
		
		return view('user/allaboutyou');
	}

	public function jobs()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$user = new userModel();
        $data['user_details'] = $user->where('fuser_name', $username)->first();
		
		return view('user/jobs');
	}


	public function recommendations()
	{

		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		//$data['users'] = $user->findAll();

		$data['user_details'] = $user->where('fuser_name', $username)->first();

		$user_experience = new UserExperienceModel();
        $data['user_experiences'] = $user_experience->where('fuser_id', $user_id)->orderBy('frecno', 'desc')->findAll();

		
		$applicant_id = session()->applicant_id;

	

		$applicant = new UserModel();
		$data['user_details'] = $applicant->where('fuser_name', $username)->first();

		
		$recommendation = new RecommendationRequestModel();
		$data['sent_recommendation_requests'] = $recommendation->where('fsender_id', $user_id)->findAll();

		$data['sent_count'] = count($data['sent_recommendation_requests']);

		$recommendation = new RecommendationRequestModel();
		$data['received_recommendation_requests'] = $recommendation->where('freceiver_id', $user_id)->findAll();

		$data['received_count'] = count($data['received_recommendation_requests']);

		$recommendation = new RecommendationOfferModel();
		$data['sent_recommendation_offers'] = $recommendation->where('fsender_id', $user_id)->findAll();

		$recommendation = new RecommendationOfferModel();
		$data['received_recommendation_offers'] = $recommendation->where('freceiver_id', $user_id)->findAll();

		//var_dump($data['sent_count']);
		
		return view('user/recommendations', $data);
	}

	public function recommendationrequest()
	{
		//var_dump($this->request->getPost());
		if(!empty($this->request->getPost('receiver_id')))
		{
			$user = new userModel();
			$receiver = $user->where('fuser_id', $this->request->getPost('receiver_id'))->first();
			$sender = $user->where('fuser_id', session()->user_id)->first();
			$receiver['fprofession'];

			if(!empty($sender['fcurrent_employer']))
			{
				$company = $sender['fcurrent_employer'];
			}
			else
			{
				$company = "Company Not Set";
			}

			$data = [

				'freceiver_id' => $this->request->getPost('receiver_id'),
				'freceiver_name' => $receiver['ffirst_name']. ' ' . $receiver['flast_name'],
				'freceiver_job_title' => $receiver['fprofession'],
				'freceiver_pic' => $receiver['fprofile_image'],
				'fsender_id' => session()->user_id,
				'fsender_name' => session()->first_name . ' '. session()->last_name,
				'fsender_pic' => $sender['fprofile_image'],
				'fsender_job_title' => session()->job_title ,
				'fsender_company' => $company,
				'freceiver_company' => $receiver['fcurrent_employer'],
				'fmessage' => "Kindly recommend me",
				'fsubject' => "I Will Like To Request A Recommendation From You",
				'frelationship' => $this->request->getPost('relationship'),
				'frelationship-position' => $this->request->getPost('relationship-position'),
				];
				
			//var_dump($data);

			$recommendation_request_model = new RecommendationRequestModel();
			$recommendation_request_model->insert($data);
			$inserted = $recommendation_request_model->affectedRows();
			if($inserted > 0)
			{
				session()->setFlashdata('success', 'Recommendation Request sent successfully');
				return redirect()->to(base_url('user/recommendations'));
			}
			else
			{
				session()->setFlashdata('error', 'Request not sent');
				return redirect()->to(base_url('user/recommendations'));
			}
		}
		else
		{
			session()->setFlashdata('error', 'The receiver you selected is not a registered user on the platform. You can ask them to register a Kokoruns account');
			return redirect()->to(base_url('user/recommendations'));
		}
	}

	public function recommendationoffer()
	{
		
		if(!empty($this->request->getPost('receiver_id')))
		{
			//var_dump($this->request->getPost());
			$user = new userModel();
			$receiver = $user->where('fuser_id', $this->request->getPost('receiver_id'))->first();
			$receiver['fprofession'];

			$data = [

			'freceiver_id' => $this->request->getPost('receiver_id'),
			'freceiver_name' => $receiver['ffirst_name']. ' ' . $receiver['flast_name'],
			'freceiver_job_title' => $receiver['fprofession'],
			'fsender_id' => $this->request->getPost('sender_id'),
			'fsender_name' => $this->request->getPost('sender_name'),
			'fsender_job_title' => $this->request->getPost('sender_job_title'),
			'fmessage' => $this->request->getPost('message'),
			'fsubject' => "I Will Like To Recommend You",
			'frelationship' => $this->request->getPost('relationship'),
			'frelationship-position' => $this->request->getPost('relationship-position'),
            
		   ];
		   
		   //var_dump($data);
            
			//var_dump($user_details['fprofession']);
			$recommendation_offer_model = new RecommendationOfferModel();
			$recommendation_offer_model->insert($data);
			$inserted = $recommendation_offer_model->affectedRows();
			if($inserted > 0)
			{

				if(!empty($this->request->getPost('honesty')))
				{
					$honesty = $this->request->getPost('honesty');
					
				}
				else
				{
					$honesty = 0;
				}

				if(!empty($this->request->getPost('proactive')))
				{
					$proactive = $this->request->getPost('proactive');
					
				}
				else
				{
					$proactive = 0;
				}

				if(!empty($this->request->getPost('adaptable')))
				{
					$adaptable = $this->request->getPost('adaptable');
					
				}
				else
				{
					$adaptable = 0;
				}

				if(!empty($this->request->getPost('team_oriented')))
				{
					$team_oriented = $this->request->getPost('team_oriented');
					
				}
				else
				{
					$team_oriented = 0;
				}

				if(!empty($this->request->getPost('respectful')))
				{
					$respectful = $this->request->getPost('respectful');
					
				}
				else
				{
					$respectful = 0;
				}

				$data = [
					'freceiver_id' => $this->request->getPost('receiver_id'),
					'fsender_id' => $this->request->getPost('sender_id'),
					'fgeneral_impression' => $this->request->getPost('impression'),
					'fhonesty_integrity' => $honesty,
					'fproactive_dedicated' => $proactive,
					'fadaptable' => $adaptable,
					'fteam_oriented' => $team_oriented,
					'frespectable' => $respectful,
				];


				$ptm = new PersonalityTraitModel();
				$ptm->insert($data);


				session()->setFlashdata('success', 'Recommendation Offer sent successfully');
				return redirect()->to(base_url('user/recommendations'));

			}
			else
			{
				session()->setFlashdata('error', 'Offer not sent');
				return redirect()->to(base_url('user/recommendations'));
			}
		}
		else
		{
			session()->setFlashdata('error', 'The receiver you selected is not a registered user on the platform. You can ask them to register a Kokoruns account');
			return redirect()->to(base_url('user/recommendations'));
		}
	}

	public function RecommendationRequestDetails($id)
	{
		$recommendation = new RecommendationRequestModel();
		$data['recommendation_request'] = $recommendation->where('frecno', $id)->first();
		return view('user/recommendation_request_details', $data);
	}

	public function RecommendationRequestAccept($id)
	{
		$recommendation = new RecommendationRequestModel();
		$data['recommendation_request'] = $recommendation->where('frecno', $id)->first();
		return view('user/recommendation_request_details2', $data);
	}

	public function RecommendationRequestAcceptAction()
	{
		//var_dump($this->request->getPost());
		
		
			$data = [
				'freceiver_id' => $this->request->getPost('receiver_id'),
				'fsender_id' => session()->user_id,
				'fgeneral_impression' => $this->request->getPost('first_impression'),
				'fhonesty_integrity' => $this->request->getPost('integrity'),
				'fpunctuality' => $this->request->getPost('punctuality'),
	
			];


			//var_dump($data);


		 $pt = new PersonalityTraitModel();
		 $pt->insert($data);

		 $inserted = $pt->affectedRows();
		if($inserted > 0)
		{
			$data = [
			'faccepted' => 1,
			];

			$recommendation = new RecommendationRequestModel();
			$updated = $recommendation->update($this->request->getPost('id'), $data);


			session()->setFlashdata('success', 'Recommended user successfully');
			return redirect()->to(base_url('user/recommendations'));
		}
		else
		{
			session()->setFlashdata('error', 'Error occurred!');
			return redirect()->to(base_url('user/recommendations'));
		}
	}


	public function recommendationrequestreject($id)
	{
		$data = [
			'faccepted' => -1,
			];

			$recommendation = new RecommendationRequestModel();
			$updated = $recommendation->update($id, $data);
			if($updated > 0)
			{
				session()->setFlashdata('success', 'Recommendation Request rejected successfully');
				return redirect()->to(base_url('user/recommendations'));
			}
			else
			{
				session()->setFlashdata('error', 'Error occurred!');
				return redirect()->to(base_url('user/recommendations'));
			}

	}

	public function cancelsentrequest()
	{
		$id = $this->request->getPost('id');
		$rrm = new RecommendationRequestModel();
        $rrm->where('frecno', $id)->delete();
		$deleted = $rrm->affectedRows();
		if($deleted > 0)
		{
			session()->setFlashdata('success', 'Request deleted successfully');
			return redirect()->to(base_url('user/recommendations'));
		}
		else
		{
			session()->setFlashdata('error', 'Request not deleted. Try Again');
			return redirect()->to(base_url('user/recommendations'));
		}
	}


	public function RecommendationOfferDetails($id)
	{
		$recommendation = new RecommendationOfferModel();
		$data['recommendation_offer'] = $recommendation->where('frecno', $id)->first();
		return view('user/recommendation_offer_details', $data);
	}

	public function RecommendationOfferAccept($id)
	{
		$recommendation = new RecommendationRequestModel();
		$data['recommendation_request'] = $recommendation->where('frecno', $id)->first();
		return view('user/recommendation_request_details2', $data);
	}

	public function RecommendationOfferAcceptAction($id)
	{
		$data = [
		  'faccepted' => 1,
		];

		$recommendation = new RecommendationOfferModel();
		$updated = $recommendation->update($id, $data);

		if($updated > 0)
		{
			session()->setFlashdata('success', 'Recommendation Offer accepted successfully');
			return redirect()->to(base_url('user/recommendations'));
		}
		else
		{
			session()->setFlashdata('error', 'Error occurred!');
			return redirect()->to(base_url('user/recommendations'));
		}
	}


	public function recommendationofferreject($id)
	{
			$data = [
				'faccepted' => -1,
			];

			$recommendation = new RecommendationOfferModel();
			$updated = $recommendation->update($id, $data);
			if($updated > 0)
			{
				session()->setFlashdata('success', 'Recommendation Offer rejected successfully');
				return redirect()->to(base_url('user/recommendations'));
			}
			else
			{
				session()->setFlashdata('error', 'Error occurred!');
				return redirect()->to(base_url('user/recommendations'));
			}

	}

	public function recommendationacceptancerevoke($id)
	{
			$data = [
				'faccepted' => -2,
			];

			$recommendation = new RecommendationOfferModel();
			$updated = $recommendation->update($id, $data);
			if($updated > 0)
			{
				session()->setFlashdata('success', 'Acceptance revoked successfully');
				return redirect()->to(base_url('user/recommendations'));
			}
			else
			{
				session()->setFlashdata('error', 'Error occurred!');
				return redirect()->to(base_url('user/recommendations'));
			}

	}


	public function myjobs()
	{

		
		$uj = new UserJobModel();
		$data['my_jobs'] = $uj->where('fuser_id', session()->user_id)->orderBy('frecno', 'DESC')->findAll();
		//var_dump($data);
		return view('user/myjobs', $data);
		
	}


	public function createmyjobaction()
	{
		// var_dump($this->request->getPost());
		// if (! $this->validate([
		// 	'job_title' => [
		// 		'rules' => 'required|trim',
		// 		'errors' => [
		// 				'required' => 'Job Title is required!',
		// 		]
		// 	],
		// 	'company' => [
		// 				'rules'  => 'required|trim',
		// 				'errors' => [
		// 					'required' => 'Company is required!',
		// 					]
		// 				],
		// 	'state' => [
		// 		'rules' => 'required|trim',
		// 		'errors' => [
		// 				'required' => 'State is required!',
		// 		]
		// 	],
		// 	'lga' => [
		// 				'rules'  => 'required|trim',
		// 				'errors' => [
		// 					'required' => 'End Year is required!',
		// 					]
		// 				],
        // 	'employment_type' => [
		// 		'rules' => 'required|trim',
		// 		'errors' => [
		// 				'required' => 'Position is required!',
						
		// 		]
        //     ]
		// ]))
		// {
		// 	session()->setFlashdata('error', 'Job not created');
		// 	return redirect()->to(base_url('user/myjobs'));
		// }


		$languages = array_filter($this->request->getPost('languages'));
		$extracted_languages = implode(",", $languages);

		$skills = array_filter($this->request->getPost('skills'));
		$extracted_skills = implode(",", $skills);


		$data = [

			'fjob_title' => $this->request->getPost('job_title'),
			'fjob_id' => 'UJ'. time(),
			'fjob_description' => $this->request->getPost('job_description'),
			'fsalary' => $this->request->getPost('salary'),
			'flocation' => $this->request->getPost('lga'). ','. ' '. $this->request->getPost('state'),
			'femployment_type' => $this->request->getPost('employment_type'),
			'flanguages' => $extracted_languages,
			'fskills' => $extracted_skills,
			'fuser_id' => session()->user_id
            
		   ];
		   
		  //var_dump($data);

		  $ujm = new UserJobModel();
		  $ujm->insert($data);
		  $inserted = $ujm->affectedRows();
		  if($inserted > 0)
		  {
			 
			session()->setFlashdata('success', 'Job Created Successfully');
			return redirect()->to(base_url('/user/myjobs'));
		  }
		  else
		  {
			session()->setFlashdata('error', 'Error in creating job! Try again');
			return redirect()->to(base_url('/user/myjobs'));
			  
		  }
  

	}

	public function settings()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$user = new userModel();
        $data['user_details'] = $user->where('fuser_name', $username)->first();
		
		return view('user/settings', $data);
	}


	public function search()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		return view('user/search');
	}

	public function messagesinbox()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		$message = new MessageModel();
		$data['inbox'] = $message->where('freceiver_id', $user_id)->groupBy('fsender_id')->orderBy('frecno', 'DESC')->findAll();
		$data['inbox_count'] = count($data['inbox']);

		$data['sent'] = $message->where('fsender_id', $user_id)->findAll();
		$data['sent_count'] = count($data['sent']);

		
		//var_dump($data['inbox']);
		return view('user/messagesinbox', $data);
	}


	public function messagessent()
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}

		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$user = new userModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		$user_message = new MessageModel();
		$data['inbox'] = $user_message->where('freceiver_id', $user_id)->findAll();
		$data['inbox_count'] = count($data['inbox']);

		$data['sent'] = $user_message->where('fsender_id', $user_id)->findAll();
		$data['sent_count'] = count($data['sent']);

		
		//var_dump($data['inbox']);
		return view('user/messagessent', $data);
	}


	public function message($message_id)
	{
		if(!session()->username)
        {
            return redirect()->to(base_url('/login'));
		}
		
		$username = session()->username;

		$user_id = session()->user_id;

		$user = new userModel();
		$data['users'] = $user->findAll();

		$data['user_details'] = $user->where('fuser_name', $username)->first();

		//var_dump($this->request->getPost());
	
		$user_message = new MessageModel();
		$data['message'] = $user_message->where('fmessage_id', $message_id)->first();

		$data['inbox'] = $user_message->where('freceiver_id', $user_id)->findAll();
		$data['inbox_count'] = count($data['inbox']);

		$data['sent'] = $user_message->where('fsender_id', $user_id)->findAll();
		$data['sent_count'] = count($data['sent']);

		//var_dump($data['message']);

		return view('user/messagedetails', $data);
	}


	public function createmessageaction()
	{
		$username = session()->username;

		$user_id = session()->user_id;


		$user = new userModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		//var_dump($this->request->getPost());
	
		$receiver = $user->where('fuser_id', $this->request->getPost('receiver_id'))->first();
		

		 $data = [

			'freceiver_id' => $this->request->getPost('receiver_id'),
			'freceiver_name' => $this->request->getPost('receiver_name'),
			'fsender_id' => session()->user_id,
			'fsender_name' => session()->first_name . ' ' .session()->last_name,
			'fcontent' => $this->request->getPost('content'),
	
            
		   ];
		   
		  //var_dump($data);
            
		
		$user_message = new MessageModel();
		$user_message->insert($data);
		$inserted = $user_message->affectedRows();
		if($inserted > 0)
		{
			$response = [
				'success' => true,
				'data' => 'saved',
				'msg' => "Inserted successfully"
			];
			
		}
		else
		{
			$response = [
				'success' => false,
				'data' => 'failed',
				'msg' => "Not inserted"
			];	
			
		}

		return $this->response->setJSON($response);
	}



	public function replymessageaction()
	{
		// var_dump($this->request->getPost());
		$username = session()->username;

		$user_id = session()->user_id;

		$user = new UserModel();
		$data['users'] = $user->findAll();

		$user = new UserModel();
		$data['user_details'] = $user->where('fuser_name', $username)->first();

		$data = [

			'freceiver_id' => $this->request->getPost('receiver_id'),
			'freceiver_name' => $this->request->getPost('receiver_name'),
			'fsender_id' => $this->request->getPost('sender_id'),
			'fsender_name' => $this->request->getPost('sender_name'),
			'fmessage_id' => 'MG' . time(),
			'fparent_message_id' => $this->request->getPost('parent_message_id'),
			'fcontent' => $this->request->getPost('content'),
			'fsubject' => $this->request->getPost('subject'),
            
		   ];
		   
		  // var_dump($data);

		$user_message = new MessageModel();
		$user_message->insert($data);
		$inserted = $user_message->affectedRows();
		if($inserted > 0)
		{
			session()->setFlashdata('success', 'Message sent successfully');
			return redirect()->to(base_url('user/messages/inbox'));
		}
		else
		{
			session()->setFlashdata('error', 'Message not sent');
			return redirect()->to(base_url('user/messages/inbox'));
		}
	}

	public function getchatmessages()
	{

		$sender_id = $this->request->getPost('sender_id');
		$receiver_id = $this->request->getPost('receiver_id'); 

		$message = new MessageModel();
		$chat_messages = $message->where('freceiver_id', $receiver_id)->orWhere('fsender_id', $sender_id)->findAll();

		$user = new UserModel();
		$user_details = $user->where('fuser_id', $sender_id)->first();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($chat_messages))
		{
			$output .= '

					<p></p>
						
				';
		}
		else
		{
			$url = base_url();
			$output .=" 
				<div class='chat-window'>
				<div class='chat-window-header mb-4'>
               
					<p class='px-3 pt-2'> <img src='".$url."/public/profilepics/".$user_details['fprofile_image']."'>
	
					<span class='sender_name' style='font-size:20px;'></span>
					
					<span class='float-right'>Last Seen: 22/03/2021</span>
	
					</p>
				 </div>";

			$output .='<div class="chat-area">';
			
		
			foreach($chat_messages as $message)
			{

				// if($message['fsender_id'] == session()->user_id)
				// {
		
					
				// }
				// else
				// {
				// 	$output .= "<div class='receiver'>".$message['fcontent']."<br><span>13:45&nbsp;Sept 09</span></div>";
				// }
	

				

				$output .= '

					<div class="sender">Hey Madara, Good Afternoon...Hope all is well. Great kokoruns page youve got there. Would greatly appreciate a recommendation though.<br>
					<span>13:45&nbsp;Sept 09</span>
					</div>

					';
	
	
			}

				
			$output .="</div>";

			
		}
		
	

		$data = array(
			'chat_messages' =>	$output,
		);	

		return $this->response->setJSON($data);


	}


	public function getchatmessages2()
	{

		$sender_id = $this->request->getPost('sender_id');
		$receiver_id = $this->request->getPost('receiver_id'); 

		$message = new MessageModel();
		$chat_messages = $message->where('freceiver_id', $receiver_id)->orWhere('fsender_id', $sender_id)->findAll();

		$user = new UserModel();
		$user_details = $user->where('fuser_id', $sender_id)->first();


		$output = "";
		$total_item = 0;
		$url = site_url();

		if(empty($chat_messages))
		{
			$output .= '

					<p></p>
						
				';
		}
		else
		{
			$url = base_url();
			
		
			foreach($chat_messages as $message)
			{

				// if($message['fsender_id'] == session()->user_id)
				// {
		
					
				// }
				// else
				// {
				// 	$output .= "<div class='receiver'>".$message['fcontent']."<br><span>13:45&nbsp;Sept 09</span></div>";
				// }
	

				

				$output .= '

					<p class="bg-light border p-3 rounded-lg">
						<span style="color:'.$message['fsender_text_colour'].'"><span class="float-right">'.date(' H:i', strtotime($message['created_at'])).'</span>
						<br>'.$message['fcontent'].'
						</p>

					';
	
	
			}

			
		}
		
	

		$data = array(
			'chat_messages' =>	$output,
		);	

		return $this->response->setJSON($data);


	}

	public function getuserdetails()
	{
		$user_id = $this->request->getPost('user_id'); 

		$user = new userModel();
		$user_details = $user->where('fuser_id', $user_id)->first();

		//var_dump($this->request->getPost())


	
		$data = array(
			'user_details' =>	$user_details,
		);	

		return $this->response->setJSON($data);

			
	}
	
	
	public function searchapplicants()
	{
		$keyword = $this->request->getPost('keyword');
 		$applicant_model = new UserModel();
 		// $users = $applicant_model->where('fuser_id !=', session()->user_id)->where('factive', 1)->like('ffirst_name', $keyword)->findAll();
		 $users = $applicant_model->where('factive', 1)->like('ffirst_name', $keyword)->findAll();
		$output = "";
		
		if(empty($users))
         {
             $output = '';
         }
         else
         {
			$output = '<div class="search-results-header">Search Results</div>';
		
            foreach ($users as $user) 
            {
                $url = base_url();
				$output .= '
					<div class="searched-result">
							<div class="from"><img src="'.$url.'/public/profilepics/600/'.$user["fprofile_image"].'" width="20%">'.$user["ffirst_name"].' '.$user["flast_name"].'</div>
							<div class="job-description">'.$user["fprofession"].' at <a href="job-description-link" class="">'.$user["fcurrent_employer"].'</a></div>   
							<div class="view-request-button-div"><a href="'.$url.'/'. $user["fuser_id"].'"><button class="view-request-button">View Profile</button></a></div>
							<div class="recommend-button-div">
							
							
									<button type="submit" class="recommend-button request_send" data-receiver_id="'.$user["fuser_id"].'">Send Request</button>
							
							</div> 
					</div>
				'; 
			}

		
		 }
		 
		 
         $data = array(
            'users'		=>	$output,
         );	

		return $this->response->setJSON($data);

	}


	public function searchapplicants_teams()
	{
		$keyword = $this->request->getPost('keyword');
 		$applicant_model = new UserModel();
 		// $users = $applicant_model->where('fuser_id !=', session()->user_id)->where('factive', 1)->like('ffirst_name', $keyword)->findAll();
		 $users = $applicant_model->where('factive', 1)->like('ffirst_name', $keyword)->findAll();
		$output = "";
		
		if(empty($users))
         {
             $output = '';
         }
         else
         {
		
            foreach ($users as $user) 
            {
                $url = base_url();
				$output .= '
					<div class="row my-2 no-gutters user_names cursor" data-user_id="'.$user["fuser_id"].'" data-full_name="'.$user["ffirst_name"].' '.$user["flast_name"].'">
						<div class="col-md-2 align-middle">
							<img src="'.$url.'/public/profilepics/600/'.$user["fprofile_image"].'" class="rounded-circle" width="60%">
						</div>
						<div class="col-md-7 text-left align-middle">
							'.$user["ffirst_name"].' '.$user["flast_name"].'
						</div>
						<div class="col-md-3 align-middle">
							<a href="'.$url.'/'. $user["fuser_id"].'" target="_blank"><span class="btn btn-primary btn-sm py-0" style="border-radius:3rem;">View</span></a>
						</div>
					</div>
				'; 
			}

		
		 }
		 
		 
         $data = array(
            'users'		=>	$output,
         );	

		return $this->response->setJSON($data);

	}


    public function logout()
	{
		session()->destroy();
		return redirect()->to(base_url('/'));
	}

}
	