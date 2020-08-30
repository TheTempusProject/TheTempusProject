<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">@ProjectTempus</h3>
    </div>
    <div class="panel-body">
        <ol class="list-unstyled">
            {LOOP}
                <div class="media">
                    <div class="media-left">
                        <a href="#">
                            <img class="media-object" src="{profile_image_url_https}" alt="...">
                        </a>
                    </div>
                    <div class="media-body">
                        {text}
                    </div>
                </div>
            {/LOOP}
            {ALT}
                No Recent Tweets.
            {/ALT}
        </ol>
    </div>
    <div class="panel-footer">
        <a href="{BASE}twitter">View on Twitter</a>
    </div>
</div>