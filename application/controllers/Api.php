<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Movie');
        $this->load->library('session');
        $this->checklogin = $this->session->userdata('logged_in');
        $this->user_id = $this->session->userdata('logged_in')['login_id'];
    }

    public function index() {
        $this->load->view('welcome_message');
    }

    function updateCurd_post() {
        $fieldname = $this->post('name');
        $value = $this->post('value');
        $pk_id = $this->post('pk');
        $tablename = $this->post('tablename');
        if ($this->checklogin) {
            $data = array($fieldname => $value);
            $this->db->set($data);
            $this->db->where("id", $pk_id);
            $this->db->update($tablename, $data);
        }
    }

    function createRange($start, $end, $total, $gapes, $row, $booked, $reserve, $gap) {
        //A Availble, B:Booked, R Reserved
        $temp = array();
        $temprow = array();
        for ($i = $start; $i <= $end; $i++) {
            $trow = $row . "-" . $i;
            $temp[$trow] = $trow;
        }
        for ($i = 1; $i <= $total; $i++) {
            $trow = $row . "-" . $i;
            if (isset($temp[$trow])) {
                $temprow[$trow] = "A";
            } else {
                $temprow[$trow] = "";
            }
            if (isset($booked[$trow])) {
                $temprow[$trow] = "B";
            }
            if (isset($reserve[$trow])) {
                $temprow[$trow] = "R";
            }
            if (isset($gap[$i])) {
                $trow = $row . "-" . $i . "_" . $gap[$i];
                $temprow[$trow] = "";
            }
        }
        foreach ($gapes as $key => $value) {
            $trow = $row . "-" . $value;
            $temprow[$trow] = "";
        }

        return $temprow;
    }

    function getTheaterTemplate_get($theater_id) {
        $templatelist = $this->Movie->theaterTemplate($theater_id);
        $this->response($templatelist);
    }

    function getBookedSheats($inputarray, $booking_type) {
        $event_id = $inputarray['event_id'];
        $seats = $this->Movie->getSelectedSeats($event_id, $booking_type);
        $resurve = array();
        foreach ($seats as $key => $value) {
            $resurve[$value['seat']] = "";
        }
        return $resurve;
    }

    function getLayout_GH_V_WALK_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $classprice = [];
        $paid = array();
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_V_WALK($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }

    function getLayoutGrandOcean_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayoutGrandOcean($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }

    function getLayout_GH_HSE4_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE4($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }
    
    function getLayout_GH_HSE4B_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE4B($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }
    
    function getLayout_GH_HSE4C_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE4C($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }
    
    function getLayout_GH_HSE4D_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE4D($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }

    function getLayout_GH_HSE3_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE3($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }
    function getLayout_GH_HSE3B_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        $wheelchair = "";
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
            $wheelchair = $templateobj["wheelchair"];
        }
        $layout = $this->Movie->getLayout_GH_HSE3B($booked, $reserved, $paid, $classprice);
        $layout["wheelchair"] = $this->Movie->wheelchair($wheelchair);
      
        $this->response($layout);
    }


    function getLayout_GH_HSE1_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE1($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }
    function getLayout_GH_HSE1B_get($configurable = 0) {
        $booked = array();
        $reserved = array();
        $paid = array();
        $classprice = [];
        if ($configurable) {
            $booked = $this->getBookedSheats($this->get(), "Reserved");
            $paid = $this->getBookedSheats($this->get(), "Paid");
            $templateobj = $this->Movie->theaterTemplateSingle($this->get("template_id"));
            $classprice = $templateobj["class_price"];
            $reserved = $templateobj["reserve_seats"];
        }
        $layout = $this->Movie->getLayout_GH_HSE1B($booked, $reserved, $paid, $classprice);
        $this->response($layout);
    }

}

?>