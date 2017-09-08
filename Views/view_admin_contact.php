<form action="" method="post" class="form-horizontal">
    <legend>Send Email</legend>
    <p>Please be very careful with this feature. This form allows you to send an email (formatted within the default site email template) to registered emails from various sources including newsletter subscribers, call to action subscribers, and all registered user accounts.</p>
    <fieldset>
    <div class="form-group">
        <label for="mail_type" class="col-lg-3 control-label">Recipients:</label>
        <div class="col-lg-2">
            <select class="form-control" name="mail_type" id="mail_type">
                <option value='none' checked>none</option>
                <option value='registered'>all registered users</option>
                <option value='newsletter'>only newsletter opt-ins</option>
                <option value='subscribers'>only CTA subscribers</option>
                <option value='opt'>CTA subscribers and newsletter opt-ins</option>
                <option value='all'>all available emails</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="mail_subject" class="col-lg-1 control-label">Subject:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="mail_subject" id="mail_subject">
        </div>
    </div>
    <div class="form-group">
        <label for="mail_title" class="col-lg-1 control-label">Title:</label>
        <div class="col-lg-2">
            <input class="form-control" type="text" name="mail_title" id="mail_title">
        </div>
    </div>
    <div class="form-group">
        <label for="mail_message" class="col-lg-3 control-label">Body:<br> (max:2000 characters)</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="mail_message" maxlength="2000" rows="10" cols="50" id="mail_message"></textarea>
        </div>
    </div>
    </fieldset>
    <input type="hidden" name="token" value="{TOKEN}">
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Send</button><br>
</form>