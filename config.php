<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
/*********************************************************************/
$servername = "localhost"; 
$username = "root";
$password = ""; 
$db = "testing123"; 
$conn = new mysqli($servername, $username, $password,$db);
/********************************************************************/
$mail_email = "contact@alokitoscouts.com"; // your business email
$mail_pass = "sadi9507@#";   // replace with the actual password
$mail_host = "mail.alokitoscouts.com";     // SMTP server from your cPanel
$mail_sender = "Alokito Scouts";           // company or sender name

/*****************************************************************/
$order_mail_receiver="getorderemail@gmail.com";
/*******************************************************************/
$key_id = "rzp_test_OXruRqJ6qMIrKK";
$key_secret = "3xUG6cNN3JbWwiS7J6FbmX5p";
/*******************************************************************/
$_state_ = array("Andaman and Nicobar", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman & Diu", "Delhi", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Orissa", "Pondicherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Tripura", "Uttar Pradesh", "Uttaranchal", "West Bengal");




?>