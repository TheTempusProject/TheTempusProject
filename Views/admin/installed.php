<legend><h2>Installed Models</h2></legend>
{PAGINATION}
<table class="table table-striped">
    <thead>
        <tr>
            <th style="width: 40%">Name</th>
            <th style="width: 15%">Install Date</th>
            <th style="width: 15%">Last Updated</th>
            <th style="width: 5%">Installed Version</th>
            <th style="width: 5%">File Version</th>
            <th style="width: 10%"></th>
        </tr>
    </thead>
    <tbody>
        {LOOP}
        <tr>
            <td><a href="{BASE}admin/installed/view/{name}">{name}</a></td>
            <td>{DTC}{installDate}{/DTC}</td>
            <td>{DTC}{lastUpdate}{/DTC}</td>
            <td>{installedVersion}</td>
            <td>{fileVersion}</td>
            <td><a href="{BASE}admin/installed/view/{name}" class="btn btn-sm btn-warning" role="button"><i class="glyphicon glyphicon-edit"></i></a></td>
        </tr>
        {/LOOP}
        {ALT}
        <tr>
            <td colspan="6">
                No results to show.
            </td>
        </tr>
        {/ALT}
    </tbody>
</table>