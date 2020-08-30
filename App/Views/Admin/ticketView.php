<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">{name}</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class=" col-md-9 col-lg-9 "> 
                            <table class="table table-user-success">
                                <tbody>
                                    <tr>
                                        <td>Ticket master:</td>
                                        <td>{byUser}</td>
                                    </tr>
                                    <tr>
                                        <td>Created on:</td>
                                        <td>{DTC=date}{submittedOn}{/DTC}</td>
                                    </tr>
                                    <tr>
                                        <td>Project:</td>
                                        <td>{projecTtext}</td>
                                    </tr>
                                    <tr>
                                        <td>Category:</td>
                                        <td>{categoryText}</td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td>{statusText}</td>
                                    </tr>
                                    <tr>
                                        <td>Branch:</td>
                                        <td>{branch}</td>
                                    </tr>
                                    <tr>
                                        <td>Is this Live?</td>
                                        <td>{liveText}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">{description}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <a href="{BASE}admin/tickets/edit/{ID}" class="btn btn-sm btn-warning" role="button">Edit</a>
                </div>
            </div>
            {COMMENTS}
            {NEWCOMMENT}
        </div>
    </div>
</div>