<?php

// defining databas in constants

defined('DB_HOST') ? null : define('DB_HOST', 'localhost');

defined('DB_USER') ? null : define('DB_USER', 'root');

defined('DB_PASS') ? null : define('DB_PASS', '');

defined('DB_NAME') ? null : define('DB_NAME', 'modist');

$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); // db connection
// Check connection
if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    exit();
}

// helper functions for database queries

function escape($string)
{
    global $con;
    return mysqli_real_escape_string($con, $string);
}
// execute query
  function query($query)
  {
      global $con;
      return mysqli_query($con, $query);
  }
// check if error in query
  function confirm($result)
  {
      global $con;
      if (!$result) {
          die('QUERY FAILED' . mysqli_error($con));
      }
  }
// row count of record
  function row_count($result)
  {
      return  mysqli_num_rows($result);
  }



  function fetch_array($result)
  {
      global $con;
      return mysqli_fetch_array($result);
  }


?>
