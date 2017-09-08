<form action="" method="post" class="form-horizontal">
    <legend>Settings</legend>
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">Site Name:</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="name" id="name" value="{NAME}">
        </div>
    </div>
    <div class="form-group">
        <label for="template" class="col-lg-3 control-label">Template:</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="template" id="template" value="{TEMPLATE}">
        </div>
    </div>
    <div class="form-group">
        <label for="login_limit" class="col-lg-3 control-label">Login Limit::</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="login_limit" id="login_limit" value="{LIMIT}">
        </div>
    </div>
    <div class="form-group">
        <label for="log_F" class="col-lg-3 control-label">Feedback</label>
        <div class="col-lg-3">
            <fieldset class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_F" id="log_F" value="true" {FEEDBACK_T}>
                        Enabled
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_F" id="log_F" value="false" {FEEDBACK_F}>
                        Disabled
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="form-group">
        <label for="log_E" class="col-lg-3 control-label">Errors</label>
        <div class="col-lg-3">
            <fieldset class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_E" id="log_E" value="true" {ERRORS_T}>
                        Enabled
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_E" id="log_E" value="false" {ERRORS_F}>
                        Disabled
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="form-group">
        <label for="log_L" class="col-lg-3 control-label">Logins</label>
        <div class="col-lg-9">
            <fieldset class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_L" id="log_L" value="true" {LOGINS_T}>
                        Enabled
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_L" id="log_L" value="false" {LOGINS_F}>
                        Disabled
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="form-group">
        <label for="log_BR" class="col-lg-3 control-label">Bug Reports</label>
        <div class="col-lg-9">
            <fieldset class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_BR" id="log_BR" value="true" {BUG_REPORTS_T}>
                        Enabled
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="log_BR" id="log_BR" value="false" {BUG_REPORTS_F}>
                        Disabled
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Submit</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>