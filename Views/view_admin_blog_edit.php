<script language="JavaScript" type="text/javascript">tinymce.init({ selector:'#blog_post' });</script>
<form action="" method="post" class="form-horizontal"  enctype="multipart/form-data">
    <legend>Edit Blog Post</legend>
    <div class="form-group">
        <label for="title" class="col-lg-3 control-label">Title</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="title" id="title" value="{title}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-6 col-lg-offset-3 btn-group">
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', 'c');">&#10004;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', 'x');">&#10006;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', '!');">&#10069;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', '?');">&#10068;</button>
        </div>
    </div>
    <div class="form-group">
        <label for="blog_post" class="col-lg-3 control-label">Post</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="blog_post" maxlength="2000" rows="10" cols="50" id="blog_post">{content}</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-6 col-lg-offset-3">
            <button name="submit" value="publish" type="submit" class="btn btn-lg btn-success">Publish</button>
            <button name="submit" value="saveasdraft" type="submit" class="btn btn-lg btn-success">Save as Draft</button>
            <button name="submit" value="preview" type="submit" class="btn btn-lg btn-success">Preview</button>
        </div>
    </div>
    <input type="hidden" name="token" value="{TOKEN}">
</form>