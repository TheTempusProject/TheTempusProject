<form action="" method="post" class="form-horizontal">
    <legend>Edit Group: {name}</legend>
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">Name</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="name" id="name" value="{name}">
        </div>
    </div>
    <div class="form-group">
        <label for="page_limit" class="col-lg-3 control-label">Query Limit</label>
        <div class="col-lg-2">
            {OPTION=page_limit}
            <select name="page_limit" id="page_limit" class="form-control">
                <option value='5'>5</option>
                <option value='10'>10</option>
                <option value='25'>25</option>
                <option value='50'>50</option>
                <option value='75'>75</option>
                <option value='100'>100</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="send_messages" class="col-lg-3 control-label">Send Messages</label>
        <div class="col-lg-3">
            <input name="send_messages" id="send_messages" type="checkbox" value="true"{send_messages_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="upload_images" class="col-lg-3 control-label">Upload Images</label>
        <div class="col-lg-3">
            <input name="upload_images" id="upload_images" type="checkbox" value="true"{upload_images_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="feedback" class="col-lg-3 control-label">Send Feedback</label>
        <div class="col-lg-3">
            <input name="feedback" id="feedback" type="checkbox" value="true"{feedback_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="bug_report" class="col-lg-3 control-label">Submit Bug Reports</label>
        <div class="col-lg-3">
            <input name="bug_report" id="bug_report" type="checkbox" value="true"{bug_report_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="member" class="col-lg-3 control-label">Member Access</label>
        <div class="col-lg-3">
            <input name="member" id="member" type="checkbox" value="true"{member_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="mod_cp" class="col-lg-3 control-label">Moderator Privileges</label>
        <div class="col-lg-3">
            <input name="mod_cp" id="mod_cp" type="checkbox" value="true"{mod_cp_checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="admin_cp" class="col-lg-3 control-label">Administrator Privileges</label>
        <div class="col-lg-3">
            <input name="admin_cp" id="admin_cp" type="checkbox" value="true"{admin_cp_checked}>
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Edit</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>