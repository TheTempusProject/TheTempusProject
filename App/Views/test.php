<div class="row">
	<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
	    <div class="sidebar-module">
	        <div class="panel panel-info">
			    <div class="panel-heading">
			        <h3 class="panel-title">Recent Posts</h3>
			    </div>
			    <div class="panel-body">
			        <ol class="list-unstyled">
			            {LOOP}
			                <li><a href="{BASE}blog/post/{ID}">{title}</a></li>
			            {/LOOP}
			            {ALT}
			                <li>No Posts to show</li>
			            {/ALT}
			        </ol>
			    </div>
			    <div class="panel-footer">
			        <a href="{BASE}blog">View All</a>
			    </div>
			</div>
	    </div>
	    <div class="sidebar-module">
	        <div class="panel panel-info">
			    <div class="panel-heading">
			        <h3 class="panel-title">Recent Posts</h3>
			    </div>
			    <div class="panel-body">
			        <ol class="list-unstyled">
			            {LOOP}
			                <li><a href="{BASE}blog/post/{ID}">{title}</a></li>
			            {/LOOP}
			            {ALT}
			                <li>No Posts to show</li>
			            {/ALT}
			        </ol>
			    </div>
			    <div class="panel-footer">
			        <a href="{BASE}blog">View All</a>
			    </div>
			</div>
	    </div>
	</div>
	<div class="col-sm-8 blog-main">
		<h2>Welcome to The Tempus Project</h2>
		<hr>
		<p>The aim of The Tempus Project is to create an easy to use and implement CMS based
		around the MVC style and build using php 5.6 and mysql. Here are some of the features:</p>
		<ul>
		    <li>Fully secured registration/login system</li>
		    <li>Automatic error handling</li>
		    <li>Built in debugging tools</li>
		    <li>Testing implements to help you expand it further</li>
		    <li>Customizable logging to ensure you are always up to date on whats going on</li>
		    <li>Simple administration panel</li>
		    <li>Bug reports and feedback forms included!</li>
		    <li>Drag and drop simple to install</li>
		</ul>
		<p>DISCLAIMER: as of October 24, 2016 this code is not production ready! Please use at your own risk! That being said, I am always trying to improve this system. If you have any suggestions or need to report a bug, you can do so on my <a href="https://github.com/JoeyK4816/thetempusproject">GitHub</a>.</p>
	</div>
</div>