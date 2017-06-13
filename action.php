<?php 
	session_start();
	include('dbconnect.php');
	if(isset($_POST["category"])){
		$category_query="SELECT * FROM categories";
		$run_query=mysqli_query($conn,$category_query);
		echo "<div class='nav nav-pills nav-stacked'>
					<li class='active'><a href='#'><h4>Categories</h4></a></li>";
		if(mysqli_num_rows($run_query)){
			while($row=mysqli_fetch_array($run_query)){
				$cid=$row['cat_id'];
				$cat_name=$row['cat_title'];
				echo "<li><a href='#' class='category' cid='$cid'>$cat_name</a></li>";
			}
			echo "</div>";
		}
	}
	
	if(isset($_POST["brand"])){
		$category_query="SELECT * FROM brands";
		$run_query=mysqli_query($conn,$category_query);
		echo "<div class='nav nav-pills nav-stacked'>
					<li class='active'><a href='#'><h4>Brands</h4></a></li>";
		if(mysqli_num_rows($run_query)){
			while($row=mysqli_fetch_array($run_query)){
				$bid=$row['brand_id'];
				$brand_name=$row['brand_title'];
				echo "<li><a href='#' class='brand' bid='$bid'>$brand_name</a></li>";
			}
			echo "</div>";
		}
	}
	if(isset($_POST['page']))
	{
		$sql="SELECT * FROM products";
		$run_query=mysqli_query($conn,$sql);
		$count=mysqli_num_rows($run_query);
		$pageno=ceil($count/6);
		for($i=1;$i<=$pageno;$i++)
		{
			echo "
				<li><a href='#' page='$i' class='page'>$i</a></li>
			";
		}
	}
	if(isset($_POST['getProduct'])){

		$limit=	6;
		if(isset($_POST['setPage'])){
			$pageno=$_POST['pageNumber'];
			$start=($pageno * $limit)-$limit;
		}
		else{$start=0;}
		if(isset($_POST['price_sorted'])){
			$product_query="SELECT * FROM products ORDER BY product_price";
		}
		elseif(isset($_POST['pop_sorted'])){
			$product_query="SELECT * FROM products ORDER BY RAND()";
		}
		else{
		$product_query="SELECT * FROM products LIMIT $start,$limit";
		}
		$run_query=mysqli_query($conn,$product_query);
		if(mysqli_num_rows($run_query)){
			while($row=mysqli_fetch_array($run_query)){
				$pro_id=$row['product_id'];
				$pro_cat=$row['product_cat'];
				$brand=$row['product_brand'];
				$title=$row['product_title'];
				$price=$row['product_price'];
				$img=$row['product_image'];

				echo "<div class='col-md-4'>
							<div class='panel panel-info'>
								<div class='panel-heading'>$title</div>
								<div class='panel-body'>
								<a href='#' class='imageproduct' pid='$pro_id'>
									<img src='assets/prod_images/$img' style='width:200px; height:250px;' >
								</a>
								</div>
								<div class='panel-heading'>Rs $price
								<button pid='$pro_id' class='quicklook btn btn-danger btn-xs' style='float:right;'>Quick look</button>&nbsp;
								<button pid='$pro_id' class='product btn btn-danger btn-xs' style='float:right;'>Add to Cart</button>
								</div>
							</div></div>";
			}
		}
	}

	if(isset($_POST['get_selected_Category']) || isset($_POST['get_selected_brand']) || isset($_POST['search']) || isset($_POST['price_sorted']))
	{
		if(isset($_POST['get_selected_Category'])){
			$cid=$_POST['cat_id'];
			$sql="SELECT * FROM products WHERE product_cat=$cid";
		}
		elseif(isset($_POST['get_selected_brand'])){
			$bid=$_POST['brand_id'];
			$sql="SELECT * FROM products WHERE product_brand=$bid";
			if(isset($_POST['price_sorted'])){
			$sql="SELECT * FROM products ORDER BY product_price";
			}
		}
		elseif(isset($_POST['search'])){
			$keyword=$_POST['keyword'];
			$sql="SELECT * FROM products WHERE product_keywords LIKE '%$keyword%'";
			if(isset($_POST['price_sorted'])){
			$sql="SELECT * FROM products ORDER BY product_price";
		}
		}
		$run_query=mysqli_query($conn,$sql);
		while($row=mysqli_fetch_array($run_query)){
			$pro_id=$row['product_id'];
				$pro_cat=$row['product_cat'];
				$brand=$row['product_brand'];
				$title=$row['product_title'];
				$price=$row['product_price'];
				$img=$row['product_image'];

				echo "<div class='col-md-4'>
							<div class='panel panel-info'>
								<div class='panel-heading'>$title</div>
								<div class='panel-body' class='imageproduct' pid='$pro_id'><img src='assets/prod_images/$img' style='width:200px; height:250px;'></div>
								<div class='panel-heading'>Rs $price
								<button pid='$pro_id' class='quicklook btn btn-warning btn-xs' style='float:right;'>Quick look</button>&nbsp;
								<button pid='$pro_id' class='product btn btn-danger btn-xs' style='float:right;'>Add to Cart</button>
								
								</div>
							</div></div>";
		}
		

	}

		if(isset($_POST['addToProduct'])){
			if(!(isset($_SESSION['uid']))){echo "
						<div class='alert alert-danger' role='alert'>
  					<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  					<strong>Hey there!</strong> Sign in to buy stuff!
				</div>
					";}
			else{
			$pid=$_POST['proId'];
			$uid=$_SESSION['uid'];
			$sql = "SELECT * FROM cart WHERE p_id = '$pid' AND user_id = '$uid'";
			$run_query=mysqli_query($conn,$sql);
			$count=mysqli_num_rows($run_query);
			if($count>0)
			{
				echo "<div class='alert alert-danger' role='alert'>
  					<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  					<strong>Success!</strong> Already added!
				</div>";
			}
			else
			{
				$sql = "SELECT * FROM products WHERE product_id = '$pid'";
				$run_query = mysqli_query($conn,$sql);
				$row = mysqli_fetch_array($run_query);
				$id = $row["product_id"];
				$pro_title = $row["product_title"];
				$pro_image = $row["product_image"];
				$pro_price = $row["product_price"];

				
				$sql="INSERT INTO cart(p_id,ip_add,user_id,product_title,product_image,qty,price,total_amount) VALUES('$pid','0.0.0.0','$uid','$pro_title','$pro_image','1','$pro_price','$pro_price')";
				$run_query = mysqli_query($conn,$sql);
				if($run_query){
					echo "
						<div class='alert alert-success' role='alert'>
  					<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  					<strong>Success!</strong> Product added to cart!
				</div>
					";
				}
			}
			}
		}
	

	if(isset($_POST['cartmenu']) || isset($_POST['cart_checkout']))
	{
		$uid=$_SESSION['uid'];
		$sql="SELECT * FROM cart WHERE user_id='$uid'";
		$run_query=mysqli_query($conn,$sql);
		$count=mysqli_num_rows($run_query);
		if($count>0){
			$i=1;
			$total_amt=0;
		while($row=mysqli_fetch_array($run_query))
		{
			$sl=$i++;
			$pid=$row['p_id'];
			$product_image=$row['product_image'];
			$product_title=$row['product_title'];
			$product_price=$row['price'];
			$qty=$row['qty'];
			$total=$row['total_amount'];
			$price_array=array($total);
			$total_sum=array_sum($price_array);
			$total_amt+=$total_sum;

			if(isset($_POST['cartmenu']))
			{
				echo "
				<div class='row'>
									<div class='col-md-3'>$sl</div>
									<div class='col-md-3'><img src='assets/prod_images/$product_image' width='60px' height='60px'></div>
									<div class='col-md-3'>$product_title</div>
									<div class='col-md-3'>Rs $product_price</div>
				</div>
			";
			}
			else
			{
				echo "
					<div class='row'>
						<div class='col-md-2'><a href='#' remove_id='$pid' class='btn btn-danger remove'><span class='glyphicon glyphicon-trash'></span></a>
						<a href='#' update_id='$pid' class='btn btn-success update'><span class='glyphicon glyphicon-ok-sign'></span></a>
						</div>
						<div class='col-md-2'><img src='assets/prod_images/$product_image' width='60px' height='60px'></div>
						<div class='col-md-2'>$product_title</div>
						<div class='col-md-2'><input class='form-control price' type='text' size='10px' pid='$pid' id='price-$pid' value='$product_price' disabled></div>
						<div class='col-md-2'><input class='form-control qty' type='text' size='10px' pid='$pid' id='qty-$pid' value='$qty'></div>
						<div class='col-md-2'><input class='total form-control price' type='text' size='10px' pid='$pid' id='amt-$pid' value='$total' disabled></div>
					</div>
				";
			}
		}
		if(isset($_POST['cart_checkout'])){
		echo "
			<div class='row'>
						<div class='col-md-8'></div>
						<div class='col-md-4'>
							<b>Total: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$$total_amt</b>
						</div>
					</div>
		";
		}
	}
}

	if(isset($_POST['removeFromCart']))
	{
		$pid=$_POST['pid'];
		$uid=$_SESSION['uid'];
		$sql="DELETE FROM cart WHERE p_id='$pid' AND user_id='$uid'";
		$run_query=mysqli_query($conn,$sql);
		if($run_query){
			echo "
				<div class='alert alert-danger' role='alert'>
  					<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  					<strong>Success!</strong> Item removed from cart!
				</div>
			";
		}	
	}

	if(isset($_POST['updateProduct']))
	{
		$pid=$_POST['updateId'];
		$uid=$_SESSION['uid'];
		$qty=$_POST['qty'];
		$price=$_POST['price'];
		$total=$_POST['total'];
		$sql="UPDATE cart SET qty='$qty', price='$price', total_amount='$total' WHERE p_id='$pid' AND user_id='$uid'";
		$run_query=mysqli_query($conn,$sql);
		if($run_query){
			echo "
				<div class='alert alert-success' role='alert'>
  					<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  					<strong>Success!</strong> Item updated!
				</div>
			";
		}

	}

	if(isset($_POST['cartcount'])){
		if(!(isset($_SESSION['uid']))){echo "0";}else{
		$uid=$_SESSION['uid'];
		$sql="SELECT * FROM cart WHERE user_id='$uid'";
		$run_query=mysqli_query($conn,$sql);
		$count=mysqli_num_rows($run_query);
		echo $count;
		}
	}


	if(isset($_POST['payment_checkout'])){
		$uid=$_SESSION['uid'];
		$sql="SELECT * FROM cart WHERE user_id='$uid'";
		$run_query=mysqli_query($conn,$sql);
		$i=rand();
		while($cart_row=mysqli_fetch_array($run_query))
		{
			$cart_prod_id=$cart_row['p_id'];
			$cart_prod_title=$cart_row['product_title'];
			$cart_qty=$cart_row['qty'];
			$cart_price_total=$cart_row['total_amount'];

			$sql2="INSERT INTO customer_order (uid,pid,p_name,p_price,p_qty,p_status,tr_id) VALUES ('$uid','$cart_prod_id','$cart_prod_title','$cart_price_total','$cart_qty','CONFIRMED','$i')";
			$run_query2=mysqli_query($conn,$sql2);
		}
		$i++;
		$sql3="DELETE FROM cart WHERE user_id='$uid'";
		$run_query3=mysqli_query($conn,$sql3);
	}

	if(isset($_POST['product_detail'])){
		$pid=$_POST['pid'];
		$sql="SELECT * FROM products WHERE product_id='$pid'";
		$run_query=mysqli_query($conn,$sql);
		$row=mysqli_fetch_array($run_query);
		$pro_id=$row['product_id'];
		$image=$row['product_image'];
		$title=$row['product_title'];
		$price=$row['product_price'];
		$desc=$row['product_desc'];
		$tags=$row['product_keywords'];

		echo "
				<div class='row'>
					<div class='col-md-6 pull-right'>
						<img src='assets/prod_images/$image' style='width:250px;height:300px;'>
					</div>
					<div class='col-md-6'>
						<div class='row'> <div class='col-md-12'><h1>$title</h1></div></div>
						<div class='row'> <div class='col-md-12'>Price:<h3 class='text-muted'>$price</h3></div></div>
						<div class='row'> <div class='col-md-12'>Description:<h4 class='text-muted'>$desc</h4></div></div><br><br>
						<div class='row'> <div class='col-md-12'>Tags:<h4 class='text-muted'>$tags</h4></div></div>
						<button pid='$pro_id' class='product btn btn-danger'>Add to Cart</button>
					</div>
				</div>
		";
	}

 ?>

