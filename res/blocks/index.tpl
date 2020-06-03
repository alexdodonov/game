<div class="container pt-5">
	<h1>Old game with the new rules</h1>
	<p>Welcome to the really big game )</p>
	<button type="button" class="btn btn-primary login-button"
		data-toggle="modal" data-target="#login-modal">Login</button>
	<button type="button" class="btn btn-link registration-button"
		data-toggle="modal" data-target="#registration-modal">Registration</button>
</div>

<!-- The Login Modal -->
<div class="modal" id="login-modal">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Login</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<form action="" method="post" class="needs-validation">
					<div class="form-group">
						<div class="request"></div>
					</div>
					<div class="form-group">
						<div class="invalid-feedback request"></div>
						<label for="email">Email address:</label> <input type="text"
							class="form-control email" name="email" placeholder="Enter email">
						<div class="invalid-feedback email"></div>
					</div>
					<div class="form-group">
						<label for="pwd">Password:</label> <input type="password"
							class="form-control password" name="password"
							placeholder="Enter password">
						<div class="invalid-feedback password"></div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary">Login</button>
						<button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- The Registration Modal -->
<div class="modal" id="registration-modal">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Registration</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<form method="post" class="needs-validation">
					<div class="form-group">
						<div class="request"></div>
					</div>
					<div class="form-group">
						<label for="email">Email address:</label> <input type="text"
							class="form-control email" name="email" placeholder="Enter email">
						<div class="invalid-feedback email"></div>
					</div>
					<div class="form-group">
						<label for="pwd">Password:</label> <input type="password"
							class="form-control password" name="password"
							placeholder="Enter password">
						<div class="invalid-feedback password"></div>
					</div>
					<div class="form-group">
						<label for="pwdc">Password confirmation:</label> <input
							type="password" class="form-control password"
							name="password-confirmation"
							placeholder="Enter password confirmation">
						<div class="invalid-feedback password-confirmation"></div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary">Registration</button>
						<button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>