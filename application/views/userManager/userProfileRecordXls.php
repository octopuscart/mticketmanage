 <table id="tableDataOrder" class="table table-bordered table-striped" border="1">
        <thead>
            <tr>
                <th style="width: 20px;">S.N.</th>
                <th style="width: 75px;">Name</th>
                <th style="width: 100px;">Email </th>
                <th style="width: 100px;">Contact No.</th>

            </tr>
        </thead>
        <tbody>
            <?php
            if (count($users)) {

                $count = 1;
                foreach ($users as $key => $value) {
                    ?>
                    <tr>
                        <td><?php echo $count; ?></td>

                        

                        <td>
                            <span class="">
                                <b><span class="seller_tag"><?php echo $value->name; ?></span></b>
                            </span>
                        </td>

                        <td>
                            <span class="">
                                <span class="seller_tag">
                                    <?php echo $value->email; ?>
                                </span>

                            </span>
                        </td>
                        <td>
                            <span class="">

                                <?php echo $value->contact_no; ?>
                            </span>
                        </td>




                    </tr>
                    <?php
                    $count++;
                }
            }
            ?>
        </tbody>
    </table>