<legend>Recent Comments</legend>
{PAGINATION}
<form action="{BASE}admin/comments/delete" method="post">
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 20%">Author</th>
                <th style="width: 20%">Subject</th>
                <th style="width: 35%">Comment</th>
                <th style="width: 10%">Time</th>
                <th style="width: 5%"></th>
                <th style="width: 5%"></th>
                <th style="width: 5%">
                    <INPUT type="checkbox" onchange="checkAll(this)" name="check.c" value="C_[]"/>
                </th>
            </tr>
        </thead>
        <tbody>
            {LOOP}
            <tr>
                <td><a href="{BASE}admin/users/view/{author}">{author_name}</a></td>
                <td><a href="{BASE}admin/blog/view/{content_id}">{content_title}</a></td>
                <td>{content}</td>
                <td>{DTC}{created}{/DTC}</td> 
                <td><a href="{BASE}admin/comments/edit/{ID}" class="btn btn-sm btn-warning" role="button"><i class="glyphicon glyphicon-edit"></i></a></td>
                <td><a href="{BASE}admin/comments/delete/{ID}" class="btn btn-sm btn-danger" role="button"><i class="glyphicon glyphicon-trash"></i></a></td>
                <td>
                    <input type="checkbox" value="{ID}" name="C_[]">
                </td>
            </tr>
            {/LOOP}
            {ALT}
            <tr>
                <td align="center" colspan="7">
                    No results to show.
                </td>
            </tr>
            {/ALT}
        </tbody>
    </table>
    <button name="submit" value="submit" type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>
<br />