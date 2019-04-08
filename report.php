<?php 

require_once( 'db_connection.php' );

require_once( 'dist/library/class-session.php' );
ND_Session :: init();

if( empty ( ND_Session :: get('id') ) ){
   // header("Location: ". $_SERVER['REQUEST_SCHEME']."://" .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."index.php");
    //exit;

    die('Please Login First');
}

// DONT FORGET TO REMOVE THIS LINE
//ND_Session :: set('id',3);

require_once( 'dist/library/class-user.php' );
require_once( 'dist/library/class-html.php' );


$device_dropdown = ND_User :: get_device_dropdown_array();
$from_date = empty( $_POST['from_date'] ) ? '': $_POST['from_date'] ;

$to_date = empty( $_POST['to_date'] ) ? '': $_POST['to_date'] ;

$post_device_id = ( empty( $_POST['device_id'] ) OR 'all' === $_POST['device_id']  )? '': $_POST['device_id'] ;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Kamlesh Kumar">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">

    <!-- <title>Aro GPS Tracker Report </title> -->
    <?php 
    // if( ! empty( $post_device_id ) AND empty( $from_date ) AND empty( $to_date ) ){

    //     echo "<title>Summary For Device - ". $device_dropdown[ $post_device_id ] ."</title>";

    // }
    // if( ! empty( $post_device_id ) AND ! empty( $from_date ) AND empty( $to_date ) ){

    //     echo "<title>Summary For Device - ". $device_dropdown[ $post_device_id ] ." After Date $from_date</title>";

    // }
    // if( ! empty( $post_device_id ) AND ! empty( $from_date ) AND ! empty( $to_date ) ){

    //     echo "<title>Summary For Device - ". $device_dropdown[ $post_device_id ] ." $from_date to $to_date</title>";

    // }

    if( empty( $from_date ) AND ! empty( $to_date ) ){

        echo"<title>Summary For Device - Before $to_date</title>";

    }

    if( ! empty( $from_date ) AND  empty( $to_date ) ){

        echo "<title>Summary For Device - After $to_date</title>";

    }

    if( ! empty( $from_date ) AND ! empty( $to_date ) ){

        echo "<title>Summary For Device - $from_date to $to_date</title>";

    }

    if( ! empty( $from_date ) AND ! empty( $to_date ) ){

        echo "<title>Summary For Device</title>";

    }
    if(  empty( $from_date ) AND  empty( $to_date ) ){

        echo "<title>Summary For Devices</title>";

    }

    // if(  empty( $post_device_id ) AND  empty( $from_date ) AND  empty( $to_date ) ){

    //     echo "<title>Summary For All Devices</title>";

    // }

    // if(  empty( $post_device_id ) AND ! empty( $from_date ) AND  ! empty( $to_date ) ){

    //     echo "<title>Summary For All Devices - $from_date to $to_date</title>";

    // }

    // if(  empty( $post_device_id ) AND  empty( $from_date ) AND  ! empty( $to_date ) ){

    //     echo "<title>Summary For All Devices -  Before $to_date</title>";

    // }

        
    
    
    ?>


    <!-- Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
#anand_report .form-group {
    display: inline-block;
}
</style>
</head>

<body class="skin-default fixed-layout">
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Welcome to Aro GPS Tracker</p>
        </div>
    </div>
    <div id="main-wrapper">
        <?php 
            include 'dist/php/header.php';
        ?>
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                      
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                          
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Get Report</h4>
                                <h6 class="card-subtitle">Filter Data</h6>
                                <div class="card">
                            <div class="card-body">
                            
                            <div class="row">
<form method='post' id='anand_report'>
                            <div class="col-xs-3 form-group">
                            <label for="device_label">Device </label> <br>

                <?php 






echo form_dropdown('device_id[]', $device_dropdown, isset( $_POST['device_id'] ) ?  $_POST['device_id'][0] : '' , ['id'=>'device_label' ,'class' => 'form-control select2','multiple'=> "multiple" ]);

?>

              </div>

                <div class="col-xs-3 form-group">
                  <label for="exampleInputPassword1">Trip Start Date</label>
                  <input    type="text" name='from_date' value="<?php echo $from_date; ?>"  class="datepicker form-control" id="exampleInputPassword1" placeholder="Date Of Joining">
                </div>

                <div class="col-xs-3 form-group">
                  <label for="exampleInputPassword1">Trip End Date</label>
                  <input   type="text" name='to_date' value="<?php echo $to_date; ?>" class="datepicker form-control" id="exampleInputPassword1" placeholder="Date Of Joining">
                </div>

                <div class="col-xs-3 form-group">
                <label for="exampleInputEmail1">Action</label>
                <button type="submit" name='filter_form' value='search' class="btn btn-warning btn-block">Filter</button>
                </div>
</form>
              </div>
                            
                              </div> </div>

                                <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                                <div class="table-responsive m-t-40">
								            
                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                 <th>S.No.</th>
                                                            <th>Trip No</th>
                                                            <th>Region</th>
                                                            <th>Origin</th>
                                                            <th>Vehicle No</th>
                                                            <th>Driver Name</th>
                                                            <th>Licence No</th>
															<th>Vehicle Inspection Id</th>
															<th>Driver Contact</th>
															<th>No of Trips</th>
															
                                                           <th>No of drop points</th>
														   <th>	Drop points covered	</th>
														   <th>Direction	</th>
														   <th>Trip Start Date</th>
														   <th>	Trip Start Time	</th>
														   <th>Trip End Date</th>
														   <th>	Trip End Time</th>
														   <th>	Total distance Travelled (km)</th>
														   <th>	Total Drive time (h:m)	</th>
														   <th>Night Driving (yes/no)</th>
														   <th>if yes, Duration (h:m)</th>
														   <th>	Total distance travelled at night (km)	</th>
														   <th>Overspeed (above 65kmph for 2 min)	</th>
														   <th>Continuous Driving</th>
														   <th>	Harsh Braking</th>
														   <th>	Rapid Acceleratio</th>
														   <th>Harsh Maneuvering	</th>
														   <th>Total Violations (Sum of all)</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
															<th>S.No.</th>
                                                            <th>Trip No</th>
                                                            <th>Region</th>
                                                            <th>Origin</th>
                                                            <th>Vehicle No</th>
                                                            <th>Driver Name</th>
                                                            <th>Licence No</th>
															<th>Vehicle Inspection Id</th>
															<th>Driver Contact</th>
															<th>No of Trips</th>
															
                                                           <th>No of drop points</th>
														   <th>	Drop points covered	</th>
														   <th>Direction	</th>
														   <th>Trip Start Date</th>
														   <th>	Trip Start Time	</th>
														   <th>Trip End Date</th>
														   <th>	Trip End Time</th>
														   <th>	Total distance Travelled (km)</th>
														   <th>	Total Drive time (h:m)	</th>
														   <th>Night Driving (yes/no)</th>
														   <th>	if yes, Duration (h:m)</th>
														   <th>	Total distance travelled at night (km)	</th>
														   <th>Overspeed (above 65kmph for 2 min)	</th>
														   <th>Continuous Driving</th>
														   <th>	Harsh Braking</th>
														   <th>	Rapid Acceleratio</th>
														   <th>Harsh Maneuvering	</th>
														   <th>Total Violations (Sum of all)</th>
															
                                            </tr>
                                        </tfoot>
                                                                                         <tbody>
<?php 
//$all_devices[] =  ND_User :: get_my_devices ( ) ;
$all_devices[0] = [];
 if( isset( $_POST['device_id'] ) and ! empty ( $_POST['device_id'] ) ) {
//     $all_devices = [];
     $all_devices[0] = $_POST['device_id'];
 }

$i = 0;



foreach( $all_devices[0] as $device ): $i++; ?>
<tr>
															
															<td><?php  echo $device;//$i ?></td>
                                                            
                                                            <td><?php echo ND_User :: get_trip_number( $device ); ?></td>
															<td><?php echo ND_User :: get_group_name( $device ); ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'Origin' );?></td>
															<td><?php echo ND_User :: get_device_row( $device ,'name' ); ?></td>
															<td><?php echo ucwords ( ND_User :: get_driver_row( $device ,'name' ) ); ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'License Number' );?></td>
															<td><?php echo ND_User :: get_driver_row( $device ,'uniqueid' ) ; ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'Driver Contact' );?></td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															
															
                                                            <?php if( empty($from_date) AND empty( $to_date ) ):?>
                                                            <td><?php echo ND_User :: get_trip_start_date( $device );?></td>
															<td><?php echo ND_User :: get_trip_start_time( $device );?></td>
															<td><?php echo ND_User :: get_trip_end_date( $device );?></td>
															<td><?php echo ND_User :: get_trip_end_time( $device );?></td>
<?php else:?>
<td><?php echo empty( $from_date )?'-': $from_date;  ?></td>
<td>-</td>
<td><?php echo empty( $to_date )?'-': $to_date;  ?></td>
<td>-</td>
															
<?php endif;?>                                                    
                                                        <td><?php echo ND_User :: get_total_distance_travelled_between_date( $device , $from_date , $to_date );?></td>

                                                            <td><?php 

                                                            // here the last parameter is $want_time.. instaed of distance diff.. it will show time duration difference.
                                                            echo ND_User :: get_total_distance_travelled_between_date( $device , $from_date , $to_date , true );?></td>

															<!-- <td><?php //echo ND_User :: get_total_distance_travelled( $device );?></td> -->
															<!-- <td><?php //echo ND_User :: get_total_time_taken( $device );?></td> -->
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															<td>0</td>
															
                                                            
														
                                                             
</tr>
<?php endforeach; ?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
              
                <?php
                    include 'dist/php/right-sidebar.php';
                ?>
              
        <?php
            include 'dist/php/footer.php';
        ?>
     
            </div>
           
      
    </div>
   
    <script src="assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
    <script src="assets/node_modules/popper/popper.min.js"></script>
    <script src="assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="dist/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="dist/js/waves.js"></script>
    <script src="dist/js/sidebarmenu.js"></script>
    <script src="assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
    <script src="dist/js/custom.min.js"></script>
    <script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
        $(document).ready(function() {
            var table = $('#example').DataTable({
                "columnDefs": [{
                    "visible": false,
                    "targets": 2
                }],
                "order": [
                    [2, 'asc']
                ],
                "displayLength": 25,
                "drawCallback": function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var last = null;
                    api.column(2, {
                        page: 'current'
                    }).data().each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                            last = group;
                        }
                    });
                }
            });
            // Order by the grouping
            $('#example tbody').on('click', 'tr.group', function() {
                var currentOrder = table.order()[0];
                if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                    table.order([2, 'desc']).draw();
                } else {
                    table.order([2, 'asc']).draw();
                }
            });
        });
    });
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    jQuery('.select2').select2();

    jQuery('.datepicker').datepicker({
      autoclose: true,
      format:'dd/mm/yyyy',
      todayHighlight:true,
      orientation:'bottom'
    });
    </script>
</body>

</html>