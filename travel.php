<?php
header('Content-type: text/html; charset=utf-8');   //使用萬用字元碼utf-8
include_once("mysql.php");                          // 連結資料庫new
$Table_file="file";                 // 取file資料表(影響：Taichung按鈕，Tainan按鈕，delete按鈕)
$Table_user="user";                 // 取user資料表(影響：edit按鈕)
$Table_file2="file2";               // 取file2資料表(影響：Taichung按鈕，Tainan按鈕，delete按鈕)
$Table_dstaddress="dstaddress";     // 取dstaddress資料表(影響：map呈現中心點)

session_start();    // 啟動session(使用：$_SESSION['userName']，$_SESSION["ds"])


// 點選"Tainan按鈕"  
if(isset($_POST['tain']))
{
   $_SESSION["ds"]=1;       // 設$_SESSION["ds"]為1
   
   //從dstaddress取Tainan的經緯度資料
   $result3=mysqli_query($conn,"SELECT *FROM $Table_dstaddress 
                                WHERE d=1");
}
// 點選"Taichung按鈕"
else
{
   $_SESSION["ds"]=0;       // 設$_SESSION["ds"]為0
   
   //從dstaddress取Taichung的經緯度資料
   $result3=mysqli_query($conn,"SELECT *FROM $Table_dstaddress 
                                WHERE d=0");
}    

$row3=mysqli_fetch_array($result3); // 取資料
$lat = $row3['lat'];                // 取經度
$lng = $row3['lng'];                // 取緯度
$mark = $row3['mark'];              // 取標記
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Life is Travel.</title>
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/animate/animate.css" />
<link rel="stylesheet" href="assets/animate/set.css" />
<link rel="stylesheet" href="assets/gallery/blueimp-gallery.min.css">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="assets/style.css">

<!-- Map  -->
<script>
function initMap() 
{
    // 地圖中心位置
    var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center:  new google.maps.LatLng('<?php echo $lat ?>','<?php echo $lng?>')
    });
    
    // 地圖標記位置
    var marker = new google.maps.Marker({
    		position : new google.maps.LatLng('<?php echo $lat ?>','<?php echo $lng?>')
    });
		
	marker.setMap(map);     // 標記設定

	// 標記訊息顯示
	var infowindow = new google.maps.InfoWindow({
			content : '<?php echo $mark ?>'
	});
    
    // 開啟地圖、標記
	infowindow.open(map, marker);
	// 將地址轉換成經緯度座標
    var geocoder = new google.maps.Geocoder();
  
    // 點選Search按鈕，呼叫geocodeAddress function  
    document.getElementById('submit').addEventListener('click', function() {
        geocodeAddress(geocoder, map);
    });
}


function geocodeAddress(geocoder, resultsMap) 
{
    // 取得得到的地址
    var address = document.getElementById('address').value;
    geocoder.geocode({'address': address}, function(results, status) {
    
    //  順利解析得到的地址，回傳地理編碼，印出新標記
    if (status == google.maps.GeocoderStatus.OK) 
    {
        resultsMap.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: resultsMap,
            position: results[0].geometry.location
        });
    }
    // 未順利找到該地址
    else 
    {
      alert('Geocode was not successful for the following reason: ' + status);
    }
    });
}
</script>

<!-- API key--> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJHpvTDXyRQ_vf2DLT1tlFytkLSB2WbPQ&signed_in=true&callback=initMap"
async defer></script>

</head>


<body>
<div class="topbar animated fadeInLeftBig"></div>

<!-- Header Starts -->
<div class="navbar-wrapper">
  <div class="container">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="top-nav">
      <div class="container">
          
        <!-- 顯示使用者名稱 -->
        <a class="navbar-brand active"><h2><?php echo $_SESSION["userName"];?></h2></a>
      
            <div class="navbar-collapse  collapse">
              <ul class="nav navbar-nav navbar-right">
                
                 <li ><a href="index.php">Home</a></li>
                 <li ><a href="view.php">View</a></li>
                 <li class="active"><a href="travel.php">My Travel</a></li>
                 <li ><a href="achievement.php">My Ahievement</a></li>
                 <li ><a href="contact.php">Forum</a></li>
                 <li ><a href="index.php?logout=1">Logout</a></li>
               
              </ul>
            </div>

      </div>
     </div>
   </div>
</div>
<!-- Header Ends -->


<!-- List Starts -->
<?php
echo "<div class='overlay spacer'>
        <div class='container'>";
?>        

<!-- Taichung & Tainan button -->
<form method="post" action="">
  <button  name="taic" type="submit">Taichung</button> 
  <button  name="tain" type="submit">Tainan</button> 
</form>       

<?php        
echo "    <h3>Your list：</h3>
            <div class='row text-center'>";
            
// 如果點選"Taichung按鈕"            
if($_SESSION['ds']=="0")
    // 從file資料表內取username加入的景點的資料 
    $result=mysqli_query($conn,"SELECT * FROM $Table_file 
                                WHERE username ='{$_SESSION['userName']}' 
                                AND additem ='1'"); 
// 如果點選"Tainan按鈕"
else
    // 從file2資料表內取username加入的景點的資料 
    $result=mysqli_query($conn,"SELECT * FROM $Table_file2 
                                WHERE username ='{$_SESSION['userName']}' 
                                AND additem ='1'"); 
// 列出加入的景點名稱
if(mysqli_num_rows($result)>0){
    while($row =mysqli_fetch_array($result))
    {
        echo "<h4>{$row['dname']}";                                     // 印出景點名稱    
        echo "<a href='travel_done.php?del={$row['dnum']}'>delete</a>"; // 刪除景點      
        echo "</h4>";       
    }
    echo"</div>
        </div>
       </div>";
}
?>
<!-- List Ends -->


<!-- Plan Starts -->
<div class="clearfix">
<div class="col-sm-6">
    <div id="carousel-testimonials" class="carousel slide testimonails  wowload fadeInLeft" data-ride="carousel">
      <div class="item  animated bounceInLeft row">
        <div  class="col-xs-10">
            
        <?php
        // 從user資料表資料表內取與username對應的資料
        $result2=mysqli_query($conn,"SELECT * FROM $Table_user
                                    WHERE username='{$_SESSION['userName']}'");
        // 取每筆資料        
        while($row2 =mysqli_fetch_array($result2)){
                // 如果點選"Taichung按鈕"
                if($_SESSION['ds']=="0")
                {
                  echo "<h4>{$row2['edit']}</h4>";                                                  // 印出Taichung計畫
                  echo "<h5><a href='travel_edit.php?edit={$_SESSION['userName']}'>edit</a><h5>";   // 編輯Taichung計畫   
                }
                // 如果點選"Tainan按鈕"
                else
                {
                  echo "<h4>{$row2['edit2']}</h4>";                                                 // 印出Tainan的計畫
                  echo "<h5><a href='travel_edit.php?edit2={$_SESSION['userName']}'>edit</a><h5>";  // 印出Tainan的計畫
                }
        }
        ?>
      
         </div>
      </div>
    </div>
</div>
<!-- Plan Ends -->


<!-- Map Starts -->

  <div class="col-sm-6 partners  wowload fadeInRight">
    <!--  map button -->
    <div id="floating-panel">
        <input id="address" type="textbox" value="Taichung Train Station">
        <input id="submit" type="button" value="Search">
    </div>
    <!-- map picture-->
    <div id="map" style="width: ˊ600px; height: 400px"></div>
  </div>
</div>

<!-- Map Ends --> 


</body>
</html>