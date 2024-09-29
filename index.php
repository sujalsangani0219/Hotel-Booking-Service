<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_NUMBER_INT);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_NUMBER_INT);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_NUMBER_INT);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked already!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }
   }
}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message sent successfully!';
   }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/Styles.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="WebSites_Image/Room.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Rooms</h3>
               <a href="#availability" class="btn">Check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="WebSites_Image/Bar.jpg" alt="">
            <div class="flex">
               <h3>Foods and Drinks</h3>
               <a href="#reservation" class="btn">Make a Reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="WebSites_Image/Hall.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Halls</h3>
               <a href="#contact" class="btn">Contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<section class="about" id="about">

<div class="row">
   <div class="image">
      <img src="WebSites_Image/Staff1.jpg" alt="">
   </div>
   <div class="content">
      <h3>Best Staff</h3>
      <p>Leadership is creating an environment in which people want to be part of the organization and not just work for the organization.</p>
      <a href="#reservation" class="btn">make a reservation</a>
   </div>
</div>

<div class="row revers">
   <div class="image">
      <img src="WebSites_Image/about-img-2.jpg" alt="">
   </div>
   <div class="content">
      <h3>Best Foods</h3>
      <p>Food is the most universal language, and everyone can understand it.</p>
      <a href="#contact" class="btn">contact us</a>
   </div>
</div>

<div class="row">
   <div class="image">
      <img src="WebSites_Image/Swim.jpg" alt="">
   </div>
   <div class="content">
      <h3>Swimming Pool</h3>
      <p>Swimming is a sport that requires dedication, perseverance, and hard work. But the rewards are worth it.</p>
      <a href="#availability" class="btn">check availability</a>
   </div>
</div>

</section>

<section class="services">

   <div class="box-container">

   <div class="box">
              <img src="WebSites_Image/icon-1.png" alt="">
              <h3>Food & Drinks</h3>
              <p>To me, food is as much about the moment, the occasion, the location and the company as it is about the taste.</p>
           </div>

           <div class="box">
              <img src="WebSites_Image/icon-2.png" alt="">
              <h3>Outdoor Dining</h3>
              <p>Outdoor dining is a great way to enjoy the beauty of nature while savoring a delicious meal.</p>
           </div>
     
           <div class="box">
              <img src="WebSites_Image/icon-3.png" alt="">
              <h3>Beach View</h3>
              <p>The blue sky and the sea meet on the horizon - a magical line which has been the theme of innumerable poems.</p>
           </div>
     
           <div class="box">
              <img src="WebSites_Image/icon-4.png" alt="">
              <h3>Decorations</h3>
              <p>The blue sky and the sea meet on the horizon - a magical line which has been the theme of innumerable poems.</p>
           </div>
     
           <div class="box">
              <img src="WebSites_Image/icon-5.png" alt="">
              <h3>Swimming Pool</h3>
              <p>The water is my happy place. It's where I go to escape, to relax, and to recharge.</p>
           </div>
     
           <div class="box">
              <img src="WebSites_Image/icon-6.png" alt="">
              <h3>Resort Beach</h3>
              <p>The beach is not just a place, it's a feeling.</p>
           </div>
     

   </div>

</section>

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>Make a Reservation</h3>
      <div class="flex">
         <div class="box">
            <p>Your Name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>Your Email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>Your Number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="WebSites_Image/Gallary1.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary2.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary3.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary4.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary5.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary6.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary7.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary8.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary9.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary10.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary11.jpg" class="swiper-slide" alt="">
         <img src="WebSites_Image/Gallary6.jpg" class="swiper-slide" alt="">            </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>Send us Message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
              <h3 class="title">Frequently Asked Questions</h3>
              <div class="box active">
                 <h3>How to Cancel?</h3>
                 <p>Cancle Process Help For The Contact Of Hotel And Bank.</p>
              </div>
              <div class="box">
                 <h3>Is There Any Vacancy?</h3>
                 <p>24x7 Vacancy Avialability.</p>
              </div>
              <div class="box">
                 <h3>What Are Payment Methods?</h3>
                 <p>Payment Method Like Online, Cash.</p>
              </div>
              <div class="box">
                 <h3>How to Claim Coupons Codes?</h3>
                 <p>Reddem The Coupon Code</p>
              </div>
              <div class="box">
                 <h3>What Are The Age Requirements?</h3>
                 <p>Age Requirements For Child and under 18 age Ducument For Adharcard.</p>
              </div>
           </div>

   </div>

</section>

<section class="reviews" id="reviews">
     
     <div class="swiper reviews-slider">
  
        <div class="swiper-wrapper">
           <div class="swiper-slide box">
              <img src="WebSites_Image/m1.jpg" alt="">
              <h3>Shubham Sanjaybhai Sangani</h3>
              <p>High expectations are the key to absolutely everything.</p>
           </div>
           <div class="swiper-slide box">
              <img src="WebSites_Image/m4.png" alt="">
              <h3>Sujal Sanjaybhai Sangani</h3>
              <p>High expectations are the key to absolutely everything.</p>
           </div>
           <div class="swiper-slide box">
              <img src="WebSites_Image/m2.png" alt="">
              <h3>Jash Rajubhai Aswani</h3>
              <p>A man is but the product of his thoughts. What he thinks, he becomes.</p>
           </div>
           <div class="swiper-slide box">
              <img src="WebSites_Image/m3.png" alt="">
              <h3>Naitik Rajput</h3>
              <p>A man is but the product of his thoughts. What he thinks, he becomes.</p>
           </div>
        </div>
  
        <div class="swiper-pagination"></div>
     </div>
  
  </section>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>