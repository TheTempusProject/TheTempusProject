<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h1>Getting Started</h1>
        <hr>
        <div class="well">
    	<p>The aim of this guide is to walk you through the process of installing all the tools you need to start developing with The Tempus Project. This is intended for Windows Users but there will be guides for Linux systems and mac coming soon. If you have already installed and set up xampp or another similar environment you can find installation instructions for The Tempus Project here.</p>
        <p>The Tempus Project is a PHP based application and in order to run it you will first need a web server. The easiest way to do this is to install a web server on your own computer to use for testing and building your site before you put it online for everyone. Its not hard, and for windows users, its as simple as clicking install.</p>
        <p>This web server is extremely light weight and instead of everyone having access to see it, it will only be on your local network.</p>
        <h2>The first thing we need to do is download a few programs.</h2>
        <p><a href="https://www.apachefriends.org/index.html">Xampp</a></p>
        <p>This program is called xampp. It is essentially our web-server in a box. It will install php, apache, and marina db. These are all the features we will need to run our web application on our own computer.</p>
        <p><a href="https://getcomposer.org/download/">Composer</a></p>
        <p>Composer is a php dependency manager. The Tempus Project runs on a core code base called Tempus-Project-Core as well as font-awesome, bootstrap and others. In order to easily install, update, utilize the code bases we will use composer.</p>
        <p><a href="http://www.toolheap.com/test-mail-server-tool/">Mail Testing Utility</a></p>
        <p>This is the testing mail server tool. It is not essential for the project, but in conjunction with this utility and thunderbird, you can be able to easily capture outgoing mail for debugging and testing purposes.</p>
        <h3>Installation</h3>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-warning.png" /></p>
        <p>First we will need to install xampp. once you start, it may popup with a notification like this, don't worry, just click ok and continue.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-modules.png" /></p>
        <p>Once you get to this screen, please check all of the boxes. We will utilize these components in our project later.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-location.png" /></p>
        <p>Once we get to this screen, choose the folder you want to install to, any are fine, but the notification earlier said not in C:\Program Files (x86) so just make sure not to use that folder.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-last-step.png" /></p>
        <p>Once finished, do not start the control panel, we are going to complete the other installations first.</p>
        <h4>Next up is the TestMailServer installation</h4>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/testmailserverinstaller.png" /></p>
        <p>Again, pretty straight forward. you can change your destination folder if you like, but it is not required.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/testmailserver-location.png" /></p>
        <h4>And finally, Composer</h4>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/composer-install.png" /></p>
        <p>If you modified where xampp installs in the previous steps, make sure to make the proper modification on this step.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/composer-php-location.png" /></p>
        <p>After installation completes you will see a dialog box like this. Close all windows you have open except this one and then follow the instructions.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/composer-cmd-step.png" /></p>
        <p>In order to open a command window, you can search the start menu for "cmd" and this option should appear.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/cmd.png" /></p>
        <p>Once you have completed all this, composer has been successfully installed.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/composer-finish.png" /></p>
        <h3>Time to make sure everything works</h3>
        <p>Start xampp and you should be presented with a language select followed by the control panel for your web server.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/language select.png" /></p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-cp.png" /></p>
        <p>Click the start buttons for apache and mysql. This will start your web server for the first time. Once complete, they should be indicated in green.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/xampp-started-services.png" /></p>
        <p>Now open up any web browser and navigate to <code>http://localhost/</code> If everything was successful you should see the xampp dashboard like this</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/welcome-page.png" /></p>
        <p>Now it is time to check composer. You can simply open up a command window as before and type <code>composer</code> This should bring up the composer documentation.</p>
        <p><img class="img-responsive" src="{BASE}Images/Guides/install/composer-check.png" /></p>
        <h2>Congratulations!</h2>
        <p>You have now setup your own personal web server. Now that everything is working, check out the <a href="{BASE}documentation/installation">Installation</a> guide for The Tempus Project or you can head back to the main <a href="{BASE}documentation">Documentation</a> page. </p>
	</div>
        </div>
</div>