<?php
class ND_User{

    private static $connection = '';

    private static $users_table = 'tc_users';
    private static $user_to_device_table = 'tc_user_device';
   // private static $user_to_device_table = 'tc_user_device';

    public static function attempt_login( $username,$token ){

        $email_row  =  self :: get_user_row_for_email_id( $username );

        if( ! $email_row ) {
            return false;
        }

        $hashedpassword = $email_row['hashedpassword'];

        $temp_hash = self :: make_hash( $token, $email_row['salt'] );
        
        //$hashedpassword = $email_row['salt'];
        
        //$sql = "SELECT *  FROM ". self :: $users_table ." WHERE email = '". mysqli_real_escape_string( self :: get_connection_to_db() , $username ) ."' AND `token` = '". mysqli_real_escape_string( self :: get_connection_to_db() , $token ) ."'";
        
        //$result = self :: run_query( $sql );

        return $hashedpassword === $temp_hash ? $email_row : false ;
      
    
    }

    private static function get_my_devices_array(){

    }

    private static function make_hash($pass,$salt){
        return bin2hex( hash_pbkdf2('sha1',$pass, hex2bin( $salt ),1000,24,true) );
    }

    private static function get_user_row_for_email_id( $email = '' ){


        $sql = "SELECT *  FROM ". self :: $users_table ." WHERE email = '". mysqli_real_escape_string( self :: get_connection_to_db() , $email ) . "'" ;
         
        $result = self :: run_query( $sql );

        if ( $result->num_rows > 0) {
        
            while($row = $result->fetch_assoc()) {
                return $row;
            }
        
        } else {
            return false;
        }

    }

    private static function get_connection_to_db(){

        if( self :: $connection === '' ){
            
            $conn =  mysqli_connect( HOSTNAME , USERNAME , PASSWORD , DEFAULTDB );
            self :: $connection = $conn ? $conn : '';
            
        }

        return self :: $connection;
    }

    public static function get_my_id(){
        return  ND_Session :: get( 'id' );
    }

    public static function get_trip_number ( $device_id = '' ) {

        ;

        if($device_id === '') return "Not Available";

        $sql = "SELECT id FROM tc_positions where deviceid = ". $device_id ." ORDER BY id DESC LIMIT 0, 1 ";

        $result = mysqli_fetch_assoc ( self :: run_query($sql) );

        return $result['id'];

    }

    public static function get_last_trip_id( $device_id ){
        return self :: get_trip_number( $device_id );
    }

    public static function get_trip_start_date( $device_id ){

        $trip_id = self :: get_last_trip_id( $device_id );

        if( empty( $trip_id ) ){
            return "Not Found";
        }
   

        $date_obj = self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'fixtime' ) );

        return $date_obj->format('d/m/Y');

    }

    public static function get_trip_start_time( $device_id ){

        $trip_id = self :: get_last_trip_id( $device_id );

        if( empty( $trip_id ) ){
            return "Not Found";
        }
   

        $date_obj = self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'fixtime' ) );

        return $date_obj->format('h:i A');

    }
    public static function get_trip_end_time( $device_id ){

        $trip_id = self :: get_last_trip_id( $device_id );

        if( empty( $trip_id ) ){
            return "Not Found";
        }
   

        $date_obj = self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'devicetime' ) );

        return $date_obj->format('h:i A');

    }
    public static function get_trip_end_date( $device_id ){

        $trip_id = self :: get_last_trip_id( $device_id );

        if( empty( $trip_id ) ){
            return "Not Found";
        }
   

        $date_obj = self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'devicetime' ) );

        return $date_obj->format('d/m/Y');

    }

    public static function get_total_distance_travelled($device_id ,$want_time = false){

        if( $want_time ){
            return self  :: get_total_time_taken( $device_id );
        }

        $trip_id = self :: get_last_trip_id( $device_id );

        return self :: get_positions_attribute( $trip_id , 'totalDistance' );
    }

    public static function get_datetime_obj($string , $format = 'Y-m-d H:i:s' ){
        return DateTime::createFromFormat( $format , $string );
    }

    public static function get_total_time_taken( $device_id ){
        
        $trip_id = self :: get_last_trip_id( $device_id );
        if( empty( $trip_id ) ){
            return "Not Found";
        }

        $datetime1 = self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'devicetime' ) );
        $datetime2 =  self :: get_datetime_obj( self :: get_positions_row( $trip_id , 'fixtime' ) );
        $interval = $datetime1->diff($datetime2);
        
        $elapsed = $interval->format('%h:%i');
        return $elapsed;


    }

    public static function get_total_time_taken_by_trip_ids( $first_trip_id , $last_trip_id ){
    

        $datetime1 = self :: get_datetime_obj( self :: get_positions_row( $first_trip_id , 'fixtime' ) );
        $datetime2 =  self :: get_datetime_obj( self :: get_positions_row( $last_trip_id , 'fixtime' ) );
        //return self :: get_positions_row( $last_trip_id , 'fixtime' );
        //$datetime2 = self :: get_datetime_obj( '2019-03-13 09:32:30' );
        //$datetime1 =  self :: get_datetime_obj( '2019-03-20 09:32:30' );

        if( false === $datetime1 OR false === $datetime2 ){
            return 'NA';
        }

        $interval = $datetime1->diff($datetime2);
        
         $hours =  $interval->h + ($interval->d * 24);
         $mins =  $interval->i;
        return "$hours:$mins";


    }

    public static function get_device_dropdown_array(){
        $dropdown_array = [];

        $device_ids = self :: get_my_devices();

        foreach( $device_ids as $device ){
            $dropdown_array[ $device ] = self :: get_device_name( $device );
        }

       // $dropdown_array = ['all'=>'All']  + $dropdown_array ;
        return $dropdown_array;
    }

    public static function get_device_start_date_row(  $device_id  , $from_date= ''  ){

       // $device_row = self :: get_device_row( $device_id );

        $from_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $from_date );

        $sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $from_date_obj->format('Y-m-d') ." 00:00:00' order by id DESC limit 0,1";

        $result = self :: run_query( $sql );

        if(! $result){
            return false;

        }

        while($row = $result->fetch_assoc()) {
            return $row;
        } 



    }

    private static function get_total_distance_travelled_after_date ( $device_id , $after_date, $want_time = false ){

        $from_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $after_date );
        $last_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $from_date_obj->format('Y-m-d') ." 00:00:00' order by id DESC limit 0,1";

        $start_day_first_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $from_date_obj->format('Y-m-d') ." 00:00:00' order by id ASC limit 0,1";


        $last_row_result = self :: run_query( $last_trip_row_sql );
        $start_day_first_trip_row_result = self :: run_query( $start_day_first_trip_row_sql );

        $last_row = $last_row_result->fetch_assoc();
        $start_day_first_trip_row = $start_day_first_trip_row_result->fetch_assoc();

        if($want_time){
            return self :: get_total_time_taken_by_trip_ids ( $start_day_first_trip_row['id'],  $last_row['id'] );
        }

        $start_day_first_row_total_distance  = self :: get_positions_attribute( $start_day_first_trip_row['id'] );

        $last_day_total_distance  = self :: get_positions_attribute( $last_row['id'] );

        //return  $start_day_first_trip_row['id'];
        return  number_format( (float) $last_day_total_distance - (float) $start_day_first_row_total_distance , 2, '.', '');
    }

    
    private static function get_total_distance_travelled_before_date ( $device_id , $before_date, $want_time = false ){

           

            $from_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $before_date );

            //if(! $from_date_obj) return false;

            $last_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime < '". $from_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00'  order by id DESC limit 0,1";

            $start_day_first_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime < '". $from_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00'  order by id ASC limit 0,1";


           // $start_day_first_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $from_date_obj->format('Y-m-d') ." 00:00:00' order by id ASC limit 0,1";
    
    
            $last_row_result = self :: run_query( $last_trip_row_sql );
            $start_day_first_trip_row_result = self :: run_query( $start_day_first_trip_row_sql );
    
            $last_row = $last_row_result->fetch_assoc();
            $start_day_first_trip_row = $start_day_first_trip_row_result->fetch_assoc();
    
            if($want_time){
                return self :: get_total_time_taken_by_trip_ids ( $start_day_first_trip_row['id'],  $last_row['id'] );
            }

            $start_day_first_row_total_distance  = self :: get_positions_attribute( $start_day_first_trip_row['id'] );
    
            $last_day_total_distance  = self :: get_positions_attribute( $last_row['id'] );
    
            return  number_format( (float) $last_day_total_distance - (float) $start_day_first_row_total_distance , 2, '.', '');
       

    }

    public static function get_total_distance_travelled_between_date ( $device_id , $after_date ='' , $before_date = '',$want_time = false ){

        

        if( ''=== trim($after_date) AND '' === trim($before_date) ){
            // No date range provided , so we will just call the original total distance calculator
           // return 'NO DATE';
            return self :: get_total_distance_travelled ( $device_id , $want_time );
        }

        if( ''=== trim( $after_date ) ){
            // No date range provided , so we will just call the original total distance calculator
           // return 'to date';
            return self :: get_total_distance_travelled_before_date ( $device_id , $before_date  , $want_time );
        }

        if( ''=== trim ( $before_date ) ){
           // return 'from date';
            // No date range provided , so we will just call the original total distance calculator
            return self :: get_total_distance_travelled_after_date ( $device_id , $after_date  , $want_time );
        }

        $after_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $after_date );

        $before_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $before_date );


        $start_day_first_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $after_date_obj->format('Y-m-d') ." 00:00:00'  order by id ASC limit 0,1";

        $last_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime < '". $before_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00'  order by id DESC limit 0,1";


       // $start_day_first_trip_row_sql = "select * from tc_positions where deviceid=$device_id AND fixtime > '". $from_date_obj->format('Y-m-d') ." 00:00:00' order by id ASC limit 0,1";


        $last_row_result = self :: run_query( $last_trip_row_sql );
        $start_day_first_trip_row_result = self :: run_query( $start_day_first_trip_row_sql );

        $last_row = $last_row_result->fetch_assoc();
        $start_day_first_trip_row = $start_day_first_trip_row_result->fetch_assoc();

        if($want_time){
            return self :: get_total_time_taken_by_trip_ids ( $start_day_first_trip_row['id'],  $last_row['id'] );
        }

        $start_day_first_row_total_distance  = self :: get_positions_attribute( $start_day_first_trip_row['id'] );

        $last_day_total_distance  = self :: get_positions_attribute( $last_row['id'] );

        return  number_format( (float) $last_day_total_distance - (float) $start_day_first_row_total_distance , 2, '.', '');
   

}

    

    // public static function get_total_distance_travelled_date_range( $device_id , $from_date='' , $to_date='' ){

        

     
    //     $from_row  = get_device_start_date_row( $device_id , $from_date );

    //     $to_row  = get_device_start_date_row( $device_id , $to_date );

    //     $to_date_obj = self :: get_datetime_obj( $to_date , 'd/m/Y' );

    //     }

    //     if( false === $from_date ){
    //         $from_date_obj  = new DateTime('now');
    //     }

    // }

    public static function get_device_end_date_row(  $device_id , $to_date = ''  ){

        $to_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $to_date );


        $sql = "select * from tc_positions where deviceid=$device_id AND fixtime < '". $to_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00'  order by id ASC limit 0,1";

        $result = self :: run_query( $sql );

        if(! $result){
            return false;
        }

        while($row = $result->fetch_assoc()) {
            return $row;
        }
       // $sql = "select devicetime from tc_positions where devicetime < '2019-03-15 00:00:00'"; // to + 1 day
        
    }
    public static function get_device_from_to_date(  $device_id ,$from_date='', $to_date = ''  ){

        $to_date_obj =  DateTime::createFromFormat( 'd/m/Y' , $to_date );


        $sql = "select * from tc_positions where deviceid=$device_id AND fixtime < '". $to_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00' AND AND fixtime < '". $to_date_obj->add( new DateInterval('P1D') )->format('Y-m-d') ." 00:00:00' order by id ASC limit 0,1";

        $result = self :: run_query( $sql );

        if(! $result){
            return false;
        }

        while($row = $result->fetch_assoc()) {
            return $row;
        }
        
    }

    private static function get_device_name($device_id){

        $sql = "SELECT * FROM tc_devices where id='". $device_id ."'";

        $result = self :: run_query( $sql );

        if(! $result){
            return 'NA';
        }

        while($row = $result->fetch_assoc()) {
            return $row['name'];
        }

    }

    public static function get_my_devices(){

        $device_array = [];

        $table_name = self :: $user_to_device_table;
        $user_id = self :: get_my_id();

        $sql = "SELECT *  FROM $table_name WHERE userid = $user_id";

        
        $result = self :: run_query( $sql );

        if(! $result){
            return $device_array;
        }

        while($row = $result->fetch_assoc()) {
            $device_array[]=$row['deviceid'];
        }

        return array_unique ( $device_array );

    }

    private static function run_query( $sql ){
        $conn = self :: get_connection_to_db();

        return $conn->query($sql);
    }

    public static function get_group_name($device_id = ''){
        
        if($device_id === '') return "Not Available";

        $sql = "select tc_devices.id as device_id, tc_devices.groupid as groupid ,tc_groups.name as group_name from tc_devices INNER JOIN tc_groups ON tc_devices.groupid=tc_groups.id and tc_devices.id = " . $device_id;

        $result = mysqli_fetch_assoc ( self :: run_query($sql) );

        return $result['group_name'];



    }

    public static function get_linked_driver_id( $device_id = '' ){
       
        if($device_id === '') return "Not Available";

        $sql = "select * from tc_device_driver where deviceid = " . $device_id ;
        $result = mysqli_fetch_assoc ( self :: run_query($sql) );

        return $result['driverid'];

    }

    public static function get_driver_row( $driver_id = 0,$col = ''){
        $sql = "select * from tc_drivers where id = " . $driver_id ;
        $result = self :: run_query($sql);
        if( ! $result ) return false;

        $result = mysqli_fetch_assoc ( $result );

        if( '' === $col ){
            return $result;
        }

        return $result[ $col ] === '' ? 'Not Found': $result[ $col ] ;
    }

    public static function get_positions_row( $trip_id , $col = '' ){
        $sql = "select * from tc_positions where id = " . $trip_id ;
        $result = self :: run_query( $sql );
        if( ! $result ) return false;

        $result = mysqli_fetch_assoc ( $result );

        if( '' === $col ){
            return $result;
        }

        return $result[ $col ] === '' ? 'Not Found': $result[ $col ] ;
    }

    public static function get_device_row( $device_id = 0, $col = ''){
        $sql = "select * from tc_devices where id = " . $device_id ;
        $result = self :: run_query($sql);
        if( ! $result ) return false;

        $result = mysqli_fetch_assoc ( $result );

        if( '' === $col ){
            return $result;
        }

        return $result[ $col ];
    }

    public static function get_driver_attribute( $driver_id , $attribute_name='Origin'){

        $data = self :: get_driver_row( $driver_id );
        if( false === $data ) return 'Not Found';
       

        //return json_encode($data);
        $attribute = (array)json_decode( $data['attributes'] );

        return isset( $attribute[ $attribute_name ] ) ? $attribute[ $attribute_name ] : 'Not Found';

    }

    public static function get_positions_attribute( $trip_id , $attribute_name='totalDistance'){

        $data = self :: get_positions_row( $trip_id );
        if( false === $data ) return 'Not Found';
       

        //return json_encode($data);
        $attribute = (array)json_decode( $data['attributes'] );

        return isset( $attribute[ $attribute_name ] ) ? $attribute[ $attribute_name ] : 'Not Found';

    }

}