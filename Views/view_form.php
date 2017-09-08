<form action="" method="post" class="form-horizontal"  enctype="multipart/form-data">
    <legend>Form</legend>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">Text</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX" value="{XXXXXXXXXXXXXXXXXXXXXXX}">
        </div>
    </div>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">Radio</label>
        <div class="col-lg-3">
            <fieldset class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX" value="true" {FEEDBACK_T}>
                        XXXXXXXXXXXXXXXXXXXXXXX
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX" value="false" {FEEDBACK_F}>
                        XXXXXXXXXXXXXXXXXXXXXXX
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">Select</label>
        <div class="col-lg-2">
            {OPTION=XXXXXXXXXXXXXXXXXXXXXXX}
            <select name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX" class="form-control">
                <option value='XXXXXXXXXXXXXXXXXXXXXXX'>XXXXXXXXXXXXXXXXXXXXXXX</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">upload</label>
        <div class="col-lg-3">
            <input class="form-control" type="file" name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX">
        </div>
        <div class="col-md-3 col-lg-3 " align="center">
            <img alt="XXXXXXXXXXXXXXXXXXXXXXX" src="XXXXXXXXXXXXXXXXXXXXXXX" class="img-circle img-responsive">
        </div>
    </div>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">CheckBox</label>
        <div class="col-lg-3">
            <input name="XXXXXXXXXXXXXXXXXXXXXXX" id="XXXXXXXXXXXXXXXXXXXXXXX" type="checkbox" value="XXXXXXXXXXXXXXXXXXXXXXX">
        </div>
    </div>
    <div class="form-group">
        <label for="XXXXXXXXXXXXXXXXXXXXXXX" class="col-lg-3 control-label">TextArea</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="XXXXXXXXXXXXXXXXXXXXXXX" maxlength="2000" rows="10" cols="50" id="XXXXXXXXXXXXXXXXXXXXXXX"></textarea>
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Submit</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>