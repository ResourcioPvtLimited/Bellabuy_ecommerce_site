<?php
require "../config.php";
if(!isset($_SESSION['type'])){
   header('Location:login.php');
}
?>
<header class="topbar" data-navbarbg="skin5">
         <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5" style="background:#1f262d!important">
               <a class="navbar-brand" href="index.html">
               <img src="../assets/images/logo.png" style="width: 135px;">
               </a>
            </div>
            <div
               class="navbar-collapse collapse"
               id="navbarSupportedContent"
               data-navbarbg="skin5"
               >
               <ul class="navbar-nav float-start me-auto">
                  <li class="nav-item d-none d-lg-block">
                     <a
                        class="nav-link sidebartoggler waves-effect waves-light"
                        ><i class="mdi mdi-menu font-24"></i
                        ></a>
                  </li>
               </ul>
            </div>
         </nav>
      </header>