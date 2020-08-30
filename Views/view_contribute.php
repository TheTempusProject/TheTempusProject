<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><a href="#contribution-guidelines-for-thetempusproject" aria-hidden="true" class="anchor" id="contribution-guidelines-for-thetempusproject"></a>Contribution Guidelines for TheTempusProject</h1>
            <p>Contributing to TheTempusProject is completely voluntary and should follow all of the guidelines listed here in order to ensure the highest probability of acceptance. It is highly recommended to use a php linter to automate more of this process. The project is maintained on github and all contributions need to be submitted via pull request to their specific repository under the <code>dev</code> branch. In order to contribute, simply follow the instructions for <a href="#creating-a-pull-request">creating a pull request</a> below.</p>
            <h2><a href="#pull-request-requirements" aria-hidden="true" class="anchor" id="pull-request-requirements"></a>Pull Request Requirements</h2>
            <ul>
                <li>All revisions must follow TTP naming conventions (see <a href="#naming-conventions">Naming Conventions</a> Section)</li>
                <li>Include a clear and concise explanation of the features or changes included in your revision listed by file.</li>
                <li>All code must follow <a href="http://www.php-fig.org/psr/psr-2/" rel="nofollow">PSR 2</a> standards</li>
                <li>prefer the use of [] for arrays over array()</li>
                <li>All functions must be documented with the exception of controller methods (see <a href="#documentation">Documentation</a> Section)</li>
                <li>Controller methods may be doc-blocked when necessary for clarity (see <a href="#documentation">Documentation</a> Section)</li>
                <li>All new Classes must include a class level doc-block (see <a href="#documentation">Documentation</a> Section)</li>
                <li>Any new dependencies will have a longer validation process and should be accompanied by the required information (see <a href="#dependencies">Dependencies</a> Section)</li>
            </ul>
            <h2><a href="#naming-conventions" aria-hidden="true" class="anchor" id="naming-conventions"> </a>Naming Conventions</h2>
            <ul>
                <li>File names are to be lower case</li>
                <li>All class names must be upper case</li>
                <li>Any data being stored as a file must be saved in the app directory</li>
                <li>Controllers must have a constructor and destructor incorporating the constructor and destructor in the Resources Controller</li>
                <li>(This will be an interface requirement soon)</li>
                <li>Views must be named using underscores for separation and must be prefixed with view_</li>
            </ul>
            <h2><a href="#dependencies" aria-hidden="true" class="anchor" id="dependencies"> </a>Dependencies</h2>
            <p>Whenever a dependency is updated or added, pull requests must include a section that answers the following questions.</p>
            <ul>
                <li>Why is this dependency required</li>
                <li>Could this be reasonably accomplished within the app by implementing new features in a later version? explain.</li>
                <li>What is the latest stable version that can be used</li>
                <li>What features are absolutely necessary for your feature or modification to work</li>
            </ul>
            <h2><a href="#documentation" aria-hidden="true" class="anchor" id="documentation"> </a>Documentation</h2>
            <h3><a href="#classes" aria-hidden="true" class="anchor" id="classes"></a>Classes</h3>
            <p>New classes must be prefaced with a doc-block following this style:</p>
            <pre>
                <code>
                    &#47;**
                     * Controllers/admin.php
                     *
                     * This is the admin controller.
                     *
                     * @version 1.0
                     *
                     * @author  Joey Kimsey &lt;JoeyKimsey@thetempusproject.com&gt;
                     *
                     * @link    https://TheTempusProject.com
                     *
                     * @license https://opensource.org/licenses/MIT [MIT LICENSE]
                     *&#47;
                </code>
            </pre>
            <p>From top to bottom:</p>
            <ul>
            <li>Filename on the second line</li>
            <li>A description for the file</li>
            <li>The TTP version this file was built for
            <code>@version 1.0</code></li>
            <li>The Authors name or alias and email
            <code>@author first last &lt;email@link.com&gt;</code></li>
            <li>A copy of the MIT license
            <code>@license https://opensource.org/licenses/MIT [MIT LICENSE]</code></li>
            <li>May include a link for more information
            <code>@link http://link.com</code></li>
            </ul>
            <h3><a href="#functions" aria-hidden="true" class="anchor" id="functions"> </a>Functions</h3>
            <p>Functions must be prefaced with a doc-block following this style:</p>
            <pre>
                <code>
                &#47;**
                 * Intended as a self-destruct session. If the specified session does not
                 * exist, it is created. If the specified session does exist, it will be
                 * destroyed and returned.
                 *
                 * @param string $name   - Session name to be created or checked
                 * @param string $string - The string to be used if session needs to be
                 *                         created. (optional)
                 *
                 * @return bool|string   - Returns bool if creating, and a string if the
                 *                         check is successful.
                 *&#47;
                </code>
            </pre>
            <p>From top to bottom:</p>
            <ul>
                <li>There must be a description of the functions intended usage on the second line</li>
                <li>All parameters should be documented like this
                    <code>@param [type] $name - description</code>
                </li>
                <li>Any function with a return statement must also be documented as such
                    <code>@return [type] - description</code>
                </li>
            </ul>
            <h2><a href="#creating-a-pull-request" aria-hidden="true" class="anchor" id="creating-a-pull-request"></a>Creating a Pull Request</h2>
            <p>This is a simple explanation of how to create a pull request for changes to TheTempusProject. You can find a detailed walk-through on how to <a href="https://help.github.com/articles/creating-a-pull-request/">create a pull request</a> on github.</p>
            <ol>
                <li>First ensure you have followed all the contributing guidelines</li>
                <li>Squash your merge into a single revision. This will make it easier to view the changes as a whole.</li>
                <li>You can submit a pull request <a href="https://github.com/TheTempusProject/TheTempusProject/compare">here</a></li>
                <li>Please submit all pull requests to the dev branch or they will be ignored.</li>
            </ol>
        </div>
    </div>
</div>