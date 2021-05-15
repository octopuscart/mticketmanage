<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ob_start();

class MovieEvent extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('User_model');
        $this->load->library('session');
        $this->user_id = $this->session->userdata('logged_in')['login_id'];
        $this->user_type = $this->session->logged_in['user_type'];
    }

    public function index() {
        redirect("Media/images");
    }

    ///Category management 
    public function images() {
        $this->db->select('id, image');
        $query = $this->db->get('media');
        $image_list = $query->result();
        $data['image_list'] = $image_list;
        if (isset($_POST['submit'])) {
            $datetime = date("F j, Y, g:i a");
            //Check whether user upload picture
            if (!empty($_FILES['picture']['name'])) {
                $config['upload_path'] = 'assets/media';
                $config['allowed_types'] = '*';
                $temp1 = rand(100, 1000000);
                $ext1 = explode('.', $_FILES['picture']['name']);
                $ext = strtolower(end($ext1));
                $file_newname = $temp1 . "1." . $ext;
                $config['file_name'] = $file_newname;
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('picture')) {
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                } else {
                    $picture = '';
                }
            } else {
                $picture = '';
            }
            $user_id = $this->session->userdata('logged_in')['login_id'];
            $post_data = array(
                'image' => $file_newname,
                'datetime' => date('Y-m-d H:M:S'),
                'user_id' => $user_id
            );

            $this->db->insert('media', $post_data);
            $last_id = $this->db->insert_id();


            //
            //Storing insertion status message.
            if ($last_id) {
                redirect('Media/images');
                $this->session->set_flashdata('success_msg', 'User data have been added successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Some problems occured, please try again.');
            }
        }
        $this->load->view('CMS/Media/image', $data);
    }

    public function New() {
        $data = array();
        $tag_data = array();
        $tags = [];

        $data['tags'] = $tags;


        $data['categories'] = array();

        $config['upload_path'] = 'assets/movies';
        $config['allowed_types'] = '*';
        if (isset($_POST['submit_data'])) {
            $picture = '';

            if (!empty($_FILES['picture']['name'])) {
                $temp1 = rand(100, 1000000);
                $config['overwrite'] = TRUE;
                $ext1 = explode('.', $_FILES['picture']['name']);
                $ext = strtolower(end($ext1));
                $file_newname = $temp1 . "$userid." . $ext;
                $picture = $file_newname;
                $config['file_name'] = $file_newname;
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('picture')) {
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                } else {
                    $picture = '';
                }
            }



            $tags = implode(", ", $this->input->post("tags"));

            $blogArray = array(
                "image" => $picture,
                "tag" => $tags,
                "category_id" => $this->input->post("category_id"),
                "title" => $this->input->post("title"),
                "description" => $this->input->post("description"),
            );

            $this->Curd_model->insert('style_tips', $blogArray);
            redirect("CMS/newBlog");
        }

        $this->load->view('Movie/new_event', $data);
    }

    public function eventList() {
        $this->db->order_by('id desc');
        $query = $this->db->get('movie_show');
        $image_list = $query->result_array();
        $data['eventlist'] = $image_list;
        $this->load->view('Movie/list', $data);
    }

}
