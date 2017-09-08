<form action="" method="post" class="form-horizontal">
    <legend>New Message</legend>
    <fieldset>
    <div class="form-group">
        <label for="to_user" class="col-lg-1 control-label">To:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="to_user" id="to_user" value="{prepopuser}">
        </div>
    </div>
    <div class="form-group">
        <label for="subject" class="col-lg-1 control-label">Subject:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="subject" id="subject">
        </div>
    </div>
    <div class="form-group">
        <label for="entry" class="col-lg-3 control-label">Message:</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="message" maxlength="2000" rows="10" cols="50" id="message"></textarea>
        </div>
    </div>
    </fieldset>
    <input type="hidden" name="token" value="{TOKEN}">
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Send</button><br>
</form>