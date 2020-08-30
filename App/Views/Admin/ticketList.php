<legend>Tickets</legend>
{PAGINATION}
<form action="{BASE}admin/tickets/delete" method="post">
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 40%">Name</th>
                <th style="width: 15%">Project</th>
                <th style="width: 15%">Status</th>
                <th style="width: 15%">Category</th>
                <th style="width: 10%">Ticket Master</th>
                <th style="width: 5%">
                    <INPUT type="checkbox" onchange="checkAll(this)" name="check.g" value="T_[]"/>
                </th>
            </tr>
        </thead>
        <tbody>
            {LOOP}
            <tr>
                <td><a href="{BASE}admin/tickets/viewTicket/{ID}">{name}</a></td>
                <td><a href="{BASE}admin/tickets/project/{project}">{projectText}</a></td>
                <td><a href="{BASE}admin/tickets/status/{status}">{statusText}</a></td>
                <td><a href="{BASE}admin/tickets/category/{category}">{categoryText}</a></td>
                <td><a href="{BASE}admin/tickets/list/creator/{submittedBy}">{byUser}</a></td>
                <td>
                    <input type="checkbox" value="{ID}" name="T_[]">
                </td>
            </tr>
            {/LOOP}
            {ALT}
            <tr>
                <td align="center" colspan="5">
                    No results to show.
                </td>
            </tr>
            {/ALT}
        </tbody>
    </table>
    <a href="{BASE}admin/tickets/newTicket" class="btn btn-sm btn-success" role="button">Create</a>
    <button name="submit" value="submit" type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>