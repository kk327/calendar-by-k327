## About
Calendar by k327 is a selfhostable calendar webapp that uses MySQL and PHP. Made using Vue.js.  
You can view an example calendar hosted by me [here](https://example-calendar.k327.eu), it has saving disabled and the account credentials are: username - exampleUser, password - P4ssw0rd

## Features
First, there's a login/registration screen, with registration requiring a registration token that you can create in the MySQL database outside of the webapp.  
There's a remember me option that uses localStorage, so you don't have to login everytime you visit the calendar if you don't want to.  
After you log in, you're greeted with the main screen - the calendar.  
At the top there's a header with a logout button in the left top corner, month/year navigation in the center at the top(you can also use arrow keys for it), and names of week days below it.  
Under the names of the weeks are the days themselves. The current day is marked with a red outline, and days that have notes added to them have a colored background.  
To add or edit a note, click on a day. Now, you can type the note from the center of your screen. In the left top corner there's a button to set the color of the note, and in the right top corner there's a button to go back to the main screen(you can also press the backspace key to do it).  
On the main screen, you can also hover your cursor over a day with a note to read the note without entering edit mode.  
The calendar webapp is responsive so it can be viewed on mobile devices. Of course keyboard controls and hovering your cursor won't work there.

## Setup
First, the config files. There are two of them:  
php/connectionConfig - You have to specify the details of the connection to your MySQL server in it.  
src/calendarConfig.json - You can change the first day of the week in it.  
Then you do the usual build process (for example: npm install, npm run build).  
Regarding the MySQL server, create a database and then create the following tables in it:  
  
CREATE TABLE registrationTokens(  
	token VARCHAR(255)  
);  
  
CREATE TABLE users(  
	id INT PRIMARY KEY AUTO_INCREMENT,  
    username VARCHAR(255) UNIQUE,  
    password BLOB  
);  
  
CREATE TABLE notes(  
	date DATE UNIQUE,  
    content BLOB,  
    color VARCHAR(7),  
    userId INT,  
    FOREIGN KEY (userId) REFERENCES users(id)  
);  
  
After you've done that, you can add a registration token that you'll use to register an account.  
Like that: INSERT INTO registrationTokens VALUES("yourtokenhere");  
You can then use it in the registration form on the webapp, you can create more registration tokens later on for more accounts if desired.