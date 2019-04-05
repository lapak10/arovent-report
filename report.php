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
    <title><?php //echo json_encode( ND_User :: get_my_devices( )  ) ; ?>Aro GPS Tracker Report</title>
    <!-- Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
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
$all_devices =  ND_User :: get_my_devices ( ) ;
$i = 0;
foreach( $all_devices as $device ): $i++; ?>
<tr>
															
															<td><?php  echo $i ?></td>
                                                            
                                                            <td><?php echo ND_User :: get_trip_number( $device ); ?></td>
															<td><?php echo ND_User :: get_group_name( $device ); ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'Origin' );?></td>
															<td><?php echo ND_User :: get_device_row( $device ,'name' ); ?></td>
															<td><?php echo ucwords ( ND_User :: get_driver_row( $device ,'name' ) ); ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'License Number' );?></td>
															<td><?php echo ND_User :: get_driver_row( $device ,'uniqueid' ) ; ?></td>
															<td><?php echo ND_User :: get_driver_attribute( ND_User :: get_linked_driver_id( $device ) , 'Driver Contact' );?></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															
                                                            
														
                                                             
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
    </script>
</body>

</html>