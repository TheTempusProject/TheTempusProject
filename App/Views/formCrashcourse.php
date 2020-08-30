<legend>Crash Course Sign-up</legend>
<p>Thank you for your interest in the crash course! The intention is to have a course that will walk you through getting set up to start building your own applications using The Tempus Project; regardless of your skill level. The course is not quite ready for the first group of volunteers, but we are accepting applications.</p>
<p>The course will begin on January 15th, if you would like to be one of the first people to get your hands on the course, please fill out this form. Once the course is ready to move forward, we will select a group of 20 users to work with us on the first round.</p>
<form action="" method="post" class="form-horizontal"  enctype="multipart/form-data">
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">Name</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="name" id="name" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-lg-3 control-label">Email</label>
        <div class="col-lg-3">
            <input type="email" class="form-check-input" name="email" id="email" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="os" class="col-lg-3 control-label">Preferred OS</label>
        <div class="col-lg-2">
            <select name="os" id="os" class="form-control">
                <option value='win' selected>Windows</option>
                <option value='uni'>Linux</option>
                <option value='mac'>Mac</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="experience" class="col-lg-3 control-label">Experience with web development</label>
        <div class="col-lg-2">
            <select name="experience" id="experience" class="form-control">
                <option value='1' selected>No Experience</option>
                <option value='2'>Some Experience</option>
                <option value='3'>Experienced</option>
                <option value='4'>Proficient</option>
                <option value='5'>Expert</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="goals" class="col-lg-3 control-label">Goals:<br>What would you like to accomplish from this course?</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="goals" maxlength="2000" rows="10" cols="50" id="goals"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="info" class="col-lg-3 control-label">Additional Information:</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="info" maxlength="2000" rows="10" cols="50" id="info"></textarea>
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Submit</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>