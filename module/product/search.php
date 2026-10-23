<?php
if (!defined("ROOT"))
{
	echo "You don't have permission to access this page!"; exit;	
}

$tensach = getIndex("proname");
$data = [             
	"SP11" => ["L1","Sách 11", 1000, "h11.jpg"], 
	"SP12"=>["L1","Sách 12", 1000, "h12a.jpg;h12b.jpg"],
	"SP21" => ["L2","Sách 21", 2000, "h21a.jpg;h21b.jpg"], 
	"SP22"=>["L2","Sách 22", 2000, "h22.jpg"],
	"SP31" => ["L3","Sách 31", 3000, "h31.jpg"], 
	"SP32"=>["L3","Sách 32", 3000, "h32a.jpg;h32b.jpg;h32c.jpg"]
  ];
  $ten="";
  $hinh="";
  foreach ($data as $row) {
	if($row[1]==$tensach){
		$ten=$row[1];
		$hinh=$row[3];
	}
  }
?>
<div class="productList">
	Tên và hình ảnh của các Sách được tìm thấy:<br> <?php echo $ten."<br>"; echo $hinh?>
</div>
