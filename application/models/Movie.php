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

    function movieevent_active() {
        $time30 = "";
        $this->db->select("*");

        $this->db->order_by("event_date desc");
        $query = $this->db->get('movie_event');
        $movieevents = $query->result_array();
        $returndata = [];
        foreach ($movieevents as $key => $value) {
            $theater = $this->theaterInformation($value["theater_id"]);
            $movie = $this->movieInforamtion($value["movie_id"]);
            if ($movie) {
                $value["movie"] = $movie;
                $value["theater"] = $theater;
                array_push($returndata, $value);
            }
        }
        return $returndata;
    }

    function movieevent($status = "") {
        $time30 = "";
        $this->db->select("*");
        if ($status) {
            $this->db->where('status!=', $status);
        }

        $this->db->where('event_date>=', date("Y-m-d"));

        $this->db->order_by("event_date desc");
        $query = $this->db->get('movie_event');
        $movieevents = $query->result_array();
        $returndata = [];
        foreach ($movieevents as $key => $value) {
            $theater = $this->theaterInformation($value["theater_id"]);
            $movie = $this->movieInforamtion($value["movie_id"]);
            $value["movie"] = $movie;
            $value["theater"] = $theater;
            array_push($returndata, $value);
        }
        return $returndata;
    }

    function eventInformation($event_id) {
        $this->db->select("*");
        $this->db->where("id", $event_id);
        $query = $this->db->get('movie_event');
        $movieevents = $query->row_array();
        $returndata = [];

        $theater = $this->theaterInformation($movieevents["theater_id"]);
        $movie = $this->movieInforamtion($movieevents["movie_id"]);
        $movieevents["movie"] = $movie;
        $movieevents["theater"] = $theater;
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

    function wheelchair($inputchair) {
        $wheelchairt_temp = (explode(", ", $inputchair));
        $wheelchair = array();
        if ($inputchair) {
            foreach ($wheelchairt_temp as $key => $value) {
                $wheelchair[$value] = "";
            }
        }

        return $wheelchair;
    }

    function theaterTemplateSingle($template_id) {
        $this->db->where('id', $template_id);
        $query = $this->db->get('movie_theater_template');
        $theater_array = $query->row_array();
        $theater_array_adata = array();
        $this->db->where('template_id', $theater_array["id"]);
        $query = $this->db->get('movie_theater_template_class');
        $theater_array_class = $query->result_array();
        $theater_array["class_price"] = $theater_array_class;
        $reserveseat_temp = (explode(", ", $theater_array["reserve_seats"]));
        $reserveseats = array();
        foreach ($reserveseat_temp as $key => $value) {
            $reserveseats[$value] = "";
        }
        $theater_array["reserve_seats"] = $reserveseats;

        return $theater_array;
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

    function getSelectedSeats($event_id, $booking_type) {
        $this->db->select("*");
        $this->db->where('event_id', $event_id);
        $this->db->where("booking_type", $booking_type);

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

    function getSelectedSeatsEventId($event_id) {
        $this->db->select("*");
        $this->db->where('event_id', $event_id);
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

        $this->db->where('status', "active");

        $query = $this->db->get('movie_show');
        $movieobj = $query->row_array();
        if ($movieobj) {
            $movieobj["image"] = MOVIEPOSTER . $movieobj["image"];
            return $movieobj;
        } else {
            return false;
        }
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

        $this->db->where('event_date>=', $cdate);
        $this->db->group_by("movie_id");
        $this->db->group_by("theater_id");
//        $this->db->group_by("id");
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

    function getEventsSingle($theater_id, $movie_id, $template_id) {

        $this->db->where('theater_id', $theater_id);
        $this->db->where("movie_id", $movie_id);
        $this->db->where("theater_template_id", $template_id);
        $query = $this->db->get('movie_event');
        $evalue = $query->row_array();

        $theaterobj = $this->theaterInformation($theater_id);
        $temparray["theaterobj"] = $theaterobj;

        $temparray["template"] = $this->theaterTemplate($theater_id)[$evalue["theater_template_id"]];

        $movieobj = $this->movieInforamtion($movie_id);
        $temparray["movie"] = $movieobj;

        $this->db->where('movie_id', $movie_id);
        $this->db->where('theater_id', $theater_id);
        $this->db->where('theater_template_id', $template_id);
        $this->db->order_by("event_date asc");
        $query = $this->db->get('movie_event');
        $theaterobj = $query->result_array();
        $temparray["event_datetime"] = $theaterobj;

        return $temparray;
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

    function createRange($start, $end, $total, $gapes, $row, $booked, $reserve, $paid, $gap) {
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

            if (isset($paid[$trow])) {
                $temprow[$trow] = "P";
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

    function getLayout_GH_V_WALK($booked, $reserved, $paid, $classprice) {
        $gaps = array();
        $layout = array(
            "totalinrow" => 20,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "A" => $this->createRange(6, 13, 19, [], "A", $booked, $reserved, $paid, $gaps),
                        "B" => $this->createRange(3, 16, 19, [], "B", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "C" => $this->createRange(3, 17, 19, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(3, 17, 19, [], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(3, 17, 19, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(3, 18, 19, [], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 19, 19, [], "G", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayoutGrandOcean($booked, $reserved, $paid, $classprice) {

        $gaps = array("7" => "", "25" => "");
        $layout = array(
            "totalinrow" => 35,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "A" => $this->createRange(8, 25, 32, [], "A", $booked, $reserved, $paid, $gaps),
                        "B" => $this->createRange(1, 32, 32, [], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 32, 32, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 32, 32, [], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(1, 32, 32, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 32, 32, [], "F", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "G" => $this->createRange(5, 28, 32, [25, 26, 7, 8], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(5, 28, 32, [], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(5, 28, 32, [], "I", $booked, $reserved, $paid, $gaps),
                        "J" => $this->createRange(1, 32, 32, [], "J", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "K" => $this->createRange(1, 32, 32, [24, 25, 9, 8], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 32, 32, [24, 25, 9, 8], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 32, 32, [24, 25, 9, 8], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 32, 32, [], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 32, 32, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 32, 32, [], "P", $booked, $reserved, $paid, $gaps),
                        "Q" => $this->createRange(27, 32, 32, [], "Q", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE4($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "19" => "");
        $layout = array(
            "totalinrow" => 25,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 22, 22, [16], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 22, 22, [], "C", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "D" => $this->createRange(1, 22, 22, [16], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(1, 22, 22, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 22, 22, [16], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 22, 22, [], "G", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "H" => $this->createRange(1, 22, 22, [16], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 22, 22, [], "I", $booked, $reserved, $paid, $gaps),
                        "J" => $this->createRange(1, 22, 22, [16], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 22, 22, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 22, 22, [16], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 22, 22, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 22, 22, [16], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 22, 22, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 22, 22, [16, 5, 6, 7, 8, 9], "P", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE4B($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "16" => "");
        $layout = array(
            "totalinrow" => 25,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 22, 22, [16], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 22, 22, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 22, 22, [16], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(1, 22, 22, [], "E", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "F" => $this->createRange(1, 22, 22, [16], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 22, 22, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 22, 22, [16], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 22, 22, [], "I", $booked, $reserved, $paid, $gaps),
                        "J" => $this->createRange(1, 22, 22, [16], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 22, 22, [], "K", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "L" => $this->createRange(1, 22, 22, [16], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 22, 22, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 22, 22, [16], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 22, 22, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 22, 22, [16, 5, 6, 7, 8, 9], "P", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE4C($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "16" => "");
        $layout = array(
            "totalinrow" => 25,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 22, 22, [16], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 22, 22, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 22, 22, [16], "D", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "E" => $this->createRange(1, 22, 22, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 22, 22, [16], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 22, 22, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 22, 22, [16], "H", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "I" => $this->createRange(1, 22, 22, [], "I", $booked, $reserved, $paid, $gaps),
                        "J" => $this->createRange(1, 22, 22, [16], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 22, 22, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 22, 22, [16], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 22, 22, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 22, 22, [16], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 22, 22, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 22, 22, [16, 5, 6, 7, 8, 9], "P", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE4D($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "16" => "");
        $layout = array(
            "totalinrow" => 25,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 22, 22, [16], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 22, 22, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 22, 22, [16], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(1, 22, 22, [], "E", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "F" => $this->createRange(1, 22, 22, [16], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 22, 22, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 22, 22, [16], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 22, 22, [], "I", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "J" => $this->createRange(1, 22, 22, [16], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 22, 22, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 22, 22, [16], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 22, 22, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 22, 22, [16], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 22, 22, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 22, 22, [16, 5, 6, 7, 8, 9], "P", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE3($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "19" => "");
        $layout = array(
            "totalinrow" => 24,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 21, 21, [19], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 21, 21, [], "C", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "D" => $this->createRange(1, 21, 21, [19], "D", $booked, $reserved, $paid, $gaps),
                        "E" => $this->createRange(1, 21, 21, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 21, 21, [19], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 21, 21, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 21, 21, [19], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 21, 21, [], "I", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "J" => $this->createRange(1, 21, 21, [19], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 21, 21, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 21, 21, [19], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 21, 21, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 21, 21, [19], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 21, 21, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 21, 21, [19], "P", $booked, $reserved, $paid, $gaps),
                        "Q" => $this->createRange(1, 21, 21, [20, 21, 3], "Q", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE3B($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "19" => "");
        $layout = array(
            "totalinrow" => 24,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "B" => $this->createRange(1, 21, 21, [19], "B", $booked, $reserved, $paid, $gaps),
                        "C" => $this->createRange(1, 21, 21, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 21, 21, [19], "D", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "E" => $this->createRange(1, 21, 21, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 21, 21, [19], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 21, 21, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 21, 21, [19], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 21, 21, [], "I", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "J" => $this->createRange(1, 21, 21, [19], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 21, 21, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 21, 21, [19], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 21, 21, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 21, 21, [19], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 21, 21, [], "O", $booked, $reserved, $paid, $gaps),
                        "P" => $this->createRange(1, 21, 21, [19], "P", $booked, $reserved, $paid, $gaps),
                        "Q" => $this->createRange(1, 21, 21, [20, 21, 3, 7], "Q", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE1($booked, $reserved, $paid, $classprice) {
        $gaps = array("4" => "", "19" => "");
        $layout = array(
            "totalinrow" => 26,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#fff",
                    "row" => array(
                        "C" => $this->createRange(1, 24, 24, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 24, 24, [19], "D", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#fff",
                    "row" => array(
                        "E" => $this->createRange(1, 24, 24, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 24, 24, [19], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 24, 24, [], "G", $booked, $reserved, $paid, $gaps),
                        "H" => $this->createRange(1, 24, 24, [19], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 24, 24, [], "I", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#fff",
                    "row" => array(
                        "J" => $this->createRange(1, 24, 24, [19], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 24, 24, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 24, 24, [19], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 24, 24, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 24, 24, [19], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 24, 24, [5, 6, 7, 12], "O", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function getLayout_GH_HSE1B($booked, $reserved, $paid, $classprice) {

        $gaps = array("4" => "", "19" => "");
        $layout = array(
            "totalinrow" => 26,
            "sitclass" => array(
                "class1" => array(
                    "price" => count($classprice) ? $classprice[0]["class_price"] : 0,
                    "rowcount" => "2",
                    "color" => "#ffc0b266",
                    "row" => array(
                        "C" => $this->createRange(1, 23, 23, [], "C", $booked, $reserved, $paid, $gaps),
                        "D" => $this->createRange(1, 23, 23, [19], "D", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class2" => array(
                    "price" => count($classprice) ? $classprice[1]["class_price"] : 0,
                    "rowcount" => "6",
                    "color" => "#ffe0b266",
                    "row" => array(
                        "E" => $this->createRange(1, 23, 23, [], "E", $booked, $reserved, $paid, $gaps),
                        "F" => $this->createRange(1, 23, 23, [19], "F", $booked, $reserved, $paid, $gaps),
                        "G" => $this->createRange(1, 23, 23, [], "G", $booked, $reserved, $paid, $gaps),
                    )
                ),
                "class3" => array(
                    "price" => count($classprice) ? $classprice[2]["class_price"] : 0,
                    "rowcount" => "5",
                    "color" => "#ff572247",
                    "row" => array(
                        "H" => $this->createRange(1, 23, 23, [19], "H", $booked, $reserved, $paid, $gaps),
                        "I" => $this->createRange(1, 23, 23, [], "I", $booked, $reserved, $paid, $gaps),
                        "J" => $this->createRange(1, 23, 23, [19], "J", $booked, $reserved, $paid, $gaps),
                        "K" => $this->createRange(1, 23, 23, [], "K", $booked, $reserved, $paid, $gaps),
                        "L" => $this->createRange(1, 23, 23, [19], "L", $booked, $reserved, $paid, $gaps),
                        "M" => $this->createRange(1, 23, 23, [], "M", $booked, $reserved, $paid, $gaps),
                        "N" => $this->createRange(1, 23, 23, [19], "N", $booked, $reserved, $paid, $gaps),
                        "O" => $this->createRange(1, 23, 23, [5, 6, 7, 12], "O", $booked, $reserved, $paid, $gaps),
                    )
                ),
            )
        );
        return $layout;
    }

    function paymentGroups($event_id) {
        $querystr = "select sum(total_price) as total_price, payment_type, sum(total_seats) as total_seats from "
                . "(SELECT mtb.id, mtb.payment_type, mtb.booking_type, mtb.total_price, (select count(id)"
                . " from movie_ticket where movie_ticket_booking_id=mtb.id) as total_seats "
                . "FROM `movie_ticket_booking` as mtb where mtb.event_id=$event_id and mtb.booking_type='Paid') "
                . "as atable group by payment_type;";
        $query = $this->db->query($querystr);
        return $query->result_array();
    }

    function getHoldSeats($template_id) {
        $this->db->where('id', $template_id);
        $query = $this->db->get('movie_theater_template');
        $template_array = $query->row_array();
        if ($template_array) {
            return substr_count($template_array["reserve_seats"], "-");
        } else {
            return 0;
        }
    }

    function eventBookingList() {
        $eventlist = $this->movieevent_active();
        $eventdatalist = [];
        foreach ($eventlist as $mk => $mv) {
            $event_id = $mv["id"];
            $mv["paymentdata"] = $this->paymentGroups($event_id);

            $reserved = $this->getSelectedSeats($event_id, "Reserved");
            $paid = $this->getSelectedSeats($event_id, "Paid");
            $theaterobjcount = $this->getHoldSeats($mv["theater_template_id"]);
            $mv["hold"] = $theaterobjcount;
            $mv["totalavailable"] = $mv["theater"]['seat_count'] - $theaterobjcount;
            $mv["paid"] = count($paid);
            $mv["reserved"] = count($reserved);

            array_push($eventdatalist, $mv);
        }

        return $eventdatalist;
    }

    function getSelectedSeatsByAttr($theater_id, $movie_id, $select_date, $select_time) {
        $this->db->select("*");
        $this->db->where('booking_type!=', "Cancelled");
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

}
