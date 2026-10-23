<div class="boxleft">
<div class="headerBoxLeft">
Loại sách
</div>
        <?php 
                $data = [             
                        "SP11" => ["L1","Sách 11", 1000, "h11.jpg"], 
                        "SP12"=>["L1","Sách 12", 1000, "h12a.jpg;h12b.jpg"],
                        "SP21" => ["L2","Sách 21", 2000, "h21a.jpg;h21b.jpg"], 
                        "SP22"=>["L2","Sách 22", 2000, "h22.jpg"],
                        "SP31" => ["L3","Sách 31", 3000, "h31.jpg"], 
                        "SP32"=>["L3","Sách 32", 3000, "h32a.jpg;h32b.jpg;h32c.jpg"]
                ];
                foreach ($data as $row ) {
                        ?>
                        <div>
                        <a href="index.php?mod=product&ac=catalog&idcat=<?php echo $row[3];?>">
			<?php echo $row[1];?></a>
                        </div>
                        <?php
                }
        ?>

        <!-- <div >
        	<a href="#">Sách 11</a>
        </div>
        <div >
        	<a href="#">Sách 12</a>
        </div>
        <div >
        	<a href="#">Sách 21</a>
        </div>
        <div >
        	<a href="#">Sách 22</a>
        </div>
        <div >
        	<a href="#">Sách 31</a>
        </div>
        <div >
        	<a href="#">Sách 32</a>
        </div> -->
</div>

