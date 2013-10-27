<?php require 'header.php'; ?>

<table class="table table-bordered">

	<form class="form-horizontal" role="form">
	  <div class="form-group">
	    <label for="username" class="">Username</label>
	    <input type="text" class="form-control" id="username" placeholder="Username">
	  </div>
	  <div class="form-group">
	    <label for="mobileNumber" class="">Mobile number</label>
	    <input type="text" class="form-control" id="mobileNumber" placeholder="Mobile Number">
	  </div>
	  <div class="form-group">
	      <button type="submit" class="btn btn-default">Add New User</button>
	  </div>
	</form>


	<thead>
		<tr>
			<th>Username</th>
			<th>Phonenumber</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>

<?php require 'footer.php'; ?>