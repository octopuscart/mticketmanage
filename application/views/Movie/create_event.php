<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
?>
<!-- ================== BEGIN PAGE CSS STYLE ================== -->
<link href="<?php echo base_url(); ?>assets/plugins/jquery-tag-it/css/jquery.tagit.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/plugins/jquery-tag-it/js/tag-it.min.js"></script>

<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />
<div class="page-wrapper">
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->

        <!-- begin #content -->
        <div id="content" class="content">
            <!-- begin #content -->
            <!-- begin #content -->
            <div id="content" class="content content-full-width">
                <!-- begin vertical-box -->
                <div class="vertical-box">

                    <!-- begin vertical-box-column -->
                    <div class="vertical-box-column">

                        <!-- begin wrapper -->
                        <div class="wrapper">
                            <div class="col-md-6">
                                <!-- begin email form -->
                                <form action="#" method="post" enctype="multipart/form-data">
                                    <!-- begin email to -->
                                    <div class="card-body bg-light">
                                        <h4 class="card-title">Create Movie/Event List</h4>


                                        <div class="form-group has-success">
                                            <label class="control-label">Select Event</label>
                                            <select class="form-control custom-select" name="movie_id">
                                                <?php
                                                foreach ($eventlist as $key => $value) {
                                                    ?>
                                                    <option value = "<?php echo $value["id"]; ?>"><?php echo $value["title"]; ?> - <?php echo $value["attr"]; ?></option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                            <small class="form-control-feedback"> Select Event Or Movie Here </small> 
                                        </div>
                                        <div class="form-group has-success">
                                            <label class="control-label">Select Theater</label>
                                            <select class="form-control custom-select" name="theater_id">
                                                <?php
                                                foreach ($theater_list as $key => $value) {
                                                    ?>
                                                    <option value = "<?php echo $value["id"]; ?>"><?php echo $value["title"]; ?> </option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                            <small class="form-control-feedback"> Select Theater For Event Here </small> 
                                        </div>
                                        <div class="row">
                                           


                                        </div>
                                        <button type="submit" name="submit_data" class="btn btn-primary p-l-40 p-r-40">Create New Event</button>

                                    </div>


                                    <!-- end email content -->
                                </form>
                                <!-- end email form -->
                            </div>
                        </div>
                        <!-- end wrapper -->
                    </div>
                    <!-- end vertical-box-column -->
                </div>
                <!-- end vertical-box -->
            </div>
            <!-- end #content -->
        </div>
    </div>
</div>

<?php
$this->load->view('layout/footer');
?>
<script>
    function changeCategory(cat_name, cat_id) {
        $("#category_name").text(cat_name);
        $("#category_id").val(cat_id);
    }

    $(function () {


        $('#tags').tagit({
            availableTags: <?php echo json_encode($tags); ?>
        });






<?php
$checklogin = $this->session->flashdata('checklogin');
if ($checklogin['show']) {
    ?>
            $.gritter.add({
                title: "<?php echo $checklogin['title']; ?>",
                text: "<?php echo $checklogin['text']; ?>",
                image: '<?php echo base_url(); ?>assets/emoji/<?php echo $checklogin['icon']; ?>',
                            sticky: true,
                            time: '',
                            class_name: 'my-sticky-class '
                        });
    <?php
}
?>
                })
</script>