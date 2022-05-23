<?php
$userdata = $this->session->userdata('logged_in');

$menu_control = array();

if ($userdata) {
    switch ($userdata["user_type"]) {
        case "Distributor":
            $order_menu = array(
                "title" => "Booking",
                "icon" => "icon-Receipt-4",
                "active" => "",
                "sub_menu" => array(
                    "Booking Reports" => site_url("MovieEvent/deshboard"),
  
                ),
            );
            array_push($menu_control, $order_menu);
            break;
        case "Admin":
            $order_menu = array(
                "title" => "Booking",
                "icon" => "icon-Receipt-4",
                "active" => "",
                "sub_menu" => array(
                    "Booking Reports" => site_url("MovieEvent/deshboard"),
                    "Book Now" => site_url("MovieEvent/bookingList"),
                    "Search Booking" => site_url("MovieEvent/eventReportAll"),
                ),
            );
            array_push($menu_control, $order_menu);

            $client_menu = array(
                "title" => "Client Manegement",
                "icon" => "icon-Add-User",
                "active" => "",
                "sub_menu" => array(
                    "Clients Reports" => site_url("UserManager/usersReport"),
                ),
            );
            array_push($menu_control, $client_menu);

            $lookbook_menu = array(
                "title" => "Movie Information",
                "icon" => "ti-image",
                "active" => "",
                "sub_menu" => array(
                    "Add Movie Info" => site_url("MovieEvent/newEvent"),
                    "Movie List" => site_url("MovieEvent/eventList"),
                ),
            );
            array_push($menu_control, $lookbook_menu);

            $blog_menu = array(
                "title" => "Theater Management",
                "icon" => "ti-video-camera",
                "active" => "",
                "sub_menu" => array(
                    "Create  Event(s)" => site_url("MovieEvent/widgetEvent"),
                    "Event List(s)" => site_url("MovieEvent/evenMovietList"),
                    "Release Hold Seat(s)" => site_url("MovieEvent/releaseHold"),
                ),
            );
            array_push($menu_control, $blog_menu);

            $theater = array(
                "title" => "Theater Settings",
                "icon" => "ti-blackboard",
                "active" => "",
                "sub_menu" => array(
                    "Theater(s) List" => site_url("CMS/theaterSetting"),
                    "Create Template" => site_url("MovieEvent/newsTheaterTemplate"),
                ),
            );
            array_push($menu_control, $theater);

            $setting_menu = array(
                "title" => "Settings",
                "icon" => "ti-settings",
                "active" => "",
                "sub_menu" => array(
                    "System Log" => site_url("Services/systemLogReport"),
                    "Report Configuration" => site_url("Configuration/reportConfiguration"),
                ),
            );
            array_push($menu_control, $setting_menu);

            $social_menu = array(
                "title" => "Social Management",
                "icon" => "ti-share-alt",
                "active" => "",
                "sub_menu" => array(
                    "Social Link" => site_url("CMS/socialLink"),
                ),
            );
            array_push($menu_control, $social_menu);

            $seo_menu = array(
                "title" => "SEO",
                "icon" => "ti-world",
                "active" => "",
                "sub_menu" => array(
                    "General" => site_url("CMS/siteSEOConfigUpdate"),
                    "Page Wise Setting" => site_url("CMS/seoPageSetting"),
                ),
            );
            array_push($menu_control, $seo_menu);
            break;
        default:
    }
} else {
    redirect("Authentication/index", "refresh");
}


foreach ($menu_control as $key => $value) {
    $submenu = $value['sub_menu'];
    foreach ($submenu as $ukey => $uvalue) {
        if ($uvalue == current_url()) {
            $menu_control[$key]['active'] = 'active';
            break;
        }
    }
}
?>
<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li>
                    <!-- User Profile-->
                    <div class="user-profile dropdown m-t-20">
                        <div class="user-pic">
                            <img src="<?php echo base_url(); ?>assets/assets/images/users/1.jpg" alt="users" class="rounded-circle img-fluid" />
                        </div>
                        <div class="user-content hide-menu m-t-10">
                            <h5 class="m-b-10 user-name font-medium"><?php echo $userdata['first_name']; ?> <?php echo $userdata['last_name']; ?></h5>
                            <a href="<?php echo site_url("profile") ?>" title="Profile" class="btn btn-circle btn-sm">
                                <i class="ti-settings"></i>
                            </a>
                            &nbsp;
                            <a href="<?php echo site_url("Authentication/logout") ?>" title="Logout" class="btn btn-circle btn-sm">
                                <i class="ti-power-off"></i>
                            </a>

                        </div>
                    </div>
                    <!-- End User Profile-->
                </li>
                <!-- User Profile-->

                <?php foreach ($menu_control as $mkey => $mvalue) { ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow waves-effect waves-dark <?php echo $mvalue['active']; ?>" href="javascript:void(0)" aria-expanded="false">
                            <i class="<?php echo $mvalue['icon']; ?>"></i>
                            <span class="hide-menu"><?php echo $mvalue['title']; ?> </span>
                        </a>

                        <ul aria-expanded="false" class="collapse  first-level <?php echo $mvalue['active'] == 'active' ? 'in' : ''; ?>">
                            <?php
                            $submenu = $mvalue['sub_menu'];
                            foreach ($submenu as $key => $value) {
                                ?>
                                <li class="sidebar-item">
                                    <a href="<?php echo $value; ?>" class="sidebar-link">
                                        <i class="mdi mdi-book-multiple"></i>
                                        <span class="hide-menu"> <?php echo $key; ?> </span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>


            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
