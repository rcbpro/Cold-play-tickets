<?php
if ('itg-em-user-help.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_subs')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
/**
 * Shows the User the help documents
 * Also lists down the inst. Contact informaiton
 */
global $itg_em_options;
?>
<div class="wrap">
    <h2>Welcome to our Online Event Management System</h2>
    <p>
        <span class="description">This page shows you all the information and help you need. Please read them carefully before asking for doubt. :)</span>
    </p>
    <h3>Institution Information:</h3>
    <div class="tabs">
        <ul>
            <li><a href="#1basic_info">Basic Information</a></li>
            <li><a href="#1contact_details">Contact Details</a></li>
            <li><a href="#1notes">Notes</a></li>
        </ul>
        <div id="1basic_info">
            <table class="widefat">
                <thead>
                    <tr>
                        <th scope="col" colspan="2">Information of our Institution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Institution Name:</td>
                        <td><?php echo $itg_em_options['institute_name']; ?></td>
                    </tr>
                    <tr>
                        <td>Shortname:</td>
                        <td><?php echo $itg_em_options['institute_short_name']; ?></td>
                    </tr>
                    <tr>
                        <td>Fest / Event Name:</td>
                        <td><?php echo $itg_em_options['fest_name']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="1contact_details">
            <table class="widefat">
                <thead>
                    <tr>
                        <th scope="col" colspan="2">Our Contact Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Address:</td>
                        <td>
                            <ul>
                                <li><?php echo $itg_em_options['address1']; ?></li>
                                <li><?php echo $itg_em_options['address2']; ?></li>
                                <li><?php echo $itg_em_options['state']; ?></li>
                                <li><?php echo $itg_em_options['city']; ?></li>
                                <li><?php echo $itg_em_options['country']; ?></li>
                                <li>Pin: <?php echo $itg_em_options['pincode']; ?></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>Email: </td>
                        <td><a href="mailto:<?php echo $itg_em_options['contact_email']; ?>"><?php echo $itg_em_options['contact_email']; ?></a></td>
                    </tr>
                    <tr>
                        <td>Phone Numbers:</td>
                        <td>
                            <ul>
                                <li><?php echo $itg_em_options['phone_num1']; ?></li>
                                <li><?php echo $itg_em_options['phone_num2']; ?></li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="1notes">
            <table class="widefat">
                <thead>
                    <tr>
                        <th scope="col">Other Notes:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $itg_em_options['notes']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <h3>Understanding the basic working of the Online Registration system:</h3>
    <div class="tabs">
        <ul>
            <li><a href="#2contact_info">Contact Info.</a></li>
            <li><a href="#2team_members">Team Members</a></li>
            <li><a href="#2ev_apply">Apply for Events</a></li>
            <li><a href="#2status_check">Checking the status</a></li>
        </ul>
        <div id="2contact_info">
            <p>To register for the event, you need to first update your contact information. Else nothing will work for you.</p>
            <ul class="user_ul">
                <li>Go to the <a href="admin.php?page=itg_em_user_profile_page">Contact Information page</a></li>
                <li>Enter your phone number, university/college/school and your roll number. Please be precise here. You are responsible if you make any mistake. However you can always get back to the page to edit the information</li>
                <li>Now <strong>Please go the <a href="profile.php">PROFILE Page</a></strong> and update your first name, last name etc. So that we can fully identify you.</li>
                <li>Thats it. Now you are good to go.</li>
            </ul>
        </div>
        <div id="2team_members">
            <p>Many of our events requires additional team members to play. So, we have added a team member option for you. Just follow the instruction</p>
            <ul class="user_ul">
                <li>Go to <a href="admin.php?page=itg_em_user_add_team_page">Add Team members</a></li>
                <li>Type in the details. You dont need to add the same member several times for different events. Once you add a team member he/she will be shown on each event application</li>
                <li>Hit Save and you are done.</li>
            </ul>
            <p>Similary you can <a href="admin.php?page=itg_em_user_edit_team_page">Edit Members</a> to modify the members in the similar way.</p>
        </div>
        <div id="2ev_apply">
            <p>This is as easy as 1-2-3</p>
            <ol class="user_ol">
                <li>Go to the <a href="admin.php?page=itg_em_user_apply_event_page">Apply Events</a> page.</li>
                <li>View details of the events and hit apply</li>
                <li>Check in your team members and hit <strong>Apply for the Event</strong></li>
            </ol>
            <p>Easy peasy eh?</p>
        </div>
        <div id="2status_check">
            <p>You can check the status of your application any time from the <a href="">App. Status</a> menu. Here you will be...</p>
            <ul class="user_ul">
                <li>Given a list of your Application.</li>
                <li>You will be given a unique registration id on payment confirmation.</li>
                <li>You may close any application if the payment is not complete and you wish to cancel it.</li>
                <li>You can take a print of the application status by hitting the <strong>View Application Status</strong> button</li>
            </ul>
        </div>
    </div>
    
    <h3>Tips and Credits:</h3>
    <div class="tabs">
        <ul>
            <li><a href="#3tips_t">Tips</a></li>
            <li><a href="#3credits_au">Credits</a></li>
            <li><a href="#3behind_scene">Behind the Plugin</a></li>
            <li><a href="#3video_tut">Video Tutorial</a></li>
        </ul>
        
        <div id="3tips_t">
            <p>Follow these tips for a better understanding of the online event registration</p>
            <ul class="user_ul">
                <li>Always add sufficient team members, else you will not be able to apply for an event which mandatorily requires some fixed amount of members.</li>
                <li>If you see (<em>Announced Later</em>) for payment, then you can contact the co-ordinator of the event of payment details</li>
                <li>While paying for the events, always check and verify your application ID. Your application ID is unique and it tells us that this application is made by you</li>
                <li>Also, keep your USER ID and email handy while paying for an event. Administrator can easily search your application if they have those information.</li>
                <li>Always take a print out of approved applications. At least save a copy of it. Our administrator may delete old applications, (once the event is over), so we are not responsible for anything.</li>
                <li>As of now, you can not apply for an event and delete it. Only administrators are able to delete your application. You can close the application and contact the admin to delete it if you want.</li>
                <li>Every event will be approved on a case by case verification. If you are in doubt, you can always leave a message while applying for the event.</li>
            </ul>
        </div>
        <div id="3credits_au">
            <p>So, looking here? Well, this whole thing is made on the backend of <a href="http://wordpress.org">WordPress</a> a very powerful PHP/MySQL CMS.</p>
            <p>This event management thing is actually a plugin to the WordPress, developed from scratch by <a href="http://www.swashata.me">Swashata</a> | <a href="http://www.intechgrity.com">InTechgrity</a></p>
            <p>While developing I have used many other Open Source projects, especially jQuery plugins and have widely used many WordPress API to make my life easier. A big thanks goes to all of them :)</p>
            <p>And last, but not least, this is an Open Source Project. If you are a PHP/MySQL developer and/or interested to see how this thing works, check in our <a href="http://code.google.com/p/rcciit-wp-techtrix-event-management-plugin/">Google Project</a> or <a href="http://wordpress.org/extend/plugins/wp-simple-event-management-system/">WordPress Plugin Repository</a> (Globalized. Under development).</p>
            <p>Following is a list of resources used:</p>
            <ul class="user_ul">
                <li><a href="http://jquery.com">jQuery UI</a></li>
                <li><a href="http://codex.wordpress.org/Plugin_API">Loads of WP API</a> - Heaps and Tons of it ;)</li>
                <li><a href="http://plugins.jquery.com/project/PrintArea">PrintArea jQuery Plugin</a></li>
                <li>And some other stuffs. All my codes are well commented and proper credits are given within the source code.</li>
            </ul>
        </div>
        <div id="3behind_scene">
            <p>Hi There. My name is <a href="http://www.swashata.me">Swashata</a> and currently I am pursuing B.Tech in Computer Sciences and Engineering from <a href="http://www.rcciit.in">RCCIIT</a> - Kolkata [2nd year as of 2010]. Besides that, I own a site <a href="http://www.intechgrity.com">inTechgrity</a> and a portfolio <a href="http://www.itgdesignbox.com">iTgDesignBox</a> and some other sites as well.</p>
            <p>The concept of this plugin came when I was asked to develop something for online registration at my college. Every year we have two fests at our college. <a href="http://techtrix.rcciitretech.in">TechTrixX</a> and <a href="http://regalia.rcciitretech.in">RegaliaX</a> combinedly known as <a href="http://www.rcciitretech.in">ReTech</a></p>
            <p>Anyways, to develop that something was not that easy. Speaking the truth, it was actually a hard job. As I wanted to develop over <a href="http://wordpress.org">WordPress</a>. I searched for the plugin, but could not find exactly what I was looking for.</p>
            <p>So I kick started my Komodo Edit and started coding my own. I thought it would be easy at first, but as days passed, it really became something challenging for me. And yeah! I like challenges ;)</p>
            <p>Finally after around 2 weeks of work, I finished developing this plugin. The best part is that while making this thing, I tried to make it in a way that every colleges in India (Where we actually don't have or people really dont bother about paying online) can use it.</p>
            <p>So far so good. The effort I have put behind this plugin is really worthy. Probably that's why you are reading this and using this plugin ;)</p>
            <p>And just to include, if you manage to find any bug inside this system or have any idea about improvement, please feel free to tell me. You can contact me from <a href="http://www.intechgrity.com/contct-us/">Here</a></p>
            <p>While developing this plugin I have used many other open source stuffs, mostly <a href="http://jqueryui.com/">jQuery UI</a> and <a href="http://plugins.jquery.com/project/PrintArea">PrintArea</a> jQuery Plugin</p>
            <p>One more thing... I like to hear from you! I have devoted much of my time behind this wonderful plugin so to be frank, I need your feedback. Not necessarily I need a positive one, but let me know if you are developing over this or using this plugin :)</p>
            <p>For developers, well it is an open source, as open as it can be. Released under GPL license, it gives you full freedom of doing whatever you want! The primary plugin I made for my college is available at <a href="http://code.google.com/p/rcciit-wp-techtrix-event-management-plugin/">Google Project Hosting</a> and also I have plan to make it global by uploading it <a href="http://wordpress.org/extend/plugins/wp-simple-event-management-system/">here</a>. Hope by the time you are seeing this, the plugin is there at WordPress official directory :)</p>
            <p>Soon I will release it over my <a href="http://www.intechgrity.com">blog</a>. The detailed tutorial will be available there. Till now here I give you some basic usage guide.</p>
        </div>
        
        <div id="3video_tut">
            <p>In this tutorial I have registered at our techtrix site and have applied for the event</p>
            <div style="width: 580px; margin: 10px auto">
                <object width="580" height="360"><param name="movie" value="http://www.youtube.com/v/BMWdZQg4X8A?fs=1&amp;hl=en_US&amp;color1=0x006699&amp;color2=0x54abd6&amp;border=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/BMWdZQg4X8A?fs=1&amp;hl=en_US&amp;color1=0x006699&amp;color2=0x54abd6&amp;border=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="580" height="360"></embed></object>
            </div>
        </div>
        
    </div>
</div>