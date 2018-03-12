# B U Z Z S A W
Your private music streaming cloud  
html multimedia server - library and player  


# About
BUZZSAW is a web application for playing music and videos and
organizing them in a library. It is designed to listen your music
anywhere, without syncing your mobile devices. It is written in
html (who'd have thought?), PHP, JS and CSS.  

## Highlights
  - playlists
  - audio visualization
  - showing id3 tags and cover images from file
  - remote player functionality
  - optimized for mobile devices

## Screenshots
![Player with visualization](https://raw.githubusercontent.com/schorschii/buzzsaw/master/img/screenshot/1_player.png)
![Library: Artists](https://raw.githubusercontent.com/schorschii/buzzsaw/master/img/screenshot/2_artists.png)
![Library: Albums](https://raw.githubusercontent.com/schorschii/buzzsaw/master/img/screenshot/3_albums.png)
![Library: Tracks](https://raw.githubusercontent.com/schorschii/buzzsaw/master/img/screenshot/4_tracks.png)
![Current playlists and options](https://raw.githubusercontent.com/schorschii/buzzsaw/master/img/screenshot/5_current.png)


# Requirements
BUZZSAW was tested with the following software environment. Please
note that there will be no support for other environments!

## Server
  - Linux-based operating system
  - Apache 2
  - PHP 5 (only with "mysqlnd" package) or 7
  - MySQL or MariaDB
  - fast connection to the client; best experience if client is running on the same machine

## Client (desktop)
  - Google Chrome v60+  
    or other webkit based browsers  
    not tested with Safari
  - Mozilla Firefox v55+
  - NOT running in IE

## Client (mobile)
  - Chrome on Android 7+
  - Safari on iOS 6
    with some limitations


# Installation

## Upgrade from old version
1. Delete all files from the buzzsaw directory, except: "database.php", "music" and "music_thumb" directory.
2. Copy all files from this archive to your buzzsaw directory, again except "database.php", "music" and "music_thumb" directory.
3. That's it. If you encounter some problems, clear your browser cache and/or execute file scan again (Menu -> "Options" -> "Scan filesystem").

## Setup up a new installation
1. Setup up a web server with PHP interpreter and a mysql server.

   For Debian <= 8 or current Raspian
   ```bash
   apt-get install apache2 php5 php5-mysqlnd libapache2-mod-php5 mysql-server
   ```

   or with PHP7 on Debian 9
   ```bash
   apt-get install apache2 php php-mysql libapache2-mod-php mysql-server
   ```

2. Copy all files from this archive into your webserver root directory.
3. Edit the database.php file and enter your mysql database credentials.
4. Grant write access for the web server user www-data to the "music" and "music_thumb" directory.
5. Open the buzzaw directory in your browser. It should ask you to execute the database setup. Click the button to start the setup. After the setup finished, log in leaving the password box empty.
6. Place all your music files into the "music" directory. Go to Menu -> "Options" -> "Scan filesystem". You can play your music without scanning by using the "files" tab inside the menu.

Enjoy your music! :-)


# License
GNU General Public License - see LICENSE.txt  
&copy; 2017-2018 Georg Sieber - [georg-sieber.de][1]  
view source and fork me on [GitHub][2]

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to:
Free Software Foundation, Inc.
59 Temple Place - Suite 330
Boston, MA  02111-1307, USA.

[1]: [https://georg-sieber.de]
[2]: [https://github.com/schorschii/buzzsaw]

# Libraries
This program uses the [getid3()][3] library v1.9.14  
© 2017 James Heinrich  
Licensed under the terms of the GPLv2

[3]: [http://getid3.sourceforge.net]


# Support
Found a bug? Great! Please report it (preferably with a ready-to-use fix for it ;-) ) on GitHub. Questions, ideas and feature requests are also welcome.


# ToDo and planned features
Visit the GitHub page for more information.
