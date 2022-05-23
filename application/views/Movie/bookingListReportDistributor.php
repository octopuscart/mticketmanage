<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
?>

<style>
    .minitable td{
        padding:0px 7px;


    }
    .minitable th{
        padding:0px 7px;

    }
</style>
<div class="page-wrapper" >
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->

        <!-- begin #content -->
        <div id="content" class="content">
            <!-- begin #content -->
            <!-- begin #content -->
            <div id="content" class="content content-full-width">
                <div class="feed-widget scrollable ps-container ps-theme-default "  >

              
                    <h3>Seats Collection</h3>
                    <div class="row m-t-40">
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card card-hover">
                                <div class="box bg-info text-center">
                                    <h1 class="font-light text-white">
                                        <?php echo $totaldata["totalavailable"] ?>
                                    </h1>
                                    <h6 class="text-white">Total Seats</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card card-hover">
                                <div class="box bg-orange text-center">
                                    <h1 class="font-light text-white">
                                        <?php echo $totaldata["reserved"] ?>
                                    </h1>
                                    <h6 class="text-white">Reserved</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card card-hover">
                                <div class="box bg-green-darker text-center" style="background: green;">
                                    <h1 class="font-light text-white">
                                        <?php echo $totaldata["paid"] ?>
                                    </h1>
                                    <h6 class="text-white">Purchase</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <?php echo $totaldata["pending"] ?>
                                    </h1>
                                    <h6 class="text-white">Pending</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->


                    </div>
                    <hr/>
               
                    <hr/>
                    <div class="table-responsive">
                        <table class="table v-middle table-bordered">
                            <thead>
                                <tr>
                                    <th class="border-top-0">Show Title</th>
                                    <th class="border-top-0" style="    width: 250px;">Location / Date Time</th>

                                    <th class="border-top-0">Total Seat(s)</th>
                                    <th class="border-top-0">Paid</th>
                                    <th class="border-top-0">Reserved</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($eventlist as $tkey => $tvalue) {
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="m-r-10">
                                                    <img src="<?php echo $tvalue["movie"]['image']; ?>" alt="user" class="" width="45">
                                                </div>
                                                <div class="m-l-10" style="margin-left: 10px;">
                                                    <h4 class="m-b-0 font-16"><?php echo $tvalue["movie"]['title']; ?></h4>
                                                    <span style="font-size: 10px;"><?php echo $tvalue["movie"]['attr']; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $tvalue["theater"]['title']; ?>
                                            <br/><?php echo $tvalue["event_date"]; ?> / <?php echo $tvalue["event_time"]; ?>
                                        </td>
                                        <td><?php echo $tvalue["totalavailable"]; ?></td>
                                        <td><b><?php echo $tvalue["paid"]; ?></b></td>
                                        <td><b><?php echo $tvalue["reserved"]; ?></b></td>
                                    
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>                        





                    </ul>
                    <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 450px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 432px;"></div></div>

                </div>
            </div>
            <!-- end #content -->
        </div>
    </div>
</div>

<?php
$this->load->view('layout/footer');
?>
<script>

    var theater_id = "<?php echo $theaterobj ? $theaterobj['layout'] : ""; ?>";

</script>

<script src="<?php echo base_url(); ?>assets/angular/ng-movies.js"></script>
