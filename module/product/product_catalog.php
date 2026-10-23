<?php
if (!defined("ROOT"))
{
	echo "You don't have permission to access this page!"; exit;	
}

$loai = getIndex("idcat");
?>
<div class="productList">
	Sách có các ảnh là: <?php echo $loai;?>
</div>
