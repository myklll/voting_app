<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	    
	    if($this->session->userdata('logged_in') == FALSE){
    	    $this->load->view('app_login');
	    }else{
    	    $this->load->model('Entry_model');
    	    $this->load->model('Category_model');
    	    $data = array();
    	    $categories = $this->Category_model->query_all_categories();
    	    
    	    foreach($categories as $category){
	       
        	    //retrun all of the entries in a given category
        	    $entries = $this->Entry_model->query_all_entries_by_cat($category->id);
    	    
        	    $cat_entries = array();
        	    $cat_entries[] = $category->cat_title;
        	    $cat_entries[] = $category->machine_name;
    	    
        	    foreach($entries as $entry){
        	       $cat_entries[] = $this->Entry_model->query_entries_by_id($entry);
        	   }
        	       $data[] = $cat_entries;
        	       }
        	       $this->load->view('app_view', array('data' => $data));    	    
    	    
	    }
	}
	public function auth()
	{
	   if($this->input->post()){
	   
	       $post = $this->input->post();
	       unset($post['submit']);
	       
	       //!TODO Move this into a user model and check if user is active
	       $query = $this->db->get_where('users', array('username' => $post['email']));   
	       $data = $query->result();
	   
	       if($data != NULL){
	           //User is active so we will log her in
    	       $this->session->set_userdata(array('logged_in'=>TRUE));
    	       redirect('/', 'refresh');
    	   }else{
    	       //User is inactive so we will send back a message saying she already voted or does not have access to system
        	   redirect('/', 'refresh');
           }	
	   
	   }else{
    	   redirect('/', 'refresh');
	   }

	}
	public function vote()
	{
    	if($this->input->post()){
        	
        	$data = $this->input->post();      	
        	
        	//insert votes into votes table
        	foreach($data as $key=>$value)
        	{	
            	$query = "INSERT INTO votes (catmname, entid) VALUES ('$key', '$value[0]')";
            	mysql_query($query);
        	}
        	
        	//set user to inactive
        	
        	$this->load->view('thank_you', array('data' => $data));
        	
    	}else{
        	redirect('/', 'refresh');
    	}
	}
}

/* End of file app.php */
/* Location: ./application/controllers/app.php */