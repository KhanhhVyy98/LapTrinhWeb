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
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

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
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

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
         $warning_msg[] = 'room booked alredy!';
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
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/Caravelle-Saigon-Deluxe-rooms.jpg" alt="">
            <div class="flex">
               <h3>Deluxe Rooms</h3>
               <a href="#reservation" class="btn">Đặt phòng ngay</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/Caravelle-Saigon-Signature-Premium.jpg" alt="">
            <div class="flex">
               <h3>Signature Premium</h3>
               <a href="#reservation" class="btn">Đặt phòng ngay</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/Caravelle-Saigon-Two-Bedroom-Suite-2.jpg" alt="">
            <div class="flex">
               <h3>Two Bedroom Suite</h3>
               <a href="#reservation" class="btn">Đặt phòng ngay</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/Caravelle-Saigon-Presidential-Suite-1.jpg" alt="">
            <div class="flex">
               <h3>Presidential Suite</h3>
               <a href="#reservation" class="btn">Đặt phòng ngay</a>
            </div>
         </div>
      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Ngày đến <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Ngày đi <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Người lớn <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 Người lớn</option>
               <option value="2">2 Người lớn</option>
               <option value="3">3 Người lớn</option>
               <option value="4">4 Người lớn</option>
               <option value="5">5 Người lớn</option>
               <option value="6">6 Người lớn</option>
            </select>
         </div>
         <div class="box">
            <p>Trẻ em <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 Trẻ em</option>
               <option value="1">1 Trẻ em</option>
               <option value="2">2 Trẻ em</option>
               <option value="3">3 Trẻ em</option>
               <option value="4">4 Trẻ em</option>
               <option value="5">5 Trẻ em</option>
               <option value="6">6 Trẻ em</option>
            </select>
         </div>
         <div class="box">
            <p>Phòng <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 Phòng </option>
               <option value="2">2 Phòng </option>
               <option value="3">3 Phòng </option>
               <option value="4">4 Phòng </option>
               <option value="5">5 Phòng </option>
               <option value="6">6 Phòng </option>
            </select>
         </div>
      </div>
      <input type="submit" value="Kiểm tra tình trạng phòng" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/1.png" alt="">
      </div>
      <div class="content">
         <h3>Nhân viên xuất sắc</h3>
         <p>Đến với Caravelle, bạn luôn có môt đội ngũ nhân viên chuyên nghiệp và xuất sắc sẽ luôn bên cạnh hỗ trợ đến bạn.
         Hãy đến Caravelle để trải nghiệm ngay lập tức.</p>
         <a href="#reservation" class="btn">Đặt phòng tại đây</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/2.png" alt="">
      </div>
      <div class="content">
         <h3>Ẩm thực</h3>
         <p>Đắm chìm vào cuộc hành trình ẩm thực khác biệt tại Caravelle Saigon, nơi ẩm thực hiện đại hòa quyện với những nét tinh hoa đương đại.</p>
         <a href="#contact" class="btn">Liên hệ với chúng tôi</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>Dịch vụ</h3>
         <p>Hồ bơi tại Caravelle là nơi lý tưởng để nhâm nhi một ly nước mát lạnh và làm một vài vòng bơi sau ngày dài làm việc hoặc khám phá thành phố.</p>
         <a href="#availability" class="btn">Kiểm tra tình trạng</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/3.png" alt="">
         <h3>Nhà hàng</h3>
         <p>Nhà hàng mang phong cách ẩm thực fine-dining với thực đơn đặc sắc và tinh tế, tọa lạc tại tầng 3 khách sạn với góc nhìn ra thành phố về đêm lung linh.</p>
      </div>

      <div class="box">
         <img src="images/4.png" alt="">
         <h3>Café</h3>
         <p>Café de l’Opera có lẽ là địa điểm tuyệt vời nhất tại khách sạn nếu Quý khách yêu thích không gian ấm cúng để thưởng thức một thực đơn nước, bánh ngọt và món ăn nhẹ ngon miệng.</p>
      </div>

      <div class="box">
         <img src="images/8.png" alt="">
         <h3>Spa</h3>
         <p>KARA Spa toạ lạc trên một không gian yên tĩnh và tách biệt rộng 750 mét vuông tại tầng 7 khách sạn Caravelle Saigon mang đến cho du khách trải nghiệm sức khỏe chất lượng cao với các gói trị liệu chăm sóc sức khoẻ đa dạng. </p>
      </div>

      <div class="box">
         <img src="images/7.png" alt="">
         <h3>Hồ bơi</h3>
         <p> Không gian kết hợp hài hòa bóng mát và ánh nắng của khu vực hồ bơi và xung quanh là địa điểm tuyệt vời trong khách sạn để Quý khách thư giãn.</p>
      </div>

      <div class="box">
         <img src="images/6.png" alt="">
         <h3>Tổ chức sự kiện</h3>
         <p>Mười không gian sự kiện của Caravelle luôn được chú trọng lắp đặt hệ thống âm thanh ánh sáng hiện đại cùng nội thất đơn giản mà đẹp mắt. </p>
      </div>

      <div class="box">
         <img src="images/5.png" alt="">
         <h3>Dịch vụ đưa đón</h3>
         <p>Đội xe Mercedes luôn sẵn sàng phục vụ khách lưu trú di chuyển tại thành phố Hồ Chí Minh. 
         Luôn sẵn sàng đưa đón, di chuyển để tham quan thành phố, Quý khách có thể yên tâm về sự an toàn, tiện lợi và tối ưu về mặt thời gian phù hợp yêu cầu đưa ra.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>Đặt phòng tại đây</h3>
      <div class="flex">
         <div class="box">
            <p>Họ và tên Khách hàng <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>Địa chỉ Email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>Số điện thoại <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>Phòng <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 phòng</option>
               <option value="2">2 phòng</option>
               <option value="3">3 phòng</option>
               <option value="4">4 phòng</option>
               <option value="5">5 phòng</option>
               <option value="6">6 phòng</option>
            </select>
         </div>
         <div class="box">
            <p>Ngày đến <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Ngày đi <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Người lớn <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 người lớn</option>
               <option value="2">2 người lớn </option>
               <option value="3">3 người lớn </option>
               <option value="4">4 người lớn </option>
               <option value="5">5 người lớn </option>
               <option value="6">6 người lớn</option>
            </select>
         </div>
         <div class="box">
            <p>Trẻ em <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 trẻ em</option>
               <option value="1">1 trẻ em</option>
               <option value="2">2 trẻ em</option>
               <option value="3">3 trẻ em</option>
               <option value="4">4 trẻ em</option>
               <option value="5">5 trẻ em</option>
               <option value="6">6 trẻ em</option>
            </select>
         </div>
      </div>
      <input type="submit" value="Đặt phòng ngay" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/ks1.jpg" class="swiper-slide" alt="">
         <img src="images/ks2.jpg" class="swiper-slide" alt="">
         <img src="images/ks3.jpg" class="swiper-slide" alt="">
         <img src="images/ks4.jpg" class="swiper-slide" alt="">
         <img src="images/ks5.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>Gửi tin nhắn đến cho chúng tôi</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Nhập tên của bạn tại đây" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="Nhập địa chỉ email của bạn tại đây" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Nhập số điện thoại của bạn tại đây" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="Gửi lời nhắn đến chúng tôi" cols="30" rows="10"></textarea>
         <input type="submit" value="Gửi tin nhắn" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">Những câu hỏi thường gặp</h3>
         <div class="box active">
            <h3>Làm sao để hủy phòng đã đặt?</h3>
            <p>Bạn cần liên hệ với nhân viên lễ tân để được hướng dẫn.</p>
         </div>
         <div class="box">
            <h3>Còn chổ trống nào không?</h3>
            <p>Bạn có thể kiểm tra phòng trống phù hợp với yêu cầu của bạn tại mục "Kiểm tra tình trạng phòng".</p>
         </div>
         <div class="box">
            <h3>Phương thức thanh toán</h3>
            <p>Chúng tôi áp dụng mọi phương thức thanh toán. Lưu ý khi đặt phòng hãy đặt cọc qua thẻ.</p>
         </div>
         <div class="box">
            <h3>Làm sao nhận được các ưu đãi?</h3>
            <p>Chúng tôi sẽ cập nhật các chương trình khuyến mãi, ưu đãi trên trang website của khách sạn.</p>
         </div>
         <div class="box">
            <h3>Độ tuổi được yêu cầu khi ở khách sạn?</h3>
            <p>Lưu ý với trẻ em dưới 3 tuổi phải đi kèm với 1 phụ huynh.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/fb1.jpg" alt="">
            <h3>Ca sĩ Đoan Trang và gia đình</h3>
            <p>Ca sĩ Đoan Trang đã lựa chọn Caravelle Saigon trong thời gian lưu trú tại Thành phố Hồ Chí Minh và chúng tôi rất vinh dự khi Mrs. Đoan Trang cùng gia đình đã có một kỳ nghỉ tuyệt vời tại Caravelle Saigon.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/fb2.jpg" alt="">
            <h3>Hoa hậu Trái Đất - Ms. Mina Sue Choi </h3>
            <p>Ms. Mina Sue Choi đã lựa chọn Caravelle Saigon trong thời gian lưu trú tại Thành phố Hồ Chí Minh để chuẩn bị cho cuộc thi Hoa hậu Trái đất Việt Nam 2023. Và chúng tôi cũng rất vinh dự khi Hoa hậu Trái đất 2022 đã có những trải nghiệm đáng nhớ ngay giữa lòng Sài Gòn</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/fb3.jpg" alt="">
            <h3>Hoa hậu Việt Nam - Đỗ Thị Hà</h3>
            <p>Đỗ Thị Hà đã có những khoảnh khắc đáng nhớ dưới ánh hoàng hôn tại Signature Lounge, với quang cảnh ôm trọn nhà hát Thành Phố tuyệt đẹp. </p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>