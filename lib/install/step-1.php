<!DOCTYPE html>
<html lang="en">
    <head>
    	<base href="/" >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="noindex" >
        <title>Site Configuration | Installtion</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <style>
        	body {
        		background: #f9f9f9;
        	}
        	form {
        		float: left;
			    margin: 50px 0px;
			    padding: 20px 5%;
			    border: 1px solid #f7f7f7;
			    box-shadow: 0px 0px 0px 0px #333;
			    width: 100%;
			    background: #fff;
			    border-radius: 4px;
        	}
        	form label {
        		float: left;
        	}
        	h1 {
        		float: left;
        		width: 100%;
        		font-size: 32px;
        		margin-bottom: 30px;
			    text-align: center;
        	}
        	h1 span {
        		float: left;
        		width: 100%;
        		font-size: 14px;
        		margin-top: 20px;
        		color: #555;
			    text-align: center;
        	}
        </style>
	</head>
	<body>

		<main>
			
			<div class="container">
				<div class="row justify-content-md-center">
					<div class="col-12 col-md-4">
						<form action="" method="POST" >
							<h1>Installation <span>Site Configuration</span></h1>
							<div class="form-group">
								<label>Site Name</label>
								<input type="text" class="form-control" name="site_name" >
							</div>
							<div class="form-group">
								<label >About Site</label>
								<textarea name="site_desc" class="form-control" ></textarea>
							</div>
							<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control" name="email" >
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="password" class="form-control" name="password" >
							</div>
							<div class="form-check">
							    <input type="checkbox" name="site_index" value="1" class="form-check-input" id="exampleCheck1" checked >
							    <label class="form-check-label" for="exampleCheck1" >Allow Search Engine to index site</label>
							</div>
							<div class="form-group" style="float:left; margin-top: 20px">
								<input type="hidden" name="type" value="install" >
								<input type="hidden" name="action" value="website" >
								<input type="submit" class="btn btn-success" value="Next Step" >
							</div>
						</form>
					</div>
				</div>
			</div>

		</main>

	</body>
</html>