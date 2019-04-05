<?php
class ND_User{

    private static $connection = '';

    private static $users_table = 'tc_users';
    private static $user_to_device_table = 'tc_user_device';
   // private static $user_to_device_table = 'tc_user_device';

    public static function attempt_login( $username,$token ){
        
        $sql = "SELECT *  FROM ". self :: $users_table ." WHERE email = '". mysqli_real_escape_string( self :: get_connection_to_db() , $username ) ."' AND `token` = '". mysqli_real_escape_string( self :: get_connection_to_db() , $token ) ."'";
        
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

}