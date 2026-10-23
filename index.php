<?php
define('ROOT', dirname(__FILE__) );//Thu muc chứa file index);
include "include/function.php";
$data = [             
  "SP11" => ["L1","Sách 11", 1000, "h11.jpg"], 
  "SP12"=>["L1","Sách 12", 1000, "h12a.jpg;h12b.jpg"],
  "SP21" => ["L2","Sách 21", 2000, "h21a.jpg;h21b.jpg"], 
  "SP22"=>["L2","Sách 22", 2000, "h22.jpg"],
  "SP31" => ["L3","Sách 31", 3000, "h31.jpg"], 
  "SP32"=>["L3","Sách 32", 3000, "h32a.jpg;h32b.jpg;h32c.jpg"]
];
$loai = ["L1"=>"Loại 1", "L2"=>"Loại 2", "L3"=>"Loại 3"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trang chủ....</title>
</head>

<body> 
<table width="100%" border="1" cellspacing="0">
  <tr>
    <td colspan="3"><p>&nbsp;</p>
     
     <?php
	 include "include/header.php";
	 ?>
      </td>
  </tr>
  <tr>
    <td width="25%" valign="top"><?php
    include "include/left.php";
	
	?></td>
    <td width="70%" valign="top">
    <?php
	include "mod.php";
	?>
  <hr>
    </td>
    <td width="11%" valign="top">
    	
        <div class="rightBox">
			<form action="index.php">
              <input type="hidden" name="mod" value="search" />
            	<input type="text" name="proname" value="Nhập tên sách"  onfocus="if (this.value=='Nhập tên sách') this.value=''; "/>
                <br />
                <input type="submit" value="Tìm kiếm" />
            </form>
        </div>
        
    
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>