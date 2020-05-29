<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">{deck_title}</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-3 " align="center">
                            <img alt="User Pic" src="{BASE}{avatar}" class="img-circle img-responsive">
                        </div>
                        <div class=" col-md-9 col-lg-9 "> 
                            <table class="table table-user-primary">
                                <tbody>
                                    <tr>
                                        <td>Created:</td>
                                        <td>{DTC}{time}{/DTC}</td>
                                    </tr>
                                    <tr>
                                        <td>Would you rather?</td>
                                        <td>{cardText)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <a href="{base}usercp/messages/newmessage?prepopuser={USERNAME}" data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                    {ADMIN}
                    <span class="pull-right">
                        <a href="{base}admin/wyr/edit/{ID}" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                        <a href="{base}admin/wyr/delete/{ID}" data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                    </span>
                    {/ADMIN}
                </div>
            </div>
        </div>
    </div>
</div>