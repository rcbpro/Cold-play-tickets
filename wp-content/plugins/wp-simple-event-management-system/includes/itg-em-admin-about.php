<?php
if ('itg-em-admin-about.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}

/**
 * Just a little about!
 * Some back links
 * etc etc
 * Also checks if the session is working properly
 * It should anyways ;)
 */
global $itg_em_db_table_name, $wpdb;

$user_table_count = $wpdb->get_var("SELECT COUNT(id) FROM {$itg_em_db_table_name['user_table']}");
$user_team_count = $wpdb->get_var("SELECT COUNT(id) FROM {$itg_em_db_table_name['user_team']}");
$admin_event_count = $wpdb->get_var("SELECT COUNT(id) FROM {$itg_em_db_table_name['admin_event']}");
$reg_table_count = $wpdb->get_var("SELECT COUNT(id) FROM {$itg_em_db_table_name['reg_table']}");
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.postbox').children('h3, .handlediv').click(function(){
                $(this).siblings('.inside').toggle();
            });
        }); 
    </script>
<div class="wrap itg_em_wrap">
    <h2>A Little about this plugin</h2>
    <div id="poststuff" class="metabox-holder">
        <div class="meta-box-sortables">
            <!--
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"></h3>
                    <div class="inside">
                        
                    </div>
                </div>  itg-em-admin-ins
            -->
            <div class="itg_em_left">
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"><span class="itg-em-admin-proj"><span></span>Behind the Scenes</span></h3>
                    <div class="inside">
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
                        <p>I have released this plugin over <a href="http://www.intechgrity.com/wp-simple-event-management-plugin-by-itg-manage-college-fests-and-events/">My Blog</a>. A detailed documentation will be available from there as well. Till then, here are some basic usage guides</p>
                    </div>
                </div>
                
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"><span class="itg-em-admin-ins"><span></span>The Concept behind the WP Simple Event Management System</span></h3>
                    <div class="inside">
                        <p>The concept was plain and simple. We wanted to have an online registration system which would take care of everything automatically. Obviously the administrator being able to manage all the users and the users being able to create and manage their <strong>profile, team members and contact information</strong>.</p>
                        
                        <p>The basic algorithm goes like this:</p>
                        
                        <ul class="user_ul">
                          <li>Administrator adds an event with all the details.</li>
                        
                          <li>Users registers using WordPress' default registration system. They get a menu <strong>Events Info.</strong> to apply for the event.</li>
                        
                          <li>User adds team members and edit if needed.</li>
                        
                          <li>User browse the available <em>upcoming</em> events and applies if he/she likes.</li>
                        
                          <li>A unique registration ID is generated for the application.</li>
                        
                          <li>The admin gets a notification and checks the details. He/She approves if the payment is cleared by hand.</li>
                        
                          <li>The user gets the notification and now able to see/print his application report along with the registration ID.</li>
                        </ul>
                        
                        <p>As simple approach as that. Now lets see how to work with the different types of the system</p>  
                    </div>
                </div>
                
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"><span class="itg-em-admin-ins"><span></span>Technical Information:</span></h3>
                    <div class="inside">
                        <?php
                        if(session_id()) {
                            ?>
                            <p class="updated fade">SESSION working properly. Following are the session data:</p>
                            <ol>
                                <li>SESSION['itg_em_uid']: <?php echo $_SESSION['itg_em_uid']; ?> - The Plugin DB uid of logged in user.</li>
                                <li>SESSION['itg_em_wpuid']: <?php echo $_SESSION['itg_em_wpuid']; ?> - The WordPress UID of currently logged in user</li>
                                <li>SESSION['itg_em_complete']: <?php echo $_SESSION['itg_em_complete']; ?> - Whether the user profile contact information is complete or not</li>
                            </ol>
                            <?php
                        }
                        else {
                            ?>
                            <p class="error fade">SESSION is not working and this plugin will not function at all. Contact Developer ASAP</p>
                            <?php
                        }
                        ?>
                        <p class="updated fade">Database created and functioning properly. Here are the short details:</p>
                        <ol>
                            <li><strong>User Table: </strong> Table name: <?php echo $itg_em_db_table_name['user_table']; ?> - Total <em><?php echo $user_table_count; ?></em> Entries.</li>
                            <li><strong>User Team: </strong> Table name: <?php echo $itg_em_db_table_name['user_team']; ?> - Total <em><?php echo $user_team_count; ?></em> Entries.</li>
                            <li><strong>Admin Events: </strong> Table name: <?php echo $itg_em_db_table_name['admin_event']; ?> - Total <em><?php echo $admin_event_count; ?></em> Entries.</li>
                            <li><strong>Registration: </strong> Table name: <?php echo $itg_em_db_table_name['reg_table']; ?> - Total <em><?php echo $reg_table_count; ?></em> Entries.</li>
                        </ol>
                    </div>
                </div>
                
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"><span class="itg-em-admin-faq"><span></span>Administrative Menus</span></h3>
                    <div class="inside">
                        <p>To be frank, it is really hard to write the documentation of all the things. Just start using it and you will understand. For now, here is a video demo.</p>
                        <div style="width: 400px; margin: 10px auto">
                            <object width="400" height="258"><param name="movie" value="http://www.youtube.com/v/zDKE9U0EWQQ?fs=1&amp;hl=en_US&amp;color1=0x2b405b&amp;color2=0x6b8ab6&amp;border=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/zDKE9U0EWQQ?fs=1&amp;hl=en_US&amp;color1=0x2b405b&amp;color2=0x6b8ab6&amp;border=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="400" height="258"></embed></object>
                        </div>
                    </div>
                </div>
                
                <div class="postbox">
                    <div class="handlediv" title="Click to Toggle"><br /></div>
                    <h3 class="hndle"><span class="itg-em-admin-faq"><span></span>User Menus</span></h3>
                    <div class="inside">
                        <p>Similarly we have a video for users as well.</p>
                        <div style="width: 400px; margin: 10px auto">
                            <object width="400" height="258"><param name="movie" value="http://www.youtube.com/v/BMWdZQg4X8A?fs=1&amp;hl=en_US&amp;color1=0x2b405b&amp;color2=0x6b8ab6&amp;border=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/BMWdZQg4X8A?fs=1&amp;hl=en_US&amp;color1=0x2b405b&amp;color2=0x6b8ab6&amp;border=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="400" height="258"></embed></object>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="itg_em_right">
                
                <div class="postbox">
                        <div class="handlediv" title="Click to Toggle"><br /></div>
                        <h3 class="hndle"><span class="itg-em-admin-donate"><span></span>Support Us</span></h3>
                        <div class="inside">
                            <p>
                                There's a lot of effort behind the development of this plugin. Please support us by doing any of the following :)
                                <ul class="user_ul">
                                    <li>Buy us some beer!</li>
                                    <li>Write about this plugin on your blog.</li>
                                    <li>Help the community by translating the plugin. </li>
                                </ul>
                            </p>
                            <p>
                                If you like to donate, then please use the link below...
                            </p>
                            <a class="don_but" href="http://www.intechgrity.com/about/buy-us-some-beer/">
                                <img src="<?php echo plugins_url('images/donate.png', WP_ITGEM_ABSFILE); ?>" />
                            </a>
                            <p>
                                Thanks you for your support
                            </p>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="handlediv" title="Click to Toggle"><br /></div>
                        <h3 class="hndle"><span class="itg-em-admin-social"><span></span>Get Social</span></h3>
                        <div class="inside">
                            <ul>
                                <li><a href="http://www.facebook.com/swashata"><img src="<?php echo plugins_url('images/facebook_add.png', WP_ITGEM_ABSFILE); ?>" /></a></li>
                                <li><a href="http://www.facebook.com/intechgrity"><img src="<?php echo plugins_url('images/facebook_follow.png', WP_ITGEM_ABSFILE); ?>" /></a></li>
                                <li><a href="http://twitter.com/swashata"><img src="<?php echo plugins_url('images/twitter_follow.png', WP_ITGEM_ABSFILE); ?>" /></a></li>
                                <li>Badges from <a href="http://twitterbuttons.sociableblog.com/">Sociableblog</a> :)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="handlediv" title="Click to Toggle"><br /></div>
                        <h3 class="hndle"><span class="wp-cpl-admin-itg"><span></span>InTechgrity</span></h3>
                        <div class="inside">
                            <script src="http://feeds.feedburner.com/greentechspot?format=sigpro" type="text/javascript" ></script><noscript><p>Subscribe to RSS headline updates from: <a href="http://feeds.feedburner.com/greentechspot"></a><br/>Powered by FeedBurner</p> </noscript>
                        <p><a href="http://feedburner.google.com/fb/a/mailverify?uri=greentechspot&amp;loc=en_US">Subscribe to inTechgrity by Email</a></p>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="handlediv" title="Click to Toggle"><br /></div>
                        <h3 class="hndle"><span class="itg-em-admin-proj"><span></span>Projects</span></h3>
                        <div class="inside">
                            <script src="http://feeds.feedburner.com/IntechgrityProjects?format=sigpro" type="text/javascript" ></script><noscript><p>Subscribe to RSS headline updates from: <a href="http://feeds.feedburner.com/IntechgrityProjects"></a><br/>Powered by FeedBurner</p> </noscript>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <div class="handlediv" title="Click to Toggle"><br /></div>
                        <h3 class="hndle"><span class="itg-em-admin-spon"><span></span>Sponsors</span></h3>
                        <div class="inside">
                            <a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=5226_0_1_3" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/125x125-2.gif" width="125" height="125" /></a>
                            &nbsp;
                            <a href="http://www.flexihostnz.com/aff.php?aff=016"><img src="http://www.flexihostnz.com/banners/125x125-2.gif" width="125" height="125" border="0" /></a>
                            &nbsp;
                            <a href="http://codecanyon.net?ref=swashata"><img src="http://envato.s3.amazonaws.com/referrer_adverts/cc_125x125_v3.gif" width="125" height="125" border="0" /></a>
                            &nbsp;
                            <a href="http://themeforest.net?ref=swashata"><img src="http://envato.s3.amazonaws.com/referrer_adverts/tf_125x125_v5.gif" width="125" height="125" border="0" /></a>
                        </div>
                    </div>
                
            </div>
        </div>
    </div>
</div>