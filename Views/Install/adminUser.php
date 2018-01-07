{installer-nav}
<br>
<br>
<form action="" method="post" class="form-horizontal">
    <fieldset>
    <div class="form-group">
        <label for="newUsername" class="col-lg-6 control-label">Username:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="newUsername" id="newUsername" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-lg-6 control-label">Email:</label>
        <div class="col-lg-2">
            <input class="form-control" type="email" name="email" id="email" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="email2" class="col-lg-6 control-label">Re-enter Email:</label>
        <div class="col-lg-2">
            <input class="form-control" type="email" name="email2" id="email2" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-lg-6 control-label">Password:</label>
        <div class="col-lg-2">
            <input class="form-control" type="password" name="password" id="password">
        </div>
    </div>
    <div class="form-group">
        <label for="password2" class="col-lg-6 control-label">Re-enter Password:</label>
        <div class="col-lg-2">
            <input class="form-control" type="password" name="password2" id="password2">
        </div>
    </div>
    </fieldset>
    <input type="hidden" name="token" value="{TOKEN}">
    <button class="btn btn-lg btn-primary center-block" type="submit" name="submit" value="submit">Install</button><br>
</form>