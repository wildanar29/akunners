<?php  
  
  return [  
      'secret' => env('JWT_SECRET'),  
      'ttl' => 1440, // Waktu hidup token dalam menit  
      'algo' => 'HS256', // Algoritma yang digunakan  
  ];  
