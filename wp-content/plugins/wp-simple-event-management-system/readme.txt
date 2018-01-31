=== WP Simple Event Management System ===
Contributors: swashata
Donate link: http://www.intechgrity.com/about/buy-us-some-beer/
Tags: event, fest, event-management, registration, user-registration, event-registration
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.0.0

Easily Manage Events for college/university/school fests etc. Integrates with WP user and registration system.

== Description ==

This plugin is developed mainly for managing events for fests at colleges, universities schools etc. It is quite feature rich.

The concept behind the plugin is to make the user management easy for registering for an event. It integrates finely with WordPress User system.

When user registers [any level below editor] then he/she is able to apply for event from the admin panel of WordPress. Administrator and Editor are allowed to add/edit events and moderate users and accept/deny/edit their application.

Also give in your Institution detail etc. Check out the videos for more information.

A copy of this with limited feature is available at [Google Code](http://code.google.com/p/rcciit-wp-techtrix-event-management-plugin/) . This is where I started the development as I had no intention to release this as another WP Plugin. But as I developed, I realised that it can be of use for someone else as well, so release at WP plugin community. Any future development will be made at here only.

###Concept Behind the working of the system###

The concept was plain and simple. We wanted to have an online registration system which would take care of everything automatically. Obviously the administrator being able to manage all the users and the users being able to create and manage their profile, team members and contact information.

The basic algorithm goes like this:

* Administrator adds an event with all the details.
* Users registers using WordPress' default registration system. They get a menu Events Info. to apply for the event.
* User adds team members and edit if needed.
* User browse the available upcoming events and applies if he/she likes.
* A unique registration ID is generated for the application.
* The admin gets a notification and checks the details. He/She approves if the payment is cleared by hand.
* The user gets the notification and now able to see/print his application report along with the registration ID.

I have not included any online payment option. Administrator have to approve the application manually. To make it easy, there is an option to search using email or user id.

###Video Tutorial###

**User Interface**
[youtube http://www.youtube.com/watch?v=BMWdZQg4X8A]

**Admin Interface**

[youtube http://www.youtube.com/watch?v=zDKE9U0EWQQ]

###Documentation###

Check the Installation and FAQ page. Detail documentation coming soon on our [Blog](http://www.intechgrity.com/wp-simple-event-management-plugin-by-itg-manage-college-fests-and-events/)

###Feature List###

* User are required to register to your site for applying and managing their events. User registration is fully integrated with WP. If the plugin is activated, then if you delete any user from WP panel, then it will also delete his/her team members, profile, applications if present in the plugins database.
* Admin can add/edit any number of events with start/end date, fully HTML enabled Description and Venue field.
* User can apply for any number of events.
* Prices can be set or a particular event can be made FREE, or Announced Later. ;)
* Generation of Unique Registration ID for every application.
* User can add any number of additional team members
* For a particular event admin can restrict the team members to whatever number and also can make the team members optional or mandatory. User will be prompted accordingly.
* User can check status of their application.
* Admin can approve user application.
* When user apply for an event admin gets mailed. Here the "to" email will be the one inserted in the **Instutition Information** > *contact email* part
* Complete option to enter institution name and contact information for the admin
* User must enter phone number, optionally inst. name, address etc. His email and name is integrated with the wordpress account. User can apply for events only after complete profile update.
* Admin has complete control over everything.
* Any user type can apply for an event. Whereas only admin and editor can manage events.
* Event detail, Member Detail, Application Status etc all are fetched via AJAX over a jQuery thickbox, which gives user a cool interface.
* Administrator can print all the registration within a specified date range. It will give the registration table grouped by applicants.
* Usage of rich jQuery UI for the general applicant's interface.

And many more features... Check the Plugins Setting page for FAQs.

**This is 0.9.9RC3 third Release Candidate. After the first and second RCs, I have fixed some bugs and hope to be done with the plugin, but I will see how it goes for my own college and will fix any further bug if encountered. Then it will be set to public release**

###To do list###
* Add I18n to the whole plugin.
* Replace the error messages with something more corporate. As I was developing for my fellow college mates so I left some funny messages
* Add more power to the administrator for user control. The current one is sufficient though.
* *Show few more options like, "User Profile" on the attendee list page* [**DONE**]
* Improve the search feature of attendee. Add proper go back buttons.
* Add a detailed help for users, then administrator
* Add jQuery UI Theming support for the users

== Installation ==

###Uploading The Plugin###

Extract all files from the ZIP file, **making sure to keep the file/folder structure intact**, and then upload it to `/wp-content/plugins/`.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Plugin Activation###

Go to the admin area of your WordPress install and click on the "Plugins" menu. Click on "Activate" for the "WP Category Post List Widget" plugin.

###Plugin Usage###

This is pretty much straight forward...

It comes with two main menus added to you admin panel sidepanel:

* Events Admin
* Events Info.

As you can see, from **Events Admin** you can do administrative works like adding/editing events, approving/deleting applications, modding users etc.

Similary, all other user types can apply for events, add/edit team members, check application status from **Events Info** section.

###Upgrading the Plugin###

So far we have released a few versions of this plugin. If you directly upgrade, then WP won't delete this plugins database. If you deactivate and uninstall this plugin then the db will be deleted.

I will think of some better way of deleting, saving database on future release.

== Frequently Asked Questions ==

Will be added soon.

== Screenshots ==

1. The admin menu
2. The User menu
3. Admin - Adding Institution information
4. Admin - Adding Event
5. Admin - Editing Events
6. Admin - Active Attendes and editing
7. Admin - Viewing users
8. User - Help and Support
9. User - Contact Information
10. User - Adding Team member
11. User - Editing Team member
12. User - Apply for event
13. User - checking application status table
14. User - particular Application status via thickbox modal window
15. Admin- Printing all the registration

== ChangeLog ==

= Version 0.9.9rc3 =
* Fixed some minor pagination bugs on some menus
* Added pagination both above and below all tables for better UI
* Added "Apply for other Events" for users submitting event application form [On success]

= Version 0.9.9rc2 =
* Maintenance release
* Fixed Pagination bug User event apply and user application status
* Fixed date problem for Print Registration admin menu
* Added information caption for event application table, for better ui
* Some small bug fixes

= Version 0.9.9rc =
* First release

== Upgrade Notice ==

= 0.9.9rc3 =
Minor bug fixes, mainly related to pagination. Upgradation recommended.

= 0.9.9rc2 =
Maintenance release. Several bug fixes. Please update immediately

= 0.9.9rc =
Initial Release