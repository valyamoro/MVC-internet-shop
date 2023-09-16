

<?php echo $name; ?>
<form action="/registry" method="post">
	<div class="mb-3">
		<label for="username" class="form-label">Username</label>
		<input type="username" name="username" class="form-control" id="username">
	</div>
	<div class="mb-3">
		<label for="password" class="form-label">Password</label>
		<input type="password" class="form-control" id="password">
	</div>
	<button type="submit" name="registry" value="1" class="btn btn-primary">Submit</button>
</form>