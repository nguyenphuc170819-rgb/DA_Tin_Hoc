<?php
define('ROOT', dirname(__FILE__) );//Thu muc chứa file index);
include "include/function.php";
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