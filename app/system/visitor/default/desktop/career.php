<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Apply Now | <?php echo SITE_NAME ?></title>

    <style>
        h1 {
            font-size: 32px;
            padding: 10px 0px;
        }
    </style>

  </head>
  <body>
    
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-6 col-sm-10">
                <h1>Apply Now</h1>
                <div id="form">
                  <form id="applyResume" enctype="multipart/form-data" >
                    <div class="form-group">
                      <label>Full Name*</label>
                      <input type="text" name="name" class="form-control" placeholder="" required >
                    </div>
                    <div class="form-group">
                      <label>Email*</label>
                      <input type="email" name="email" class="form-control" placeholder="" required >
                    </div>
                    <div class="form-group">
                      <label>Mobile*</label>
                      <input type="number" name="mobile" class="form-control" placeholder="" required >
                    </div>
                    <div class="form-group">
                      <label>Current City</label>
                      <input type="text" name="city" class="form-control" placeholder="" >
                    </div>
                    <div class="form-group">
                      <label>Qualification</label>
                      <input type="text" name="qualification" class="form-control" placeholder="" >
                    </div>
                    <div class="form-group">
                      <label>Word Experience</label>
                      <select name="experience" class="form-control" >
                        <option value="">Your work Experience</option>
                        <option value="Fresher">Fresher</option>
                        <option value="2">0-2 Years</option>
                        <option value="4">2-4 Years</option>
                        <option value="5">More than 4 Years</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Applied for position*</label>
                      <input type="text" name="position" class="form-control" placeholder="" required >
                    </div>
                    <div class="form-group">
                      <label>Expected CTC*</label>
                      <input type="text" name="ctc" class="form-control" placeholder="Expected CTC" required >
                    </div>
                    <div class="form-group">
                      <label>Resume*</label>
                      <input type="file" name="resume" class="form-control-file" placeholder="" accept="application/msword, application/pdf" required >
                      <label style="margin: 10px 0px; font-size: 11px; color: #848484;" >Only upload .doc, .docx and .pdf extension file</label>
                    </div>
                    <div class="form-group">
                      <input type="hidden" name="type" value="resume" >
                      <input type="hidden" name="action" value="apply" >
                      <input type="submit" class="btn btn-block btn-success" id='applyBtn' value="Apply Now" >
                    </div>
                  </form>
                </div>
                <div id="message"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

    <script type="text/javascript">
      $('#applyBtn').click(function(e) {
        e.preventDefault();
		    var postTitle = $('#title').val();
		    var data = new FormData(document.getElementById('applyResume'));
		    $.ajax({
			    type: 'POST',
			    data: data,
	        processData: false,
			    contentType: false,
			    dataType: 'JSON',
			    encrypt: 'multipart/form-data',
			    success: function(data) {
				    if(data.status==200) {
              $('#form').hide();
              $('#message').text(data.msg);
				    } else {
					    alert(data.msg);
				    }
			    },
			    error: function(data) {
				    console.log(data);
			    }
		    });
      });
    </script>

  </body>
</html>