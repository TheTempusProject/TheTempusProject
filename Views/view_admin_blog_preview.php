<div class="row">
    <div class="col-sm-8 blog-main">
        <div class="blog-post">
            <h2 class="blog-post-title">{title}</h2>
            <p class="blog-post-meta">{DTC}{created}{/DTC} by <a href="{BASE}admin/user/view/{author}">{author_name}</a></p>
            {content}
        </div><!-- /.blog-post -->
    </div><!-- /.blog-main -->
</div><!-- /.row -->
<form action="" method="post" class="form-horizontal"  enctype="multipart/form-data">
    <legend>New Blog Post</legend>
    <div class="form-group">
        <label for="title" class="col-lg-3 control-label">Title</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="title" id="title" value="{title}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-6 col-lg-offset-3">
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'b');"><b>B</b></button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'i');"><i>I</i></button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'u');"><u>U</u></button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 's');"><del>Strike</del></button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'img');">IMG</button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'p');">Paragraph</button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'code');">Code</button>
            <button type="button" class="btn btn-sm btn-success" onclick="replaceTag ('blog_post', 'list');">list</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', 'c');">&#10004;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', 'x');">&#10006;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', '!');">&#10069;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="insertTag ('blog_post', '?');">&#10068;</button>
            <button type="button" class="btn btn-sm btn-success" onclick="doubleTag ('blog_post', 'color', '#000000');">Color</button>
            <button type="button" class="btn btn-sm btn-success" onclick="doubleTag ('blog_post', 'url', '#');">URL</button>
            <button type="button" class="btn btn-sm btn-success" onclick="doubleTag ('blog_post', 'quote', 'Quote');">Quote</button>
        </div>
    </div>
    <div class="form-group">
        <label for="blog_post" class="col-lg-3 control-label">Post</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="blog_post" maxlength="2000" rows="10" cols="50" id="blog_post">{content}</textarea>
        </div>
    </div>
    <button name="submit" value="publish" type="submit" class="btn btn-lg btn-success">Publish</button>
    <button name="submit" value="saveasdraft" type="submit" class="btn btn-lg btn-success">Save as Draft</button>
    <button name="submit" value="preview" type="submit" class="btn btn-lg btn-success">Preview</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>