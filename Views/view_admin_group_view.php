<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">{name}</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class=" col-md-9 col-lg-9 "> 
                            <table class="table table-user-success">
                                <tbody>
                                    <tr>
                                        <td>Query Limit</td>
                                        <td>{page_limit}</td>
                                    </tr>
                                    <tr>
                                        <td>Send Messages:</td>
                                        <td>{send_messages_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Upload Images</td>
                                        <td>{upload_images_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Submit Feedback</td>
                                        <td>{feedback_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Submit Bug Reports</td>
                                        <td>{bug_report_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Can use the Admin Panel</td>
                                        <td>{admin_cp_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Can use the Moderator Panel</td>
                                        <td>{mod_cp_text}</td>
                                    </tr>
                                    <tr>
                                        <td>Can Access Member Areas</td>
                                        <td>{member_text}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <a href="{BASE}admin/groups/edit/{ID}" class="btn btn-sm btn-warning" role="button">Edit</a>
                    <a href="{BASE}admin/groups/delete/{ID}" class="btn btn-sm btn-danger" role="button">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>