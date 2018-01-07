{installer-nav}
<br>
<form action="" method="post" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label for="siteName" class="col-lg-6 control-label">Site Name:</label>
            <div class="col-lg-2">
                <input class="form-control" type="text" name="siteName" id="siteName" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="dbHost" class="col-lg-6 control-label">Database Host:</label>
            <div class="col-lg-2">
                <input class="form-control" type="text" name="dbHost" id="dbHost" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="dbName" class="col-lg-6 control-label">Database Name:</label>
            <div class="col-lg-2">
                <input class="form-control" type="text" name="dbName" id="dbName" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="dbUsername" class="col-lg-6 control-label">Database Username:</label>
            <div class="col-lg-2">
                <input class="form-control" type="text" name="dbUsername" id="dbUsername" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="dbPassword" class="col-lg-6 control-label">Database Password:</label>
            <div class="col-lg-2">
                <input class="form-control" type="password" name="dbPassword" id="dbPassword">
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="token" value="{TOKEN}">
    <button class="btn btn-lg btn-primary center-block" type="submit" name="submit" value="submit">Configure</button><br>
</form>