
<table class="table users-list">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Login</th>
			<th scope="col">Status</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
		{foreach:users}
		<tr>
			<th scope="row">{n}</th>
			<td>{email}</td>
			<td>{status}</td>
			<td><a href="javascrip:void;" onclick="hitUser(this);" class="hit-button"
				data-toggle="modal" data-target="#hit-modal" data-id="{id}">hit!</a></td>
		</tr>
		{~foreach}
	</tbody>
</table>
