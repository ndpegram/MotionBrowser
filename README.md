@path<span><span>/var/www/html/MotionBrowser/</span></span>

Readme
======

Browser-based user interface for the Motion application (see <https://github.com/Motion-Project/motion>). The original version of MotionBrowser was developed by Carlos Ladeira (<caladeira@gmail.com>). This version (<https://github.com/ndpegram/MotionBrowser>) updated by Nigel D. Pegram (<ndpegram@gmail.com>). This software is distributed under the GNU public license.

Tested with Motion 4.0, PHP 7.2, MySQL 5.7.

This web page reads the mysql database filled by Motion and output events by day It creates small thumbnail were you can click to watch the movie file for the same event. You can also delete the events you select.

It’s a good interface in case you are running Motion on a computer without monitor/keyboad/mouse, only network!

It is supposed to work with the following motion.conf options set as shown (for better results):

            pre_capture 8       (works for me with a framerate of 6)
            post_capture 8      (ie)
            output_all off
            output_normal best (or) first
            output_motion off
            text_event %Y%m%d%H%M%S
            ffmpeg_cap_new on
            ffmpeg_video_codec msmpeg4
            sql_log_image on
            sql_log_snapshot off
            sql_log_mpeg on
            sql_log_timelapse off
            sql_query INSERT ...    (I use the default)
            mysql_db motion     (my database name)
            mysql_host localhost
            mysql_user ...      (the user name i created in MySQL)
            mysql_password ...  (the password associated with user)
        

Installation
============

File system
-----------

Install the files into the appropriate location in your web software’s file tree.

The directory where you store your motion files must be writeable by the user under which the web software is running. In Ubuntu, for example, this is the `www-data` user. For example, if the directory to which motion is saving the video and image files is `/var/lib/motion`, then you should issue the following (assuming the web user is `www-data`). You will likely need to issue this commands as a superuser.

            sudo chgrp -R www-data /var/lib/motion
            sudo chmod -R g+rw /var/lib/motion
        

mySQL
-----

Use the following to create your mySQL table.

            CREATE TABLE `security` (
              `camera` int(11) DEFAULT NULL,
              `filename` varchar(80) NOT NULL DEFAULT '',
              `frame` int(11) DEFAULT NULL,
              `file_type` int(11) DEFAULT NULL,
              `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `text_event` varchar(40) NOT NULL DEFAULT '0000-00-00 00:00:00',
              `event_time_stamp` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              `file_size` varchar(36) NOT NULL DEFAULT '0',
              KEY `time_stamp` (`time_stamp`),
              KEY `event_time_stamp` (`event_time_stamp`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        

Apache
------

You need to set the appropriate values in the security.conf apache settings file.

<span>ServerName</span>  
should be set to the name under which you will serve the site.

<span>ProxyPass and ProxyPassReverse</span>  
URLs should be set to the address where motion is running. This address should use dotted quad notation and include the port.

For example: `http://192.168.2.42:9080/`, where 192.168.2.42 is the server where the motion service is running and 9080 is the port where motion is running (see `webcontrol_port` in the `motion.conf` file.).

<span>DocumentRoot</span>  
should be set to the fully-qualified path of where you intalled the MotionBrowser files

<span>Other</span>  
Log names and locations can be set as required.

config.ini
----------

The following settings need to be customised.

<span><span>\[</span>webcam<span>\]</span></span>  
 

<span>“server”</span>  
The location where motion is running. By default this is set to “localhost”.

<span>webcam\_port<span>\[</span>&lt;id&gt;<span>\]</span></span>  
Map the camera IDs as recorded in the database to the port used to access them.

<span>setup\_port</span>  
The port used to access motion’s settings.

<span><span>\[</span>mysql<span>\]</span></span>  
 

Set the below values to point to the location where motion is storing its records, including the appropriate username and password for read and write access to the table.

<span>host</span>  
the server where the MySQL database is running.

<span>user</span>  
the user name to use to login.

<span>password</span>  
the user’s password.

<span>db</span>  
the database containing the table.

<span><span>\[</span>disk<span>\]</span></span>  
 

<span>freeSpaceOptimum</span>  
The “target” percentage of space to keep free. (At present this only impacts on the display of the free space meter.)

<span>freeSpaceBuffer</span>  
The minimum free space to reserve on the disk where recordings and images are saved. This is expressed as a percentage of the total disk space. If you wish to use a maximum of 70% of the disk, then this value should be set to 30.

You must not set this so high that no recordings are retained. For example, if other software and files on the disk use 25% of the free space and this is set to 75% or higher, then no files will be retained. You should calculate the value so that:

<span><span style="font-variant:small-caps;"></span>disk space used other than by motion (%) + free space buffer (%) + motion video recording space (%) ≦ 100%</span>

History
=======

Version 2.0
-----------

Totally rewritten to use CSS div elements rather than tables, so as to degrade better and to display better on a wider range of devices. Also rewritten to use object-based coding, rather than procedural coding (as in the original), to ease maintenance. Code also separated into more discrete units to ease maintenance.

Version 1.2.1
-------------

<span>20190729</span>  
Add new feature to autoclean disk.

-   Automatically delete oldest files and SQL records when disk free space falls below a set percentage value.

    Currently this runs on page load. If this causes too great a delay, it may need to be implemented differently, such as via a cron script. The key question is does it noticeably impact on user experience?[1]

-   Moved some procedural code to functions to simplify and to prepare for move to OO coding.

-   Miscellaneous minor code optimisations.

Version 1.2
-----------

<span>20190602</span>  
Largely internal reorganisation

-   Converted from GET to POST, to allow deletion of selection of large number of videos on one day. This includes removal of overarching form in HTML code and using javascript to post and AJAX instead.

-   Converted from PHP include files to gettext for internationalisation.

-   Use AJAX for integration of internationalisation of javascript messages (alert/confirm) with PHP gettext calls. Thus only one set of internationalisation files are required.

-   Miscellaneous bug fixes and enhancements.

Version 1.1
-----------

<span>20190422</span>  
Adapted by Nigel Pegram.

-   Updated to PHP 7 and mySQL 14

-   Adjusted to stream video files rather than download

-   Miscellaneous bug fixes.

Version 1.0
-----------

<span>20060000</span>  
Original Carlos Ladeira version.

To Do/Planned features
======================

-   FIXME: Display of 2 September causes server error. Debug.

-   TODO: Alter code for month change arrows so that destination date info is displayed.

-   Update the code to object-oriented format

-   Convert layout from tables to DIV and CSS format, to allow for better display on mobiles, etc., and to allow for more useful degrading.

-   As part of the above, perhaps move to AJAX interface. For example, responding to date clicks via AJAX to fill content DIV, rather than submitting a form and redrawing the whole page (as it currently does).

-   Add link to video preview “box” to allow downloading of file, rather than streaming. (Is this necessary since we can do this from the stream?)

-   Add button to delete data and files based on user-selected date.

-   Review code to respond to disk free space falling below a preset level. There are a number of possible ways to implement this:

    1.  Insert a date into a file which a cron script reads and acts on during low load periods.

    2.  Delete from within the web browser (could be problematic as it may tie up the browser when, presumably, the user will want to interact with it).

    3.  Use a hybrid approach. Delete the database rows immediately, but write out the files to delete for later crontab processing. The advantage of this approach is that the items will no longer display in the browser and the interface should remain responsive.

        <span>Question:</span>  
        where to save the list of text files? Possible locations are /etc/motion or the files directory. The latter seems preferable. It seems wise to set this as a variable in the config file which defaults to the files directory.

    <span>Currently</span>  
    option 2 has been implemented. Responsiveness *is* an issue.

-   Fix problem with camera index starting at 1 in <span>`c`onfig.inc</span>. The problem seems to be that the cameras in the motion settings file are not necessarily sequential nor zero-based. Yet this seems to be assumed to be a zero-based numeric array.

    One possible solution is to recode so that camera IDs are an associative array of ID and port. Correctly creating this array would be the responsibility of the user. Another solution is to extract this information from the motion settings files. The extraction might be possible automatically (if we can extract the settings directory) or by manually running a utility in the settings directory.

[1] Another possiblity is to use a hybrid approach. Delete the database rows immediately, but write out the files to delete for later crontab processing. The advantage of this approach is that the items will no longer display in the browser and the interface should remain responsive.

<span>Question:</span>  
where to save the list of text files? Possible locations are /etc/motion or the files directory. The latter seems preferable. It seems wise to set this as a variable in the config file which defaults to the files directory. Another possibility is to save into the SQL database, either by marking records for deletion or creating records in a new table. The former seems to be preferable as it will be quick and not require files to be created in any new location.
