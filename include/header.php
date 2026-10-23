<?php 
  $loai = ["L1"=>"Loại 1", "L2"=>"Loại 2", "L3"=>"Loại 3"];
?>
<table width="100%" border="0" bgcolor="#FFFFAA">
        <tr>
          <?php 
          foreach ($loai as $key => $value) {
            ?>
              <td><a href="index.php?mod=product&ac=list&idcat=<?php echo $key;?>">
			          <?php echo $value;?></a></td>
            <?php
          } 
          ?>
          <!-- <td><a href="index.php?mod=info&ac=gioithieu">Giới thiệu</a></td>
          <td><a href="index.php?mod=product&ac=spbc">Sách bán chạy</a></td>
          <td><a href="index.php?mod=product&ac=spmoi">Sách mới</a></td> -->
        </tr>
      </table>