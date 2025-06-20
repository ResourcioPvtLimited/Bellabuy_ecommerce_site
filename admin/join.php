<!DOCTYPE html>
<html>
   <head>
  
      <?php require "inc/head.php";?>
      <title>Join Us</title>
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
                     <h1 class="fs-4 card-title fw-bold mb-4">Join Us</h1>
                    <form method="POST" class="join_form" novalidate="" autocomplete="off">
    <div class="mb-3">
        <label class="mb-2 text-muted" for="shopName">Shop Name</label>
        <input type="email" class="form-control name" name="shopName" required autofocus>
    </div>
    <div class="mb-3">
        <label class="mb-2 text-muted" for="email">E-Mail Address</label>
        <input type="email" class="form-control email" name="email" value="" required autofocus>
    </div>
    <div class="mb-3">
        <label class="mb-2 text-muted" for="password">Password</label>
        <input type="password" class="form-control pass" name="password" required autofocus>
    </div>
    <div class="mb-3">
        <label class="mb-2 text-muted" for="phone">Phone</label>
        <input type="password" class="form-control phone" name="phone"  required autofocus>
    </div>
    <div class="mb-3">
        <label class="mb-2 text-muted" for="address">Shop Address</label>
        <textarea class="form-control adr" placeholder="Address"></textarea>
    </div><div class="mb-3">
        <label class="mb-2 text-muted" for="address">Latitues</label>
        <textarea class="form-control lat" placeholder="Address"></textarea>
    </div><div class="mb-3">
        <label class="mb-2 text-muted" for="address">Longitues</label>
        <textarea class="form-control lon" placeholder="Address"></textarea>
    </div>
    <div class="mb-3">
        <a href="login.php">Already have an account? Login!</a>
    </div>
    <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary ms-auto">
            Join
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