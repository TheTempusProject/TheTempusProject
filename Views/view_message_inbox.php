<h2>Inbox</h2>
{PAGINATION}
<form action="{BASE}usercp/messages/delete" method="post">
	<table class="table table-hover">
		<thead>
			<tr>
				<th style="width: 20%">From</th>
				<th style="width: 25%">Subject</th>
				<th style="width: 15%">Last Reply</th>
				<th style="width: 20%"></th>
				<th style="width: 10%"></th>
				<th style="width: 10%">
					<INPUT type="checkbox" onchange="checkAll(this)" name="check.t" value="T_[]"/>
				</th>
			</tr>
		</thead>
		<tbody>
			{LOOP}
			<tr {read}>
				<td>{user_from}</td>
				<td><a href="{BASE}usercp/messages/view_message?ID={ID}">{subject}</a></td>
				<td>{DTC}{last_reply}{/DTC}</td>
				<td><a href="{BASE}usercp/messages/mark_read?ID={ID}">Mark as read</a></td>
				<td><a href="{BASE}usercp/messages/delete?ID={ID}">Delete</a></td>
				<td>
					<input type="checkbox" value="{ID}" name="T_[]">
				</td>
			</tr>
			{/LOOP}
			{ALT}
			<tr>
				<td align="center" colspan="6">
					No Messages.
				</td>
			</tr>
			{/ALT}
		</tbody>
	</table>
	<button name="submit" value="submit" type="submit" class="btn btn-sm btn-danger">Delete</button> <a href="{BASE}usercp/messages/new_message" class="btn btn-sm btn-success">New message</a>
</form>
