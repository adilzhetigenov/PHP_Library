# IK-Library - PHP Home Assignment
Daniel Jackson wrote down all the essential information during his visits to different worlds and collected many written memories. General Hammond and the General Staff decided to create a website to catalogue the books collected so far. They entrust you with this task.

![Screen Shot 2024-09-22 at 17 22 27](https://github.com/user-attachments/assets/eda8db6a-d1d3-478a-ad34-a9ca8af7e323)


## Main functions
Your task is to create a server-side application written in PHP where users can give feedback on books and mark them as read. The help section below describes the possible storage form of these two data structures (book and user), but of course, you can also structure your data differently.

Books must be added to the data in advance.

An admin user whose login data has been recorded must be included among the users. See details at Admin functions.

## Main page / List page

A title and a short description should be displayed with static text on the list page or the main page.

The main page is available to unidentified users, who can freely browse the content. The books in the system should be listed here.

The books should have a link (this can even be on a picture) that leads to the book's detailed page.

If the user is logged in, the "User profile" page should be accessible via a link.

## Book details

On the book details page, the title, author, description, cover image, year of publication, source planet, average rating and ratings should be displayed.

A rating button should be available on the books page if the user is logged in. The user can write a review for any book or mark them as read.

You can also access the main page and other menu items.

## User details

The page lists the user's data, the reviews written by him and the books he has read.

## Authentication pages

It should be possible to access the login and registration page from the main page.

You must enter your username, email address, and password twice during registration. All are required: the username must be unique, the email address format must be correct, and the passwords must match.

Error messages should be displayed in the event of a registration error! The form should be stateful! After successful registration, the user should be logged in to the main page.

During the login, we can identify with the username and password.

Report errors that occur during login on the form page! After successfully logging in, go to the main page!

## Admin functions

Create a special user, admin (username: admin, password: admin).

The admin user should be able to create a new book.

Create a static HTML prototype of the application to be developed! In the first step, design the list page, the page detailing the book, etc., using only HTML and CSS. There are pages where there is conditional information, plan these as well, and 

then remove them later with PHP. You can also test CSS statically, e.g. on the list page, how the books should be displayed, you can enter this statically. You can connect the individual pages with links.

Think about what data you will need. What must be stored, in which fields?

Where do you store users?

Where do you store the books?

How do you store the evaluations?

How do you connect users with the books they read?

How can you extract the correct data for your pages from the previously designed data structures? Create a function for each of these, but it is much better if you implement them as methods of the given Storage class.

How do you get a user's ratings back?

There are two ways to use forms:


What should happen in case of success?

How do I detect and display an error, and how does the form become stateful?

Data storage

Data: There are two types of data in the task: Book User.

## The following data of the books must be stored:

The book's title

The author of the book

The year the book was written

Description of the book (optional)

Image from the cover - this can be an image file path or image title (optional)

The name of the planet where the tear comes from (optional)

Starts with P, continues with two arbitrary characters, followed by a dash and 3 or 4 digits (see sample data above)

User data:

User name

User's e-mail address

User Password

User's last login

Admin authority
