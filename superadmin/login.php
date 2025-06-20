<!DOCTYPE html>
<html>
   <head>
      
      <?php require "inc/head.php";?>
      <title>Login</title>
   </head>
   <body>
<section class="h-100">
      <div class="container h-100">
         <div class="row justify-content-sm-center h-100">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9">
               <div class="text-center my-5">
                 
                  <img src="../assets/images/logo.png" alt="logo" width="200">
               </div>
               <div class="card shadow-lg">
                  <div class="card-body p-5">
                     <h1 class="fs-4 card-title fw-bold mb-4">Login</h1>
                     <form method="POST" class="login_form" novalidate="" autocomplete="off" >
                        <div class="mb-3">
                           <label class="mb-2 text-muted" for="email">E-Mail Address</label>
                           <input id="email" type="email" class="form-control email" name="email" value="" required autofocus>
                           
                        </div>
                        <div class="mb-3">
                           <label class="mb-2 text-muted" for="email">Password</label>
                           <input id="pass" type="password" class="form-control pass" name="pass" value="" required autofocus>
                           
                        </div>
                        

                        
                        

                        <div class="d-flex align-items-center">
                           
                           <button type="submit" class="btn btn-primary ms-auto">
                              Login
                           </button>
                        </div>
                     </form>
                  </div>
                  
               </div>
               <div class="text-center mt-5 text-muted">
                  Copyright &copy; &mdash; Food Express
               </div>
            </div>
         </div>
      </div>
   </section>
   </body>
</html>