<?php defined('SYSPATH') or die('No direct script access.');

return array(
     'Ajax/User' => array(
           "roles_users.role_id" => array(
                'col' => "roles_users.role_id",
                'met' => 'IN',
                'val' => "DB::expr@(#val)"
            ),
        ),
    
    
    
     
   
);
