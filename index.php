<?php
error_reporting(0);

session_start(); // this MUST be called prior to any output including whitespaces and line breaks!

$GLOBALS['DEBUG_MODE'] = 0;
// CHANGE TO 0 TO TURN OFF DEBUG MODE
// IN DEBUG MODE, ONLY THE CAPTCHA CODE IS VALIDATED, AND NO EMAIL IS SENT

$GLOBALS['ct_recipient']   = 'imtasw@gmail.com'; // Change to your email address!  Make sure DEBUG_MODE above is 0 for mail to send!
$GLOBALS['ct_msg_subject'] = 'Enquiry Form Authentification (BAWASLU Kepri)';
?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="float-panel.js"></script>
<link href="demo2.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" /> 
<link rel="stylesheet" type="text/css" href="assets/animate.css" />
<link rel="stylesheet" type="text/css" href="assets/demo.css" />


<?php include "meta.php"; ?>

<title><?php include "title.php"; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css" type="text/css"> 
<link rel="stylesheet" href="mobile.css" type="text/css"> 
<link href="imagehover.css" rel="stylesheet" media="all">
<link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,500" rel="stylesheet">

<link rel="stylesheet" href="respon_menu/css/style.css">

<link href="scroll_large/tutorsty.css" rel="stylesheet" type="text/css" />
<link href="scroll_large/flexcrollstyles.css" rel="stylesheet" type="text/css" />
<script type='text/javascript' src="scroll_large/flexcroll.js"></script>

    <link href="news_ticker_plug/css/prism.css" rel="stylesheet" />
    <link href="news_ticker_plug/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="logo_slide/carousel.css">
 <script src='respon_menu/js/jquery.min.js'></script>
 <script src="logo_slide/slick.js" type="text/javascript" charset="utf-8"></script>
 
	<script type="text/javascript" src="dist/wa-mediabox.min.js"></script>
	<link rel="stylesheet" href="dist/wa-mediabox.min.css" />
    <script src="sliders/js/jquery-1.11.3.min.js" type="text/javascript"></script>
	
    <link href="accordion_menu/accordion-menu.css" rel="stylesheet" />
    <script src="accordion_menu/accordion-menu.js"></script>

</head>

<body>
<div id="container" align="center">
	<header class="outer page-header"> 
		<?php include "header.php"; ?>
	</header>
	
	<div id="bg-inside-header"></div>
	
              <?php include "koneksi.php";
					//$id = $_GET['id'];
					$id = isset($_GET['id']) ? mysqli_real_escape_string($sambung, $_GET['id']) : 0;
					$module = isset($_GET['module']) ? mysqli_real_escape_string($sambung, $_GET['module']) : '';
					//$module = $_GET['module'];
							if ($id=="" || $id=="1") {
							$perintah="select * from content where id='1' && publish='Yes'";
							$hasil=mysqli_query($sambung, $perintah);
							$baris=mysqli_fetch_array($hasil,MYSQLI_ASSOC); 
							include "home.php";
							}
							
							else { $perintah="select * from content where id='$id' && publish='Yes'"; 
							$hasil=mysqli_query($sambung, $perintah);
							$baris=mysqli_fetch_array($hasil,MYSQLI_ASSOC);
								if ($id==$baris[id]) {
								include "content.php";
								} else {
								echo "page not found<br><meta http-equiv=refresh content=0;URL=index.php>";
								}
							}
							?>
	
</div>


	<div align="center" id="footer-spot">
		<div align="center" id="spot-row">
		  <div align="center" id="footer-spot-col" class="slideanim link_light">
			  <strong style="font-size:24px;">Tentang Kami :</strong><br><br>
			  <?php
				  include "profile_footer.php";
			  ?>
		  </div>
		  
		  <div align="center" id="footer-spot-col" class="slideanim link_light">
			  <strong style="font-size:24px">Publikasi : </strong><br /><br />
			  <?php
				  include "publikasi_footer.php";
			  ?>
		  </div>
			
		  <div align="center" id="footer-spot-col" class="slideanim link_light">
			  <strong style="font-size:24px">Website Terkait : </strong><br /><br />
			  <?php
				  include "link_footer.php";
			  ?>
		  </div>
			
		  <div align="center" id="footer-spot-col" class="slideanim link_light">
			  <strong style="font-size:24px">Kontak : </strong><br /><br>
			  
	      Badan Pengawas Pemilihan Umum Provinsi Kepulauan Riau<br>
	        Jl. WR. Supratman No. 4-7 Tanjungpinang<br>
	        Telepon/Fax: 0771 - 4444074
			<br>
			<a href='https://taucheruhrdirekt.com/'><span style="color:orange; opacity:0;">TAUCHERUHREN direkt</span></a>
 <script type='text/javascript' src='https://www.freevisitorcounters.com/auth.php?id=f27be569ca0b64091cf7325eadc50e30f208c1e5'></script>
<script type="text/javascript" src="https://www.freevisitorcounters.com/en/home/counter/489495/t/1"></script>

		  </div>
		</div>
	</div>

	<div id="footer">
		<div align="center" style="padding:15px; color:#FFFFFF;">copyright © 2019 all right reserved</div>
	</div>
	
	
    <script src="news_ticker_plug/js/jquery.mCustomScrollbar.min.js"></script>
    <script src="news_ticker_plug/jquery.newsTicker.js"></script>
    <script>
    		$('a[href*=#]').click(function(e) {
			    var href = $.attr(this, 'href');
			    if (href != "#") {
				    $('html, body').animate({
				        scrollTop: $(href).offset().top - 81
				    }, 500);
				}
				else {
					$('html, body').animate({
				        scrollTop: 0
				    }, 500);
				}
			    return false;
			});

    		$(window).load(function(){
	            $('code.language-javascript').mCustomScrollbar();
	        });
            var nt_title = $('#nt-title').newsTicker({
                row_height: 20,
                max_rows: 1,
                duration: 3000,
                pauseOnHover: 1
            });
        </script>
	
<script>
(function($){
	$(function(){	
      // scroll is still position
			var scroll = $(document).scrollTop();
			var headerHeight = $('.page-header').outerHeight();
			//console.log(headerHeight);
			
			$(window).scroll(function() {
				// scrolled is new position just obtained
				var scrolled = $(document).scrollTop();
								
				// optionally emulate non-fixed positioning behaviour
			
				if (scrolled > headerHeight){
					$('.page-header').addClass('off-canvas');
				} else {
					$('.page-header').removeClass('off-canvas');
				}

			    if (scrolled > scroll){
			         // scrolling down
					 $('.page-header').removeClass('fixed');
			      } else {
					  //scrolling up
					  $('.page-header').addClass('fixed');
			    }				
				 
				scroll = $(document).scrollTop();	
			 });
    
    
 	});
})(jQuery);   
</script>


	
	<script type="text/javascript">
	jQuery.noConflict();
	</script>

	
<script src="assets/viewportchecker.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.post').addClass("hidden").viewportChecker({
	    classToAdd: 'visible animated fadeInDown', // Class to add to the elements when they are visible
	    offset: 150    
	   });   
});            
</script>

<script type="text/javascript">
$(document).ready(function(){
	
	//Check to see if the window is top if not then display button
	$(window).scroll(function(){
		if ($(this).scrollTop() > 100) {
			$('.scrollToTop').fadeIn();
		} else {
			$('.scrollToTop').fadeOut();
		}
	});
	
	//Click event to scroll to top
	$('.scrollToTop').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
	
});
</script>	
<a href="#" class="scrollToTop"></a>

	<script type="text/javascript">
		$(document).ready(function(){
			$('.customer-logos').slick({
				slidesToShow: 5,
				slidesToScroll: 1,
				autoplay: true,
				autoplaySpeed: 1700,
				arrows: false,
				dots: false,
					pauseOnHover: false,
					responsive: [{
					breakpoint: 768,
					settings: {
						slidesToShow: 4
					}
				}, {
					breakpoint: 520,
					settings: {
						slidesToShow: 2
					}
				}]
			});
		});
	</script>


</body>
</html>
