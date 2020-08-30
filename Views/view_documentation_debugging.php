<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2>Debugging</h2>
        <hr>
        <div class="well">
        <p>This guide will walk you through installing and configuring The Tempus Project. For this example, i am running xampp in a windows environment, but the process remains pretty uniform regardless of your setup. If you would like a step by step guide to setting up a local web server, you can use our <a href="{BASE}documentation/gettingStarted">Getting Started guide</a>.</p>
        <h3>The preferred way to install The Tempus Project</h3>
        <p>Use git to clone it from github</p>
        <p>Use composer to install its dependencies</p>
        <p>Use the installer to install the database and configure the settings automatically.</p>
        <p>If you do not currently have git or a git client/shell installed don't worry. You can simply download the repository from github, and copy/paste it to wherever you need it.</p>
        <p>If you would like to use git I would recommend the&nbsp;<a href="https://desktop.github.com/">github desktop client</a>&nbsp;for windows. If you want to get more serious with version control in the future, there are other options, but a GUI will suffice.</p>
        <p>Github also provides a pretty amazing <a href="https://help.github.com/articles/set-up-git/#setting-up-git">Git installation guide</a> if you have any trouble.</p>
        <p>In this step if you do not have composer, or you do not want to download the dependencies separately you can replace <code>https://github.com/JoeyK4816/TheTempusProject</code> with <code>https://github.com/JoeyK4816/TheTempusProjectComplete</code>.</p>
        <h3>There are several ways to install The Tempus Project</h3>
        <p>You can download the zipped archive and unpack it wherever you need it</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/git-zip.png" /></p>
        <p>You can clone it directly using the github client</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/github-desktop-clone.png" /></p>
        <p>Or you can simply install it via git</p>
        <blockquote>
        <p>&#36; git clone https://github.com/joeyk4816/TheTempusProject</p>
        </blockquote>
        <p>Once you have the files installed, you will need to install the latest dependencies. If you are using TTP-Complete, you can skip this step.</p>
        <h3>Dependencies</h3>
        <p>To install the dependencies, simply navigate to your folder and install via composer:</p>
        <blockquote>
        <p>&#36; cd c:\xampp\htdocs\TheTempusProject<br />&#36; composer install</p>
        </blockquote>
        <p>Now that we have all the files we need, we can go ahead and run the installer.</p>
        <h3>Running the Installer</h3>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/url.png" /></p>
        <p>Open up any browser and navigate to install.php inside the folder you used when installing TTP. It should be similar to <code>http://localhost/TheTempusProject/install.php</code> Make sure you have php rewrite module loaded.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/install.png" /></p>
        <p>On this page, go ahead and fill out all the information. And click install. The installer will automatically generate a settings file and install the database.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/success.png" /></p>
        <p>Upon Completion you should see this confirmation and you can now log in and begin using the fully featured Tempus Project.</p>
        <h3>Success!</h3>
        </div>
    </div>
</div>