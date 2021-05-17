<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movie extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    function movieList() {
        $this->db->select("*");
        $this->db->where('event_date>=', date("Y-m-d"));
        $this->db->group_by("movie_id");
        $query = $this->db->get('movie_event');
        $movies = array();
        $movieevents = $query->result_array();
        foreach ($movieevents as $mekey => $mevalue) {
            $movieid = $mevalue['movie_id'];
            $this->db->select("*, description as about");
            $this->db->where('id', $movieid);
            $query = $this->db->get('movie_show');
            $movieobj = $query->row_array();
            $movieobj["image"] = MOVIEPOSTER . $movieobj["image"];
            $movies[$movieid] = $movieobj;
        }
        return $movies;
    }

    function movieevent() {
        $this->db->select("*");
        $this->db->where('event_date>=', date("Y-m-d"));
        $this->db->group_by("movie_id");
        $query = $this->db->get('movie_event');
        $movieevents = $query->result_array();
        return $movieevents;
    }

    function theaters() {
        $this->db->select("*");
        $this->db->where('event_date>=', date("Y-m-d"));
        $this->db->group_by("movie_id");
        $query = $this->db->get('movie_event');
        $movieevents = $query->result_array();
        $listoftheaters = array();
        foreach ($movieevents as $mekey => $mevalue) {
            $theaterid = $mevalue['theater_id'];
            $this->db->select("*");
            $this->db->where('id', $theaterid);
            $query = $this->db->get('movie_theater');
            $movietheater = $query->row_array();
            $listoftheaters[$theaterid] = array(
                "title" => $movietheater["title"],
                "timing" => [$mevalue["event_time"]],
                "layout" => $movietheater["layout"],
                "active" => 1,
            );
        }


        return $listoftheaters;
    }

    function theaterTemplate($theater_id) {
        $this->db->where('theater_id', $theater_id);
        $query = $this->db->get('movie_theater_template');
        $theater_array = $query->result_array();
        $theater_array_adata = array();
        foreach ($theater_array as $thkey => $thvalue) {
            $temparray = $thvalue;
            $this->db->where('template_id', $thvalue["id"]);
            $query = $this->db->get('movie_theater_template_class');
            $theater_array_class = $query->result_array();
            $temparray["class_price"] = $theater_array_class;
            $theater_array_adata[$thvalue["id"]] = $temparray;
        }
        return $theater_array_adata;
    }

    function theaterTemplateApi($theater_id) {
        $theater_data = $this->theaterTemplate($theater_id);
        $theater_array_adata = array();
        foreach ($theater_data as $thkey => $thvalue) {

            $temparray = array();
            $calssprice = [];
            $classarray = $thvalue["class_price"];
            $temparray["title"] = $thvalue["title"];

            foreach ($classarray as $key => $value) {
                $clprice = strtoupper($value["class_name"]) . " - " . GLOBAL_CURRENCY . "" . $value["class_price"];
                array_push($calssprice, $clprice);
            }
            $classpricedata = implode(", ", $calssprice);
            $temparray["class_price"] = $classpricedata;
            $theater_array_adata[$thvalue["id"]] = $temparray;
        }
        return $theater_array_adata;
    }

    function bookedSeatById($booking_id) {
        $this->db->select("*");
        $this->db->where('movie_ticket_booking_id', $booking_id);
        $query = $this->db->get('movie_ticket');
        $moviebooking = $query->result_array();
        return $moviebooking;
    }

    function getSelectedSeats($theater_id, $movie_id, $select_date, $select_time) {
        $this->db->select("*");
        $this->db->where('theater_id', $theater_id);
        $this->db->where('movie_id', $movie_id);
        $this->db->where('select_date', $select_date);
        $this->db->where('select_time', $select_time);
        $query = $this->db->get('movie_ticket_booking');
        $moviebooking = $query->result_array();
        $seats = [];
        foreach ($moviebooking as $mbkey => $mbvalue) {
            $bookingid = $mbvalue['id'];
            $booking_seat = $this->bookedSeatById($bookingid);
            foreach ($booking_seat as $skey => $svalue) {
                array_push($seats, $svalue);
            }
        }
        return $seats;
    }

    function movieInforamtion($movie_id) {
        $this->db->select("*, description as about");
        $this->db->where('id', $movie_id);
        $query = $this->db->get('movie_show');
        $movieobj = $query->row_array();
        $movieobj["image"] = MOVIEPOSTER . $movieobj["image"];
        return $movieobj;
    }

    function theaterInformation($theater_id) {
        $this->db->where('id', $theater_id);
        $query = $this->db->get('movie_theater');
        $theaterobj = $query->row_array();
        return $theaterobj;
    }

    function getEventsList($cdate = "") {
        if ($cdate == "") {
            $cdate = date("Y-m-d");
        }
        $this->db->group_by("movie_id");
        $this->db->where('event_date>=', $cdate);
        $query = $this->db->get('movie_event');
        $event_list = $query->result_array();
        $eventlistarray = array();
        foreach ($event_list as $ekey => $evalue) {
            $temparray = array();



            $theater_id = $evalue["theater_id"];
            $movie_id = $evalue["movie_id"];
            $template_id = $evalue["theater_template_id"];

            $theaterobj = $this->theaterInformation($theater_id);
            $temparray["theaterobj"] = $theaterobj;


            $temparray["template"] = $this->theaterTemplate($theater_id)[$evalue["theater_template_id"]];

            $movieobj = $this->movieInforamtion($movie_id);
            $temparray["movie"] = $movieobj;


            $this->db->where('movie_id', $movie_id);
            $this->db->where('theater_id', $theater_id);
            $this->db->where('theater_template_id', $template_id);
            $query = $this->db->get('movie_event');
            $theaterobj = $query->result_array();
            $temparray["event_datetime"] = $theaterobj;
            
            
            array_push($eventlistarray, $temparray);
        }
        return $eventlistarray;
    }

    function booking_mail($order_id, $subject = "") {
        setlocale(LC_MONETARY, 'en_US');
        $checkcode = REPORT_MODE;


        $this->db->where('booking_id', $bookingid);
        $query = $this->db->get('movie_ticket_booking');
        $bookingobj = $query->row_array();
        $movies = $this->movieList();
        $data['movieobj'] = $movies[$bookingobj['movie_id']];

        $theaters = $this->Movie->theaters();

        $data['theater'] = $theaters[$bookingobj['theater_id']];
        $data['booking'] = $bookingobj;
        $data['seats'] = $this->Movie->bookedSeatById($bookingobj['id']);
        $this->load->view('movie/ticketviewemail', $data);

        $emailsender = email_sender;
        $sendername = email_sender_name;
        $email_bcc = email_bcc;

        if ($order_details) {
            $order_no = $order_details['order_data']->order_no;
            $this->email->set_newline("\r\n");
            $this->email->from(email_bcc, $sendername);
            $this->email->to($order_details['order_data']->email);
            $this->email->bcc(email_bcc);

            $this->db->insert('user_order_log', $orderlog);

            $subject = "Your Movie Ticket(s) for";
            $this->email->subject($subject);

            $this->db->where('booking_id', $bookingid);
            $query = $this->db->get('movie_ticket_booking');
            $bookingobj = $query->row_array();
            $data['booking'] = $bookingobj;
            $data['seats'] = $this->bookedSeatById($bookingobj['id']);
            $this->load->view('movie/ticketview');

            if ($checkcode) {
                $this->email->message($this->load->view('Email/order_mail', $order_details, true));
                $this->email->print_debugger();
                $send = $this->email->send();
                if ($send) {
                    echo json_encode("send");
                } else {
                    $error = $this->email->print_debugger(array('headers'));
                    echo json_encode($error);
                }
            } else {
                echo $this->load->view('Email/order_mail', $order_details, true);
            }
        }
    }

}
